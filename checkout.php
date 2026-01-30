<?php session_start(); ?>
<!-- checkout.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Boukla — Paiement</title>
  <link rel="stylesheet" href="boukla.css">
  <link rel="stylesheet" href="checkout.css">
</head>
<body>

  <main>
    <div class="page-checkout">
      <nav class="breadcrumb" aria-label="breadcrumbs">
        <a href="index.html">Home</a>
        <span aria-hidden="true">/</span>
        <a href="shop.html">Panier</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">Paiement</span>
      </nav>
      <?php if (!empty($_SESSION['checkout_error'])): ?>
        <div class="panel" style="background:#ffeaea;color:#a00;margin:16px 0;padding:12px 18px;border-radius:10px;">
          <?= htmlspecialchars($_SESSION['checkout_error']) ?>
        </div>
        <?php unset($_SESSION['checkout_error']); ?>
      <?php endif; ?>

      <section class="hero-checkout container">
        <p class="eyebrow">Étape finale</p>
        <h1 class="page-title">Paiement</h1>
      </section>

      <section class="container grid-2">
        <!-- Colonne gauche : adresses + méthode de paiement -->
        <form class="panel" id="checkout-form" action="create-checkout-session.php" method="post" novalidate>
          <h2>Coordonnées & Livraison</h2>

          <div class="row row-cols-2">
            <div class="field">
              <label for="first_name">Prénom</label>
              <input id="first_name" name="first_name" type="text" required autocomplete="given-name">
            </div>
            <div class="field">
              <label for="last_name">Nom</label>
              <input id="last_name" name="last_name" type="text" required autocomplete="family-name">
            </div>
          </div>

          <div class="field">
            <label for="email">E-mail</label>
            <input id="email" name="email" type="email" required autocomplete="email">
          </div>

          <div class="field">
            <label for="phone">Téléphone (optionnel)</label>
            <input id="phone" name="phone" type="tel" autocomplete="tel">
          </div>

          <div class="sep"></div>

          <h2>Adresse de livraison</h2>

          <div class="field">
            <label for="addr1">Adresse</label>
            <input id="addr1" name="addr1" type="text" required autocomplete="address-line1">
          </div>

          <div class="field">
            <label for="addr2">Complément (optionnel)</label>
            <input id="addr2" name="addr2" type="text" autocomplete="address-line2">
          </div>

          <div class="row row-cols-3">
            <div class="field">
              <label for="zip">Code postal</label>
              <input id="zip" name="zip" type="text" required autocomplete="postal-code">
            </div>
            <div class="field">
              <label for="city">Ville</label>
              <input id="city" name="city" type="text" required autocomplete="address-level2">
            </div>
            <div class="field">
              <label for="country">Pays</label>
              <select id="country" name="country" required>
                <option value="FR">France</option>
                <option value="BE">Belgique</option>
                <option value="CH">Suisse</option>
              </select>
            </div>
          </div>

          <div class="field">
            <label><input type="checkbox" id="same_billing" name="same_billing" checked> Utiliser comme adresse de facturation</label>
          </div>

          <div id="billing-block" hidden>
            <div class="sep"></div>
            <h2>Adresse de facturation</h2>
            <div class="field">
              <label for="b_addr1">Adresse</label>
              <input id="b_addr1" name="b_addr1" type="text" autocomplete="address-line1">
            </div>
            <div class="row row-cols-3">
              <div class="field">
                <label for="b_zip">Code postal</label>
                <input id="b_zip" name="b_zip" type="text" autocomplete="postal-code">
              </div>
              <div class="field">
                <label for="b_city">Ville</label>
                <input id="b_city" name="b_city" type="text" autocomplete="address-level2">
              </div>
              <div class="field">
                <label for="b_country">Pays</label>
                <select id="b_country" name="b_country">
                  <option value="FR">France</option>
                  <option value="BE">Belgique</option>
                  <option value="CH">Suisse</option>
                </select>
              </div>
            </div>
          </div>

          <div class="sep"></div>
          <h2>Méthode de paiement</h2>

          <div class="pay-methods">
            <label class="pay-opt">
              <input type="radio" name="pay_method" value="card" required checked>
              Carte bancaire
              <span class="help">3-D Secure pris en charge</span>
            </label>
            <label class="pay-opt">
              <input type="radio" name="pay_method" value="paypal">
              PayPal
            </label>
          </div>

          <!-- Astuce : on NE collecte PAS la carte ici.
               On crée une Session de paiement côté serveur et on redirige. -->

          <div class="actions">
            <button type="submit" class="btn btn--accent">Procéder au paiement</button>
            <a class="btn" href="shop.html">Retour à la boutique</a>
          </div>

          <p class="muted">En cliquant, vous serez redirigé vers une page de paiement sécurisée.</p>
        </form>

        <!-- Colonne droite : récap commande -->
        <aside class="panel summary">
          <h2>Récapitulatif</h2>
          <?php
          // Accepte les clés 'name' (JS) ou 'title' (fallback)
          $items = $_SESSION['cart'] ?? [
            ['name'=>'Veste Atelier', 'qty'=>1, 'price'=>18000],
            ['name'=>'Chemise Selvedge', 'qty'=>1, 'price'=>9500],
          ];
          $subtotal = 0;
          foreach($items as $it){
            // Récupère le nom du produit
            $label = isset($it['title']) ? $it['title'] : ($it['name'] ?? '');
            // Prix : si < 100, on suppose que c'est en euros, sinon en centimes
            $prix = (float)$it['price'];
            if ($prix < 100) $prix = round($prix * 100); // convertit en centimes si besoin
            $subtotal += $prix * (int)$it['qty'];
            $it['_label'] = $label;
            $it['_prix'] = $prix;
            $it['_qty'] = (int)$it['qty'];
            $itList[] = $it;
          }
          $shipping = ($subtotal >= 15000) ? 0 : 700; // gratuit dès 150€
          $total = $subtotal + $shipping;
          function euro($c){ return number_format($c/100, 2, ',', ' ') . ' €'; }
          ?>
          <div class="checkout-lines">
            <?php foreach($itList as $it): ?>
              <div class="line">
                <span><?= htmlspecialchars($it['_label']) ?> × <?= $it['_qty'] ?></span>
                <span><?= euro($it['_prix'] * $it['_qty']) ?></span>
              </div>
            <?php endforeach; ?>
            <div class="line"><span>Sous-total</span><span><?= euro($subtotal) ?></span></div>
            <div class="line"><span>Livraison</span><span><?= $shipping ? euro($shipping) : 'Offerte' ?></span></div>
            <div class="line total"><span>Total</span><span><?= euro($total) ?></span></div>
          </div>
          <p class="note">Taxes incluses. Le total final est vérifié côté serveur avant paiement.</p>
        </aside>
      </section>
    </div>
  </main>

  <script>
    // Toggle adresse de facturation
    const sameBilling = document.getElementById('same_billing');
    const billing = document.getElementById('billing-block');
    function toggleBilling(){ billing.hidden = sameBilling.checked; }
    sameBilling?.addEventListener('change', toggleBilling); toggleBilling();
  </script>
</body>
</html>
