<?php
session_start();
$order = $_SESSION['last_order'] ?? null;
$ref = $_GET['ref'] ?? ($order['ref'] ?? null);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Boukla — Merci</title>
  <link rel="stylesheet" href="boukla.css">
</head>
<body>
  <main class="container" style="max-width:840px; padding:40px 0;">
    <p class="eyebrow">Commande confirmée</p>
    <h1 class="page-title">Merci ✨</h1>
    <p class="subtitle">Votre paiement a bien été enregistré.</p>

    <section class="panel" style="margin-top:16px;">
      <h2>Référence de commande</h2>
      <p><strong><?= htmlspecialchars($ref ?: '—') ?></strong></p>

      <?php if ($order): ?>
        <div class="sep"></div>
        <h2>Récapitulatif</h2>
        <div class="checkout-lines">
          <?php
          function euro($c){ return number_format($c/100, 2, ',', ' ') . ' €'; }
          foreach($order['items'] as $it): ?>
            <div class="line"><span><?= htmlspecialchars($it['title'] ?? $it['name'] ?? '') ?> × <?= (int)$it['qty'] ?></span>
              <span><?= euro($it['price'] * $it['qty']) ?></span></div>
          <?php endforeach; ?>
          <div class="line"><span>Sous-total</span><span><?= euro($order['subtotal']) ?></span></div>
          <div class="line"><span>Livraison</span><span><?= $order['shipping'] ? euro($order['shipping']) : 'Offerte' ?></span></div>
          <div class="line total"><span>Total</span><span><?= euro($order['total']) ?></span></div>
        </div>

        <div class="sep"></div>
        <h2>Livraison</h2>
        <p>
          <?= htmlspecialchars($order['customer']['first'].' '.$order['customer']['last']) ?><br>
          <?= htmlspecialchars($order['shipping_address']['addr1']) ?><br>
          <?php if (!empty($order['shipping_address']['addr2'])): ?>
            <?= htmlspecialchars($order['shipping_address']['addr2']) ?><br>
          <?php endif; ?>
          <?= htmlspecialchars($order['shipping_address']['zip'].' '.$order['shipping_address']['city']) ?><br>
          <?= htmlspecialchars($order['shipping_address']['country']) ?>
        </p>
      <?php else: ?>
        <p>Nous avons bien reçu votre paiement.</p>
      <?php endif; ?>

      <div class="actions" style="margin-top:20px;">
        <a class="btn btn--accent" href="shop.html">Retour à la boutique</a>
      </div>
    </section>
  </main>
</body>
</html>
