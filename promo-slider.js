// promo-slider.js
// Carrousel minimal + auto-play

(function () {
  'use strict';

  const parseImages = (el) => {
    try {
      const arr = JSON.parse(el.dataset.images || '[]');
      return Array.isArray(arr) ? arr.filter(Boolean) : [];
    } catch {
      return [];
    }
  };

  const preload = (urls) => {
    urls.forEach((src) => {
      const im = new Image();
      im.src = src;
    });
  };

  const initPromo = (promoEl) => {
    const imgEl   = promoEl.querySelector('.promo__img');
    const nextBtn = promoEl.querySelector('.promo__next');

    if (!imgEl || !nextBtn) return;

    // Liste d’images
    let images = parseImages(promoEl);
    if (!images.length && imgEl.src) images = [imgEl.src];

    let index = 0;
    const currentSrc = imgEl.getAttribute('src');
    const found = images.findIndex((src) => currentSrc && currentSrc.endsWith(src));
    if (found >= 0) index = found;

    preload(images.slice(0, index).concat(images.slice(index + 1)));

    const show = (i) => {
      if (!images.length) return;
      index = (i + images.length) % images.length;
      imgEl.src = images[index];
    };

    // Navigation manuelle
    nextBtn.addEventListener('click', () => {
      show(index + 1);
      resetAutoPlay();
    });
    nextBtn.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        show(index + 1);
        resetAutoPlay();
      }
    });

    // --- Auto-play ---
    const intervalTime = 5000; // ms => 5000ms = 5 sec
    let autoPlayTimer = null;

    const startAutoPlay = () => {
      stopAutoPlay();
      autoPlayTimer = setInterval(() => {
        show(index + 1);
      }, intervalTime);
    };

    const stopAutoPlay = () => {
      if (autoPlayTimer) clearInterval(autoPlayTimer);
    };

    const resetAutoPlay = () => {
      startAutoPlay();
    };

    // Démarrage
    startAutoPlay();

    // Option: pause si la souris survole l'image
    promoEl.addEventListener('mouseenter', stopAutoPlay);
    promoEl.addEventListener('mouseleave', startAutoPlay);
  };

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.promo').forEach(initPromo);
  });
})();


