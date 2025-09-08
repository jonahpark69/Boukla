/* =====================================================================
   Boukla — cart.js (mini-panier modal / drawer)
   - Ouverture depuis n'importe quel bouton [data-cart-open]
   - Ajout produit via [data-add-to-cart] avec dataset: id, name, price, image
   - Persistance localStorage
   - Accessibilité: focus trap, aria, Esc, overlay click
   - IMPORTANT: l'ajout AU PANIER **n'ouvre JAMAIS** le mini-panier automatiquement.
                Pour ouvrir, il faut cliquer sur un bouton [data-cart-open].
   ===================================================================== */
(function(){
  'use strict';

  const STORAGE_KEY = 'boukla_cart_v1';

  // --------- Small utils ---------
  function escapeHtml(str){
    if (str == null) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;')
      .replace(/`/g, '&#96;');
  }

  // --------- State ---------
  let cart = loadCart();
  const currency = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' });

  // --------- DOM helpers ---------
  const $ = (sel, ctx=document) => ctx.querySelector(sel);
  const $$ = (sel, ctx=document) => Array.from(ctx.querySelectorAll(sel));

  // Inject modal HTML if not present
  function ensureModal(){
    if ($('#cart-modal')) return;
    const tpl = document.createElement('div');
    tpl.innerHTML = `
<div id="cart-modal" class="cmodal" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="cart-title">
  <div class="cmodal__overlay" data-cart-close></div>
  <aside class="cmodal__panel" tabindex="-1">
    <header class="cmodal__header">
      <h2 id="cart-title" class="cmodal__title">Panier</h2>
      <button class="cmodal__close" type="button" data-cart-close aria-label="Fermer le panier">×</button>
    </header>
    <div class="cmodal__list" id="cart-list"></div>
    <footer class="cmodal__footer">
      <div class="cart-total"><span>Total</span><strong id="cart-total">0,00 €</strong></div>
      <button class="btn-primary" type="button" id="cart-checkout">Passer au paiement</button>
      <button class="btn-ghost" type="button" data-cart-close>Continuer mes achats</button>
    </footer>
  </aside>
</div>`;
    document.body.appendChild(tpl.firstElementChild);
  }

  // --------- Render ---------
  function render(){
    // count badge
    const count = getCount();
    $$('[data-cart-count]').forEach(el => { el.textContent = String(count); });

    // list
    const list = $('#cart-list');
    if (!list) return;
    list.innerHTML = '';
    if (cart.items.length === 0){
      list.innerHTML = `<p style="color:var(--muted);padding:10px 6px;">Votre panier est vide.</p>`;
    } else {
      for (const it of cart.items){
        const row = document.createElement('div');
        row.className = 'cart-item';
        row.dataset.id = it.id;
        row.innerHTML = `
          <div class="cart-item__media"><img src="${escapeHtml(it.image||'')}" alt=""></div>
          <div>
            <p class="cart-item__title">${escapeHtml(it.name)}</p>
            <p class="cart-item__meta">
              <span class="cart-item__price">${currency.format(it.price)}</span>
            </p>
            <div class="qty" aria-label="Quantité">
              <button type="button" data-qty="dec" aria-label="Diminuer">−</button>
              <input type="text" inputmode="numeric" pattern="[0-9]*" value="${it.qty}" aria-label="Quantité">
              <button type="button" data-qty="inc" aria-label="Augmenter">+</button>
            </div>
          </div>
          <div class="cart-item__actions">
            <button type="button" class="cart-item__remove" data-remove>Retirer</button>
          </div>
        `;
        list.appendChild(row);
      }
    }

    // total
    const totalEl = $('#cart-total');
    if (totalEl) totalEl.textContent = currency.format(getTotal());
  }

  // --------- Open/Close ---------
  function openModal(){
    ensureModal();
    const modal = $('#cart-modal');
    if (!modal) return;
    modal.setAttribute('aria-hidden','false');
    document.documentElement.classList.add('no-scroll');
    document.body.classList.add('no-scroll');
    // for accessibility: focus the panel
    const panel = $('.cmodal__panel', modal);
    if (panel) panel.focus();
    trapFocus(modal);
    // reflect on header button (if any)
    $$('[data-cart-open]').forEach(b => b.setAttribute('aria-expanded','true'));
  }
  function closeModal(){
    const modal = $('#cart-modal');
    if (!modal) return;
    modal.setAttribute('aria-hidden','true');
    document.documentElement.classList.remove('no-scroll');
    document.body.classList.remove('no-scroll');
    releaseFocusTrap();
    $$('[data-cart-open]').forEach(b => b.setAttribute('aria-expanded','false'));
  }

  // --------- Storage ---------
  function loadCart(){
    try{
      const raw = localStorage.getItem(STORAGE_KEY);
      if (raw) return JSON.parse(raw);
    }catch(e){}
    return { items: [] };
  }
  function saveCart(){ localStorage.setItem(STORAGE_KEY, JSON.stringify(cart)); }

  // --------- Cart ops ---------
  function addItem({id, name, price, image}, qty=1){
    id = String(id);
    const existing = cart.items.find(i => i.id === id);
    if (existing) existing.qty += qty;
    else cart.items.push({ id, name, price: Number(price)||0, image: image||'', qty });
    saveCart(); render();
  }
  function removeItem(id){
    cart.items = cart.items.filter(i => i.id !== String(id));
    saveCart(); render();
  }
  function updateQty(id, qty){
    const it = cart.items.find(i => i.id === String(id));
    if (!it) return;
    it.qty = Math.max(1, Number(qty)||1);
    saveCart(); render();
  }
  function getTotal(){
    return cart.items.reduce((sum, i) => sum + i.price * i.qty, 0);
  }
  function getCount(){
    return cart.items.reduce((sum, i) => sum + i.qty, 0);
  }

  // --------- Events ---------
  // openers
  document.addEventListener('click', (e)=>{
    const btn = e.target.closest('[data-cart-open]');
    if (btn){ e.preventDefault(); openModal(); }
  });

  // add-to-cart (NE JAMAIS OUVRIR LE PANIER ICI)
  document.addEventListener('click', (e)=>{
    const btn = e.target.closest('[data-add-to-cart]');
    if (!btn) return;
    e.preventDefault();
    const id = btn.dataset.id || btn.getAttribute('data-product-id');
    const name = btn.dataset.name || btn.getAttribute('data-name') || 'Produit';
    const price = parseFloat(btn.dataset.price || btn.getAttribute('data-price') || '0');
    const image = btn.dataset.image || btn.getAttribute('data-image') || '';
    const qty = parseInt(btn.dataset.qty || btn.getAttribute('data-qty') || '1', 10);
    addItem({id, name, price, image}, qty);
    // feedback léger (sans ouvrir le panier)
    btn.classList.add('is-added');
    setTimeout(()=>btn.classList.remove('is-added'), 600);
  });

  // modal internal actions (delegation)
  document.addEventListener('click', (e)=>{
    const close = e.target.closest('[data-cart-close]');
    if (close){ e.preventDefault(); closeModal(); return; }

    const row = e.target.closest('.cart-item');
    if (!row) return;
    const id = row.dataset.id;
    if (e.target.matches('[data-remove]')){
      e.preventDefault(); removeItem(id);
      return;
    }
    if (e.target.matches('[data-qty="inc"]')){
      e.preventDefault();
      const it = cart.items.find(i => i.id === id);
      updateQty(id, (it?.qty||1)+1);
      return;
    }
    if (e.target.matches('[data-qty="dec"]')){
      e.preventDefault();
      const it = cart.items.find(i => i.id === id);
      updateQty(id, Math.max(1,(it?.qty||1)-1));
      return;
    }
  });

  // qty manual edit
  document.addEventListener('input', (e)=>{
    if (!e.target.closest('.qty')) return;
    const row = e.target.closest('.cart-item');
    if (!row) return;
    updateQty(row.dataset.id, e.target.value);
  });

  // esc to close
  document.addEventListener('keydown', (e)=>{
    if (e.key === 'Escape'){ closeModal(); }
  });

  // checkout (placeholder)
  document.addEventListener('click', (e)=>{
    if (e.target.id === 'cart-checkout'){
      e.preventDefault();
      alert('Redirection vers le checkout… (à brancher)');
    }
  });

  // --------- Focus trap ---------
  let lastFocusTrap = null;
  function trapFocus(root){
    releaseFocusTrap();
    const focusables = root.querySelectorAll('a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])');
    if (!focusables.length) return;
    const first = focusables[0];
    const last  = focusables[focusables.length-1];
    function handle(e){
      if (e.key !== 'Tab') return;
      if (e.shiftKey && document.activeElement === first){ e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last){ e.preventDefault(); first.focus(); }
    }
    lastFocusTrap = handle;
    root.addEventListener('keydown', handle);
  }
  function releaseFocusTrap(){
    const root = $('#cart-modal');
    if (root && lastFocusTrap){
      root.removeEventListener('keydown', lastFocusTrap);
      lastFocusTrap = null;
    }
  }

  // init
  ensureModal();
  render();
})();