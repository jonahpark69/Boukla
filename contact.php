<?php
// contact.php — page Contact + handler POST (JSON) pour Boukla

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  header('Content-Type: application/json; charset=utf-8');

  // 1) Anti-spam (honeypot)
  if (!empty($_POST['website'])) {
    http_response_code(400);
    echo json_encode(['ok'=>false, 'error'=>'Spam détecté.']);
    exit;
  }

  // 2) Récupération + validation
  $name    = trim($_POST['name']    ?? '');
  $email   = trim($_POST['email']   ?? '');
  $message = trim($_POST['message'] ?? '');

  if ($name === '' || $email === '' || $message === '') {
    http_response_code(400);
    echo json_encode(['ok'=>false, 'error'=>'Merci de remplir tous les champs requis.']);
    exit;
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['ok'=>false, 'error'=>'Adresse e-mail invalide.']);
    exit;
  }

  // 3) Préparation email
  $TO       = 'contact@ton-domaine.com'; // ← remplace par ta boîte de réception
  $subject  = 'Demande via Contact';
  $SUBJECT  = '📩 Boukla — ' . $subject;

  // Sécurise les en-têtes (pas de retours chariot)
  $safeName  = str_replace(["\r","\n"], ' ', $name);
  $safeEmail = str_replace(["\r","\n"], ' ', $email);

  $BODY = "Nom : $safeName\nEmail : $safeEmail\nSujet : $subject\n\n$message\n";

  $HEADERS = [
    'From: Boukla <no-reply@boukla.fr>',
    'Reply-To: ' . $safeName . ' <' . $safeEmail . '>',
    'Content-Type: text/plain; charset=UTF-8',
  ];
  $headersStr = implode("\r\n", $HEADERS);

  // 4) Envoi (en local, mail() peut échouer)
  $sent = @mail($TO, '=?UTF-8?B?'.base64_encode($SUBJECT).'?=', $BODY, $headersStr);

  // 5) Log utile en toute circonstance
  $logLine = sprintf(
    "[%s] %s <%s>\n%s\n----\n",
    date('Y-m-d H:i:s'),
    $safeName,
    $safeEmail,
    $message
  );
  @file_put_contents(__DIR__ . '/contact.log', $logLine, FILE_APPEND);

  // 6) Réponse JSON
  echo json_encode($sent ? ['ok'=>true] : ['ok'=>true, 'note'=>'Email non envoyé (en local), message loggé dans contact.log']);
  exit; // important pour ne pas renvoyer l’HTML ensuite
}
?>
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
          <!-- Action = cette même page pour le handler JSON ci-dessus -->
          <form class="contact-form" id="contact-form"
                action="<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>"
                method="post" novalidate>
            <!-- Honeypot anti-spam -->
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
              <!-- Zone de statut pour contact.js -->
              <p class="form-status" role="status" aria-live="polite"></p>
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

  <script src="contact.js" defer></script>
</body>
</html>

