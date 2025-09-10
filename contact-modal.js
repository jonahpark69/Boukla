// ======================================================================
// Boukla — contact-modal.js
// Ouvre le modal depuis le lien nav, lock scroll, submit via fetch JSON.
// ======================================================================
(function(){
  'use strict';

  const trigger = document.querySelector('#nav-contact[data-modal-target]');
  const modal   = document.querySelector(trigger?.dataset.modalTarget || '#contact-modal');
  if(!trigger || !modal) return;

  const dialog   = modal.querySelector('.contact-modal__dialog');
  const closeEls = modal.querySelectorAll('[data-close-modal]');
  const form     = modal.querySelector('#contact-form');
  const feedback = modal.querySelector('#contact-feedback');
  const firstField = modal.querySelector('#c-name');

  function openModal(e){
    if(e) e.preventDefault();
    modal.setAttribute('aria-hidden','false');
    document.documentElement.classList.add('modal-open');
    document.body.classList.add('modal-open');
    setTimeout(()=> firstField?.focus(), 50);
    document.addEventListener('keydown', onKeydown);
  }
  function closeModal(){
    modal.setAttribute('aria-hidden','true');
    document.documentElement.classList.remove('modal-open');
    document.body.classList.remove('modal-open');
    document.removeEventListener('keydown', onKeydown);
  }
  function onKeydown(ev){
    if(ev.key === 'Escape') closeModal();
  }

  trigger.addEventListener('click', openModal);
  closeEls.forEach(el => el.addEventListener('click', closeModal));
  modal.addEventListener('click', (e)=>{ if(e.target === modal.querySelector('.contact-modal__backdrop')) closeModal(); });

  // Submit AJAX
  if(form){
    form.addEventListener('submit', async (e)=>{
      e.preventDefault();
      feedback.className = 'form-feedback';
      feedback.textContent = 'Envoi en cours…';

      const data = new FormData(form);
      data.set('ajax','1');

      try{
        const res = await fetch(form.action, { method:'POST', body: data });
        const json = await res.json();
        if(json?.success){
          feedback.classList.add('ok');
          feedback.textContent = 'Merci, votre message a bien été envoyé.';
          form.reset();
        }else{
          feedback.classList.add('err');
          feedback.textContent = json?.message || 'Impossible d’envoyer votre message.';
        }
      }catch(err){
        feedback.classList.add('err');
        feedback.textContent = 'Erreur réseau. Réessayez dans un instant.';
      }
    });
  }
})();
