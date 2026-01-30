/* ================================
   Boukla — Contact (form submit)
   ================================ */
(function(){
  const form = document.querySelector('.page-contact #contact-form');
  if(!form) return;

  const statusEl = form.querySelector('.form-status');
  const submitBtn = form.querySelector('button[type="submit"]');

  function setStatus(msg, ok = false){
    if(!statusEl) return;
    statusEl.textContent = msg || '';
    statusEl.style.color = ok ? '#1a1a1a' : '#6f6f6f';
  }

  form.addEventListener('submit', async (e) => {
    // Fallback POST classique si fetch indisponible
    if(!window.fetch) return;

    e.preventDefault();
    setStatus('Envoi en cours…');
    submitBtn.disabled = true;

    try{
      const formData = new FormData(form);
      const res = await fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: { 'Accept': 'application/json' }
      });

      const data = await res.json().catch(() => ({}));
      if(res.ok && data.ok){
        setStatus('Merci ! Votre message a bien été envoyé.', true);
        form.reset();
      }else{
        setStatus(data.error || 'Impossible d’envoyer le message. Réessayez ou écrivez-nous directement.');
      }
    }catch(err){
      setStatus('Problème réseau. Réessayez dans un instant.');
    }finally{
      submitBtn.disabled = false;
    }
  });
})();
