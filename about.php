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
       Exemple minimal si besoin :
  <header class="header">
    <nav class="nav">
      <a href="index.php">HOME</a>
      <a href="shop.php">SHOP</a>
      <a class="is-active" href="about.php">ABOUT US</a>
      <a class="login" href="#">LOGIN</a>
    </nav>
  </header>
  -->
  
  <main>
    <div class="page-about">

      <!-- Fil d’Ariane -->
      <nav class="breadcrumb" aria-label="breadcrumbs">
        <a href="index.php">Home</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">About Us</span>
      </nav>

      <!-- Hero -->
      <section class="hero-about container">
        <p class="eyebrow">Depuis 2018</p>
        <h1 class="page-title">Boukla</h1>
        <p class="subtitle">
          Une maison créative dédiée aux pièces intemporelles, pensées pour durer et se transmettre.
        </p>
      </section>

      <!-- Story + Chiffres -->
      <section class="container grid-2">
        <article class="panel panel--story">
          <h2>Notre histoire</h2>
          <p>
            Boukla est née avec une idée simple&nbsp;: créer des pièces qui ont du sens, au-delà des saisons.
            Dans notre atelier, chaque détail compte&nbsp;— de la sélection des matières à la finition à la main.
          </p>
          <p>
            Nous produisons en petites séries, privilégions des partenaires responsables, et favorisons un
            design sobre qui traverse le temps. Nos collections combinent fonctionnalité, confort et caractère.
          </p>
          <div class="timeline">
            <div class="tl-item">
              <div class="tl-dot" aria-hidden="true"></div>
              <div class="tl-content">
                <h3>2018 — Les débuts</h3>
                <p>Lancement des premières pièces en mini-séries dans un atelier partagé.</p>
              </div>
            </div>
            <div class="tl-item">
              <div class="tl-dot" aria-hidden="true"></div>
              <div class="tl-content">
                <h3>2021 — Atelier dédié</h3>
                <p>Ouverture de notre propre espace de production, montée en exigence sur les matières.</p>
              </div>
            </div>
            <div class="tl-item">
              <div class="tl-dot" aria-hidden="true"></div>
              <div class="tl-content">
                <h3>2024 — Capsule éthique</h3>
                <p>Première capsule 100% matières certifiées, traçabilité renforcée sur la chaîne de valeur.</p>
              </div>
            </div>
          </div>
        </article>

        <aside class="panel panel--figures">
          <h2>Nos engagements</h2>
          <ul class="bullets">
            <li><strong>Qualité durable</strong> — finitions soignées, contrôle à chaque étape.</li>
            <li><strong>Matières responsables</strong> — tissus et composants sourcés avec exigence.</li>
            <li><strong>Production raisonnée</strong> — petites séries, pas de sur-stock.</li>
            <li><strong>Réparabilité</strong> — service d’entretien et de réparation sur sélection.</li>
          </ul>

          <div class="kpis">
            <div class="kpi">
              <span class="kpi__value">+50</span>
              <span class="kpi__label">modèles conçus</span>
            </div>
            <div class="kpi">
              <span class="kpi__value">12</span>
              <span class="kpi__label">partenaires ateliers</span>
            </div>
            <div class="kpi">
              <span class="kpi__value">0</span>
              <span class="kpi__label">sur-production</span>
            </div>
          </div>
        </aside>
      </section>

      <!-- Équipe -->
      <section class="container team">
        <h2>Équipe</h2>
        <div class="team-grid">
          <article class="team-card">
            <div class="avatar" aria-hidden="true"></div>
            <h3>Direction Créative</h3>
            <p>Conception, choix des matières, pilotage des capsules.</p>
          </article>
          <article class="team-card">
            <div class="avatar" aria-hidden="true"></div>
            <h3>Atelier &amp; Production</h3>
            <p>Patronage, prototypage, montage, contrôle qualité.</p>
          </article>
          <article class="team-card">
            <div class="avatar" aria-hidden="true"></div>
            <h3>Relation Clients</h3>
            <p>Conseil, suivi des commandes, réparations et retouches.</p>
          </article>
        </div>
      </section>

      <!-- CTA -->
      <section class="container cta">
        <div class="cta__inner">
          <h2>Rejoindre la newsletter</h2>
          <p>En avant-première&nbsp;: sorties de capsules, coulisses d’atelier, disponibilités.</p>
          <form class="cta__form" action="#" method="post">
            <label class="sr-only" for="about-email">Votre e-mail</label>
            <input id="about-email" name="email" type="email" required placeholder="votre@email.com"/>
            <button type="submit">S’inscrire</button>
          </form>
        </div>
      </section>

    </div>
  </main>

  <!-- Optionnel : ton footer habituel -->

  <!-- (Si tu as un JS global, tu peux l’inclure ici) -->
</body>
</html>
