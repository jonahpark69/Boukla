<?php
session_start();
$ref = $_GET['ref'] ?? ($_SESSION['last_order']['ref'] ?? null);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Boukla — Paiement annulé</title>
  <link rel="stylesheet" href="boukla.css">
</head>
<body>
  <main class="container" style="max-width:840px; padding:40px 0;">
    <p class="eyebrow">Paiement annulé</p>
    <h1 class="page-title">Opération interrompue</h1>
    <p class="subtitle">Votre commande n’a pas été finalisée.</p>

    <?php if ($ref): ?>
      <p>Référence concernée : <strong><?= htmlspecialchars($ref) ?></strong></p>
    <?php endif; ?>

    <div class="actions" style="margin-top:20px;">
      <a class="btn" href="checkout.php">Revenir au paiement</a>
      <a class="btn btn--accent" href="shop.html">Voir mon panier</a>
    </div>
  </main>
</body>
</html>
