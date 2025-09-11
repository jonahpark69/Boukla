<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Boukla — Contact</title>
  <link rel="stylesheet" href="boukla.css">
  <link rel="stylesheet" href="contact.css">
</head>
<body>
  <!-- Réutilise ton header global ici -->

  <main>
    <div class="page-contact">
      <nav class="breadcrumb" aria-label="breadcrumbs">
        <a href="index.html">Home</a><span aria-hidden="true">/</span><span aria-current="page">Contact</span>
      </nav>

      <section class="hero-contact container">
        <p class="eyebrow">Échanger avec nous</p>
        <h1 class="page-title">Contact</h1>
        <p class="subtitle">Questions, conseils tailles, suivi de commande, presse, partenariats — on vous répond.</p>
      </section>

      <section class="container grid-2">
        <article class="panel">
          <h2>Écrivez-nous</h2>
          <form class="contact-form" action="contact-handler.php" method="post" novalidate>
            <input type="text" name="website" class="hp" tabindex="-1" aria-hidden="true" autocomplete="off">
            <div class="field">
              <label for="p-name">Nom</label>
              <input id="p-name" name="name" type="text" required maxlength="100" autocomplete="name">
            </div>
            <div class="field">
              <label for="p-email">E-mail</label>
              <input id="p-email" name="email" type="email" required maxlength="160" autocomplete="email">
            </div>
            <div class="field">
              <label for="p-msg">Message</label>
              <textarea id="p-msg" name="message" rows="6" required maxlength="2000"></textarea>
            </div>
            <div class="actions">
              <button type="submit">Envoyer</button>
            </div>
            <p class="form-note">Nous répondons sous 24–48h ouvrées.</p>
          </form>
        </article>

        <aside class="panel">
          <h2>Coordonnées</h2>
          <ul class="bullets">
            <li><strong>E-mail :</strong> contact@ton-domaine.com</li>
            <li><strong>Studio :</strong> 12 rue de l’Atelier, 75000 Paris</li>
            <li><strong>Horaires :</strong> Lun–Ven, 10h–18h</li>
          </ul>
          <div class="map-placeholder" role="img" aria-label="Emplacement du studio Boukla (carte à venir)"></div>
        </aside>
      </section>
    </div>
  </main>

  <!-- Footer -->
</body>
</html>
