/* ===== search.js — recherche durable vers la fiche produit ===== */
(() => {
  const WIRE_FLAG = 'data-search-wired';

  /* ---------- Utils ---------- */
  const norm = s => (s||'')
    .toLowerCase()
    .normalize('NFD').replace(/\p{Diacritic}/gu,'')
    .replace(/[^a-z0-9]+/g,' ')
    .trim();

  const uniqBy = (arr, keyFn) => {
    const seen = new Set();
    return arr.filter(x => {
      const k = keyFn(x);
      if (seen.has(k)) return false;
      seen.add(k);
      return true;
    });
  };

  function parseSlugFromHref(href){
    if(!href) return null;
    // 1) product.php?slug=xxx
    try {
      const u = new URL(href, location.origin);
      const s = u.searchParams.get('slug');
      if (s) return decodeURIComponent(s);
      // 2) /produit/xxx
      const m = u.pathname.match(/(?:^|\/)produit\/([^\/?#]+)/i);
      if (m) return decodeURIComponent(m[1]);
    } catch(e){
      // href relative sans base ? On tente en brut
      const m = String(href).match(/(?:^|\/)produit\/([^\/?#]+)/i);
      if (m) return decodeURIComponent(m[1]);
    }
    return null;
  }

  /* ---------- Index produits ---------- */
  let PRODUCTS_CACHE = null;

  // A) Via products.json (prioritaire si présent)
  async function loadFromJson(){
    try{
      // En file:// fetch est bloqué, on laissera tomber
      if (location.protocol === 'file:') return [];
      const res = await fetch('products.json', { credentials:'same-origin', cache:'no-store' });
      if (!res.ok) return [];
      const list = await res.json();
      if (!Array.isArray(list)) return [];
      return list.map(p => ({
        slug: String(p.slug || '').trim(),
        title: String(p.name || p.title || '').trim(),
        keywords: [
          ...(String(p.short||'').split(/\s+/)),
          ...(String(p.description||'').split(/\s+/))
        ],
        // Toujours pousser vers la fiche PHP
        href: `product.php?slug=${encodeURIComponent(p.slug)}`
      })).filter(x => x.slug && x.title);
    }catch(e){ return []; }
  }

  // B) Scrape shop.html (fallback si pas de JSON)
  async function loadFromShop(){
    try{
      if (location.protocol === 'file:') return scrapeGrid(document); // si ouvert localement et qu’on est sur shop
      const res = await fetch('shop.html', { credentials:'same-origin' });
      if(!res.ok) return [];
      const html = await res.text();
      const doc  = new DOMParser().parseFromString(html, 'text/html');
      return scrapeGrid(doc);
    }catch(e){ return []; }
  }

  // C) Scrape une grille déjà présente dans la page courante
  function scrapeGrid(root){
    const out = [];
    root.querySelectorAll('.product-grid .card').forEach(card=>{
      const link = card.querySelector('.card__media[href], .card__title a[href], a[href*="product.php?slug="], a[href*="/produit/"]');
      if(!link) return;
      const rawHref = link.getAttribute('href');
      const slug    = parseSlugFromHref(rawHref);
      if(!slug) return;

      const titleEl = card.querySelector('.card__title, h3, h2, figcaption, a[title]');
      const title   = (titleEl?.textContent || link.getAttribute('title') || link.textContent || '').trim();
      const tags    = (card.getAttribute('data-tags') || '').trim();
      const keywords = (tags ? tags.split(/[,\s]+/) : title.split(/\s+/)).filter(Boolean);

      out.push({
        slug,
        title,
        keywords,
        href: `product.php?slug=${encodeURIComponent(slug)}`
      });
    });
    return uniqBy(out, x => x.slug);
  }

  async function discoverProducts(){
    if (PRODUCTS_CACHE) return PRODUCTS_CACHE;

    // 1) JSON
    let items = await loadFromJson();
    if (items.length) return (PRODUCTS_CACHE = items);

    // 2) shop.html
    items = await loadFromShop();
    if (items.length) return (PRODUCTS_CACHE = items);

    // 3) grille présente (ultime recours)
    items = scrapeGrid(document);
    return (PRODUCTS_CACHE = items);
  }

  /* ---------- Scoring ---------- */
  function searchProducts(products, q){
    const nq = norm(q);
    if(!nq) return [];
    const terms = nq.split(' ').filter(Boolean);

    return products.map(p=>{
      const hay = norm(p.title + ' ' + (p.keywords||[]).join(' ') + ' ' + p.slug);
      let score = 0;

      // boosts exacts
      if (norm(p.title) === nq) score += 8;
      if (norm(p.slug)  === nq) score += 6;

      // mots présents
      for(const t of terms){
        if((' ' + hay + ' ').includes(' ' + t + ' ')) score += 3; // mot entier
        else if(hay.includes(t)) score += 1;                      // sous-chaîne
      }
      return { ...p, score };
    })
    .filter(r => r.score > 0)
    .sort((a,b) => b.score - a.score);
  }

  /* ---------- Modal "Aucun résultat" (optionnel) ---------- */
  function showNotFound(q){
    const modal = document.getElementById('search-not-found');
    if(!modal){ alert(`Aucun résultat pour « ${q} »`); return; }
    const qEl = modal.querySelector('#snf-q');
    if(qEl) qEl.textContent = q || '';
    modal.setAttribute('aria-hidden','false');
    document.documentElement.classList.add('no-scroll');
    document.body.classList.add('no-scroll');

    if(!modal.hasAttribute(WIRE_FLAG)){
      modal.setAttribute(WIRE_FLAG, '1');
      const closeEls = modal.querySelectorAll('[data-snf-close], .cmodal__overlay');
      const hide = () => {
        modal.setAttribute('aria-hidden','true');
        document.documentElement.classList.remove('no-scroll');
        document.body.classList.remove('no-scroll');
      };
      closeEls.forEach(el => el.addEventListener('click', hide));
      document.addEventListener('keydown', (e)=>{ if(e.key === 'Escape') hide(); });
    }
  }

  /* ---------- HOME : UI + submit ---------- */
  document.addEventListener('DOMContentLoaded', ()=>{
    const form   = document.getElementById('home-search');
    const toggle = document.querySelector('.js-search-toggle');
    if(!form || !toggle) return;

    if(form.hasAttribute(WIRE_FLAG)) return;
    form.setAttribute(WIRE_FLAG, '1');

    const input  = form.querySelector('input[name="q"]');
    const submit = form.querySelector('button[type="submit"]');

    // Popover open/close
    function openPopover(){
      form.hidden = false;
      form.classList.add('is-open');
      toggle.setAttribute('aria-expanded','true');
      setTimeout(()=> input && input.focus(), 0);
    }
    function closePopover(){
      form.classList.remove('is-open');
      toggle.setAttribute('aria-expanded','false');
      form.hidden = true;
    }

    toggle.addEventListener('click', (e)=>{
      e.preventDefault();
      form.hidden ? openPopover() : closePopover();
    });

    document.addEventListener('click', (e)=>{
      if(!form.contains(e.target) && !toggle.contains(e.target)) closePopover();
    });
    document.addEventListener('keydown', (e)=>{
      if(e.key === 'Escape') closePopover();
    });

    form.addEventListener('submit', async (e)=>{
      e.preventDefault();
      const q = (input?.value || '').trim();
      if(!q){
        input?.focus();
        input?.setAttribute('aria-invalid','true');
        setTimeout(()=> input?.removeAttribute('aria-invalid'), 600);
        return;
      }
      const oldTxt = submit?.textContent;
      if(submit){ submit.disabled = true; submit.textContent = '…'; }

      try{
        const products = await discoverProducts();
        const results  = searchProducts(products, q);

        if(results.length === 0){
          showNotFound(q);
          closePopover();
          return;
        }

        // Redirection DIRECTE vers la meilleure fiche produit
        window.location.href = results[0].href;
      } finally {
        if(submit){ submit.disabled = false; submit.textContent = oldTxt; }
      }
    });
  });

  /* ---------- SHOP (optionnel) : filtrer par ?q= si tu veux garder cette feature ---------- */
  async function filterShopFromQuery(){
    const grid = document.querySelector('.product-grid');
    if(!grid) return;

    const params = new URLSearchParams(location.search);
    const q = params.get('q') || '';
    if(!q){
      grid.querySelectorAll('.card').forEach(card => card.style.display = '');
      return;
    }

    const products = await discoverProducts();
    const results  = searchProducts(products, q);
    const allowed  = new Set(results.map(r => r.slug));
    let shown = 0;

    grid.querySelectorAll('.card').forEach(card=>{
      const link = card.querySelector('.card__media[href], a[href]');
      const slug = parseSlugFromHref(link?.getAttribute('href') || '');
      if(slug && allowed.has(slug)){
        card.style.display = '';
        shown++;
      } else {
        card.style.display = 'none';
      }
    });

    if(shown === 0) showNotFound(q);
  }
  document.addEventListener('DOMContentLoaded', filterShopFromQuery);
  window.addEventListener('popstate', filterShopFromQuery);
})();


