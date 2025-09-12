<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Boukla — About Us</title>

  <!-- Styles globaux -->
  <link rel="stylesheet" href="boukla.css">
  <link rel="stylesheet" href="about.css">
  
</head>
<body>

  <!-- === HEADER (reprends celui de ta Home tel quel) ===
       Garde les mêmes classes et structure. Seul le lien About doit viser about.php.
  -->

  <main>
    <div class="page-about">

      <!-- Fil d’Ariane -->
      <nav class="breadcrumb" aria-label="breadcrumbs">
        <a href="index.html">Home</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">About Us</span>
      </nav>

      <!-- Hero -->
      <section class="hero-about container">
        <p class="eyebrow">Depuis 2018</p>
        <h1 class="page-title">Boukla</h1>
        <p class="subtitle">
          Marque de soins capillaires dédiée aux boucles et aux cheveux crépus&nbsp;: des formules propres,
          efficaces et sensorielles pour révéler la beauté naturelle des textures 2A à 4C.
        </p>
      </section>

      <!-- Story + Chiffres -->
      <section class="container grid-2">
        <article class="panel panel--story">
          <h2>Notre histoire</h2>
          <p>
            Boukla est née d’un constat simple&nbsp;: trop de routines standardisées ne respectent pas les besoins
            réels des cheveux bouclés et crépus. Nous avons choisi une autre voie&nbsp;: des formules ciblées,
            testées sur des textures variées, qui hydratent, définissent et protègent sans alourdir.
          </p>
          <p>
            Chaque soin est pensé pour s’intégrer facilement à votre rituel&nbsp;: lavage doux, nutrition profonde,
            définition longue tenue et protection quotidienne. Des actifs botaniques soigneusement choisis, un pH
            respectueux du cuir chevelu, et des textures qui fondent dans le cheveu sans effet carton.
          </p>

          <div class="timeline">
            <div class="tl-item">
              <div class="tl-dot" aria-hidden="true"></div>
              <div class="tl-content">
                <h3>2018 — Les débuts</h3>
                <p>Premières recettes clean dans notre atelier, testées par une communauté de boucles et d’afros.</p>
              </div>
            </div>
            <div class="tl-item">
              <div class="tl-dot" aria-hidden="true"></div>
              <div class="tl-content">
                <h3>2021 — Laboratoire partenaire</h3>
                <p>Protocole de tests renforcé, itérations sur l’hydratation, la définition et le contrôle des frisottis.</p>
              </div>
            </div>
            <div class="tl-item">
              <div class="tl-dot" aria-hidden="true"></div>
              <div class="tl-content">
                <h3>2024 — Ligne éco-conçue</h3>
                <p>Packaging recyclable, traçabilité accrue des ingrédients, optimisation des rendements pour limiter l’empreinte.</p>
              </div>
            </div>
          </div>
        </article>

        <aside class="panel panel--figures">
          <h2>Nos engagements</h2>
          <ul class="bullets">
            <li><strong>Formules clean</strong> — sans sulfates agressifs, sans silicones lourds, sans parabènes.</li>
            <li><strong>Actifs botaniques</strong> — aloe vera, beurre de karité, huile de ricin &amp; co. sélectionnés avec soin.</li>
            <li><strong>Respect du cuir chevelu</strong> — pH équilibré, routines <em>curly-friendly</em> faciles à suivre.</li>
            <li><strong>Traçabilité &amp; responsabilité</strong> — partenaires engagés, emballages recyclables.</li>
          </ul>

          <div class="kpis">
            <div class="kpi">
              <span class="kpi__value">+50</span>
              <span class="kpi__label">formules testées</span>
            </div>
            <div class="kpi">
              <span class="kpi__value">12</span>
              <span class="kpi__label">partenaires labo &amp; ateliers</span>
            </div>
            <div class="kpi">
              <span class="kpi__value">0</span>
              <span class="kpi__label">silicones/sulfates agressifs</span>
            </div>
          </div>
        </aside>
      </section>

      <!-- Équipe -->
      <section class="container team">
        <h2>Équipe</h2>
        <div class="team-grid">
          <article class="team-card">
            <div class="avatar" aria-hidden="true"
                 style="background-image:url('assets/images/img-about-3.jpeg');"></div>
            <h3>R&amp;D &amp; Formulation</h3>
            <p>Sélection d’actifs, validation des prototypes, protocoles de tests sur textures 2A&nbsp;→&nbsp;4C.</p>
          </article>
          <article class="team-card">
            <div class="avatar" aria-hidden="true"
                 style="background-image:url('assets/images/img-about-1.jpeg');"></div>
            <h3>Laboratoire &amp; Production</h3>
            <p>Pesées, batchs pilotes, contrôles qualité et conditionnement dans le respect des bonnes pratiques.</p>
          </article>
          <article class="team-card">
            <div class="avatar" aria-hidden="true"
                 style="background-image:url('assets/images/img-about-2.jpeg');"></div>
            <h3>Conseil &amp; Communauté</h3>
            <p>Diagnostics boucles, routines personnalisées et retours d’expérience pour améliorer nos soins.</p>
          </article>
        </div>
      </section>

      <!-- CTA -->
      <section class="container cta">
        <div class="cta__inner">
          <h2>Rejoindre la newsletter</h2>
          <p>Conseils boucles, routines complètes, lancements et coulisses — en avant-première.</p>
          <form class="cta__form" action="#" method="post">
            <label class="sr-only" for="about-email">Votre e-mail</label>
            <input id="about-email" name="email" type="email" required placeholder="votre@email.com"/>
            <button type="submit">S’inscrire</button>
          </form>
        </div>
      </section>

    </div>
  </main>

