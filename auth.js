// auth.js (racine du projet)
(function(){
  const openBtn = document.querySelector('.js-auth-open');
  const modal   = document.getElementById('auth-modal');
  if(!openBtn || !modal) return;

  const msgEl = modal.querySelector('.auth-msg');
  const tabs  = modal.querySelectorAll('.js-auth-tab');
  const views = modal.querySelectorAll('.auth-view');
  const USE_MOCK = true; // mets false quand tu brancheras une vraie API

  function openAuth(view='login'){
    switchView(view);
    if(typeof modal.showModal === 'function') modal.showModal();
    else modal.setAttribute('open','');
    openBtn.setAttribute('aria-expanded','true');
    setTimeout(()=> modal.querySelector(`.auth-view[data-view="${view}"] input`)?.focus(), 0);
  }
  function closeAuth(){
    if(typeof modal.close === 'function') modal.close();
    else modal.removeAttribute('open');
    openBtn.setAttribute('aria-expanded','false');
  }
  function switchView(view){
    views.forEach(v => v.hidden = (v.dataset.view !== view));
    tabs.forEach(t => t.setAttribute('aria-selected', String(t.dataset.view === view)));
    hideMsg();
  }
  function showMsg(t, ok=false){ msgEl.hidden=false; msgEl.textContent=t; msgEl.style.color = ok ? '#1a7f37' : '#b42318'; }
  function hideMsg(){ msgEl.hidden=true; msgEl.textContent=''; }

  openBtn.addEventListener('click', (e)=>{ e.preventDefault(); openAuth('login'); });
  modal.addEventListener('click', (e)=>{ if(!e.target.closest('.auth-modal__box')) closeAuth(); });
  modal.querySelector('.auth-modal__close').addEventListener('click', ()=> closeAuth());
  tabs.forEach(tab => tab.addEventListener('click', ()=> switchView(tab.dataset.view)));
  modal.querySelectorAll('.js-auth-switch').forEach(b => b.addEventListener('click', ()=> switchView(b.dataset.view)));
  modal.querySelectorAll('.auth-toggle-pass').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const inp = btn.previousElementSibling;
      if(inp) inp.type = (inp.type === 'password' ? 'text' : 'password');
    });
  });

  modal.addEventListener('click', async (e)=>{
    const btn = e.target.closest('.js-auth-submit'); if(!btn) return;
    e.preventDefault(); hideMsg();
    btn.disabled = true; const old = btn.textContent; btn.textContent = '…';
    try{
      if(btn.dataset.action === 'login'){
        const email = modal.querySelector('#login-email')?.value.trim();
        const pass  = modal.querySelector('#login-pass')?.value;
        if(!email || !pass){ showMsg('Renseigne email et mot de passe.'); return; }

        if(USE_MOCK){
          if(pass.length < 6){ showMsg('Mot de passe incorrect.'); return; }
          localStorage.setItem('bouklaUser', JSON.stringify({ email }));
          showMsg('Connexion réussie !', true);
          setTimeout(closeAuth, 600);
        }else{
          const res = await fetch('api/login.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({email,password:pass})});
          if(!res.ok){ showMsg('Identifiants invalides.'); return; }
          const data = await res.json();
          localStorage.setItem('bouklaUser', JSON.stringify(data.user || { email }));
          showMsg('Connexion réussie !', true);
          setTimeout(()=>location.reload(), 500);
        }
      }else{
        const email = modal.querySelector('#su-email')?.value.trim();
        const p1    = modal.querySelector('#su-pass')?.value;
        const p2    = modal.querySelector('#su-pass2')?.value;
        const cgu   = modal.querySelector('#su-cgu')?.checked;
        if(!email || !p1 || !p2){ showMsg('Complète tous les champs obligatoires.'); return; }
        if(p1.length < 6){ showMsg('Mot de passe trop court.'); return; }
        if(p1 !== p2){ showMsg('Les mots de passe ne correspondent pas.'); return; }
        if(!cgu){ showMsg('Tu dois accepter les CGU.'); return; }

        if(USE_MOCK){
          localStorage.setItem('bouklaUser', JSON.stringify({ email }));
          showMsg('Compte créé, bienvenue !', true);
          setTimeout(closeAuth, 600);
        }else{
          const res = await fetch('api/register.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({email,password:p1})});
          if(!res.ok){ showMsg('Inscription refusée.'); return; }
          const data = await res.json();
          localStorage.setItem('bouklaUser', JSON.stringify(data.user || { email }));
          showMsg('Compte créé, bienvenue !', true);
          setTimeout(()=>location.reload(), 500);
        }
      }
    } finally {
      btn.disabled = false; btn.textContent = old;
    }
  });

  // (optionnel) fermer avec Echap
  document.addEventListener('keydown', (e)=>{ if(e.key==='Escape' && (modal.open || modal.hasAttribute('open'))) closeAuth(); });
})();
