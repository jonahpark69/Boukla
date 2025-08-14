/* promo-slider.js — init image 0 au chargement + fondu + autoplay (boucle avant) */
(function () {
  'use strict';

  // Parse robuste de data-images (gère quotes simples, espaces, etc.)
  function parseImagesAttr(el) {
    const raw = el.getAttribute('data-images');
    if (!raw) return [];
    try {
      const cleaned = raw
        .replace(/'/g, '"')          // quotes simples -> doubles
        .replace(/,\s*]/g, ']')      // trailing commas
        .replace(/,\s*}/g, '}');
      const arr = JSON.parse(cleaned);
      return Array.isArray(arr) ? arr.filter(Boolean) : [];
    } catch {
      return [];
    }
  }

  // Précharge un ensemble d'URLs
  function preload(urls) {
    urls.forEach((src) => {
      const im = new Image();
      im.src = src;
    });
  }

  function makeSlider(promoEl) {
    const imgEl   = promoEl.querySelector('.promo__img');
    const nextBtn = promoEl.querySelector('.promo__next');
    if (!imgEl || !nextBtn) return;

    // 1) Images depuis data-images, sinon tombe sur le src actuel
    let images = parseImagesAttr(promoEl);
    if (!images.length && imgEl.getAttribute('src')) {
      images = [imgEl.getAttribute('src')];
    }
    if (!images.length) return; // rien à afficher

    // 2) Index courant : si le src actuel est dans la liste, on s’y cale, sinon 0
    const currentSrc = imgEl.getAttribute('src') || '';
    let index = Math.max(
      0,
      images.findIndex((src) => currentSrc.endsWith(src))
    );
    if (index === -1) index = 0;

    // 3) Fonction d’affichage avec fondu + préchargement
    let isFading = false;
    function show(i) {
      if (isFading || !images.length) return;
      index = (i + images.length) % images.length;

      const target = images[index];
      if (!target) return;

      // Si même image, on ne fait rien
      if ((imgEl.getAttribute('src') || '') === target) return;

      isFading = true;
      imgEl.style.opacity = '0';

      const tmp = new Image();
      tmp.onload = () => {
        imgEl.src = target;
        requestAnimationFrame(() => {
          imgEl.style.opacity = '1';
          isFading = false;
        });
      };
      tmp.onerror = () => {
        // En cas d'erreur, on rétablit l’opacité pour éviter un écran vide
        imgEl.style.opacity = '1';
        isFading = false;
      };
      tmp.src = target;

      // Précharger l’image suivante
      const nextIdx = (index + 1) % images.length;
      const pre = new Image();
      pre.src = images[nextIdx];
    }

    function next() {
      show(index + 1);
    }

    // 4) Initialisation immédiate : forcer l’image 0 si le src actuel est cassé/différent
    //    -> garantit qu’on n’a pas d’écran vide au premier paint
    (function initFirstFrame() {
      const want = images[index]; // index vaut soit le match, soit 0
      if (!currentSrc || !currentSrc.endsWith(want)) {
        // pose immédiatement la bonne image sans fondu
        imgEl.style.opacity = '0';
        const tmp = new Image();
        tmp.onload = () => {
          imgEl.src = want;
          requestAnimationFrame(() => {
            imgEl.style.opacity = '1';
          });
        };
        tmp.onerror = () => { imgEl.style.opacity = '1'; };
        tmp.src = want;
      }
      // Précharge le reste
      const others = images.filter((_, i) => i !== index);
      preload(others);
    })();

    // 5) Interactions
    nextBtn.addEventListener('click', () => { next(); resetAutoPlay(); });
    nextBtn.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        next();
        resetAutoPlay();
      }
    });

    // 6) Auto-play (pause au survol)
    const intervalTime = 5000; // 5s
    let timer = null;
    function start() {
      stop();
      timer = setInterval(next, intervalTime);
    }
    function stop() {
      if (timer) clearInterval(timer);
      timer = null;
    }
    function resetAutoPlay() { start(); }

    start();
    promoEl.addEventListener('mouseenter', stop);
    promoEl.addEventListener('mouseleave', start);
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.promo').forEach(makeSlider);
  });
})();