<!-- ============== FOOTER ============== -->
<footer class="site-footer" role="contentinfo">
  <div class="container footer-grid">
    <!-- Marque -->
    <section class="footer-brand">
      <a href="index.php" class="footer-logo" aria-label="Boukla">
        <img src="assets/images/logo-boukla-2.svg" alt="Boukla" width="132" height="32" loading="lazy">
      </a>
      <p class="footer-tagline">Soins clean pour boucles &amp; cheveux crépus — hydrater, définir, protéger.</p>
      <ul class="footer-social">
        <li>
          <a href="https://www.instagram.com/boukla" target="_blank" rel="noopener noreferrer" aria-label="Instagram" title="Instagram">
            <img src="assets/icons/instagram.svg" alt="" width="18" height="18" loading="lazy">
          </a>
        </li>
        <li>
          <a href="https://www.pinterest.com/boukla" target="_blank" rel="noopener noreferrer" aria-label="Pinterest" title="Pinterest">
            <img src="assets/icons/pinterest.svg" alt="" width="18" height="18" loading="lazy">
          </a>
        </li>
        <li>
          <a href="https://x.com/boukla" target="_blank" rel="noopener noreferrer" aria-label="X (Twitter)" title="X (Twitter)">
            <img src="assets/icons/x.svg" alt="" width="18" height="18" loading="lazy">
          </a>
        </li>
      </ul>
    </section>

    <!-- Liens -->
    <nav class="footer-links" aria-label="Navigation pied de page">
      <div class="footer-col">
        <h3 class="footer-title">Boutique</h3>
        <ul>
          <li><a href="shop.php">Shop</a></li>
          <li><a href="new.php">Nouveautés</a></li>
          <li><a href="best-sellers.php">Best-sellers</a></li>
          <li><a href="collections.php">Collections</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h3 class="footer-title">Aide</h3>
        <ul>
          <li><a href="contact.php">Contact</a></li>
          <li><a href="faq.php">FAQ</a></li>
          <li><a href="shipping.php">Livraison &amp; retours</a></li>
          <li><a href="care-guide.php">Guide boucles</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h3 class="footer-title">Légal</h3>
        <ul>
          <li><a href="privacy.php">Confidentialité</a></li>
          <li><a href="terms.php">CGV</a></li>
          <li><a href="cookies.php">Cookies</a></li>
        </ul>
      </div>
    </nav>

    <!-- Newsletter -->
    <section class="footer-newsletter">
      <h3 class="footer-title">Newsletter</h3>
      <p class="footer-note">Conseils boucles, routines &amp; lancements en avant-première.</p>
      <form class="footer-form" action="#" method="post">
        <label class="sr-only" for="nl-email">Votre e-mail</label>
        <input id="nl-email" name="email" type="email" required placeholder="vous@email.com">
        <button type="submit">S’inscrire</button>
      </form>
    </section>

    <!-- Contact -->
    <section class="footer-contact">
      <h3 class="footer-title">Coordonnées</h3>
      <ul class="footer-contact-list">
        <li><a href="mailto:hello@boukla.fr">hello@boukla.fr</a></li>
        <li>12 Rue des Artisans, 75000 Paris</li>
        <li>Lun–Ven&nbsp;: 10h–19h</li>
      </ul>
    </section>
  </div>

  <div class="footer-bottom">
    <div class="container footer-bottom__inner">
      <p class="footer-copy">© 2025 Boukla. Tous droits réservés.</p>
      <p class="footer-made">Fabriqué avec soin — Paris.</p>
    </div>
  </div>
</footer>
<!-- ============ /FOOTER ============ -->

  <!-- (Si tu as un JS global, tu peux l’inclure ici) -->
</body>
</html>


