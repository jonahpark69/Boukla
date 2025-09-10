<?php
/* ================================
   product.php — Boukla
   ================================ */

declare(strict_types=1);

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function money_fr($n){ return number_format((float)$n, 2, ',', ' ') . ' €'; }

/* ---------- 0) Détection du slug ---------- */
$slug = '';
if (isset($_GET['slug']) && $_GET['slug'] !== '') {
  $slug = trim(rawurldecode((string)$_GET['slug']));
} elseif (!empty($_SERVER['REQUEST_URI'])) {
  if (preg_match('#(?:^|/)(?:produit)/([^/?]+)#i', $_SERVER['REQUEST_URI'], $m)) {
    $slug = rawurldecode($m[1]);
  }
}
if ($slug === '') {
  header('Location: shop.html', true, 302);
  exit;
}

/* ---------- 1) Catalogue de secours (texte complet) ---------- */
$catalogDefaults = [
  "creme-hydratante" => [
    "slug"=>"creme-hydratante",
    "name"=>"Crème hydratante",
    "price"=>34.9, "currency"=>"EUR",
    "short"=>"Hydrate sans alourdir, redéfinit les boucles et limite les frisottis.",
    "description"=>"Notre Crème hydratante nourrit intensément sans alourdir, pour des boucles souples, définies et lumineuses. Sa texture fondante enveloppe la fibre pour limiter les frisottis et maintenir l’hydratation tout au long de la journée.",
    "images"=>["assets/images/produit/boukla-1.png"],
    "benefits"=>["Hydrate en profondeur","Réduit les frisottis","Boucles souples et lumineuses"],
    "actives"=>["Aloe vera","Beurre de karité","Glycérine végétale","Huile de jojoba"],
    "how_to"=>"Sur cheveux humides ou secs, appliquer une noisette en scrunch des longueurs vers les pointes. Ne pas rincer.",
    "badges"=>["Sans silicones","Vegan","Cruelty-free"]
  ],
  "masque-hydratant" => [
    "slug"=>"masque-hydratant",
    "name"=>"Masque hydratant",
    "price"=>24.9, "currency"=>"EUR",
    "short"=>"Soin profond hebdomadaire pour boucles repulpées et brillantes.",
    "description"=>"Le Masque hydratant repulpe et assouplit la fibre pour des boucles rebondies et faciles à coiffer. Aide à limiter la casse liée à la sécheresse.",
    "images"=>["assets/images/produit/étiquette-boukla-3.jpg"],
    "benefits"=>["Hydratation intense","Démêle et assouplit","Brillance et définition"],
    "actives"=>["Aloe vera","Beurre de karité","Huile de coco","Protéines végétales"],
    "how_to"=>"Après shampoing, sur cheveux essorés : appliquer généreusement mèche par mèche, laisser poser 10–20 min, rincer."
  ],
  "shampoing-solide" => [
    "slug"=>"shampoing-solide",
    "name"=>"Shampoing solide",
    "price"=>14.9, "currency"=>"EUR",
    "short"=>"Nettoyage doux, mousse fine, respecte le cuir chevelu.",
    "description"=>"Shampoing solide doux qui nettoie sans décaper et laisse les boucles souples, prêtes à recevoir les soins.",
    "images"=>["assets/images/produit/étiquette-boukla-4.png"],
    "benefits"=>["Nettoyage doux","Respect du cuir chevelu","Mousse fine"],
    "actives"=>["Tensioactifs doux","Huile de coco","Argile blanche","Glycérine"],
    "how_to"=>"Mouiller le galet et les cheveux. Faire mousser, masser, rincer. Laisser sécher le galet."
  ],
  "huile-de-ricin" => [
    "slug"=>"huile-de-ricin",
    "name"=>"Huile de ricin",
    "price"=>29.9, "currency"=>"EUR",
    "short"=>"Fortifie visiblement les longueurs, brillance naturelle.",
    "description"=>"Huile de ricin pure, pressée à froid, idéale pour bains d’huile et massages du cuir chevelu.",
    "images"=>["assets/images/produit/étiquette-produit-boukla-2-mock-up-2.jpg"],
    "benefits"=>["Fortifie visiblement","Limite la casse","Apporte de la brillance"],
    "actives"=>["Ricinus Communis (Castor) Seed Oil 100%"],
    "how_to"=>"Massage cuir chevelu (quelques gouttes, 3–5 min), ou bain d’huile (1 part ricin + 2 parts huile légère)."
  ],
];

/* ---------- 2) Charge products.json puis fusionne avec le secours ---------- */
$products = [];
$productsPath = __DIR__ . '/products.json';
if (is_file($productsPath)) {
  $json = @file_get_contents($productsPath);
  $decoded = json_decode($json, true);
  if (is_array($decoded)) $products = $decoded;
}

/**
 * Fusion non destructive : garde ce qui existe dans $base (JSON),
 * complète avec les champs manquants issus du secours $defaults.
 */
function mergeProduct(array $base, array $defaults): array {
  // Champs scalaires à compléter si absents/vides
  foreach (['name','short','description','currency','how_to','badge'] as $k) {
    if (!isset($base[$k]) || $base[$k] === '' || $base[$k] === null) {
      if (isset($defaults[$k])) $base[$k] = $defaults[$k];
    }
  }
  // Prix
  if (!isset($base['price']) || !is_numeric($base['price'])) {
    if (isset($defaults['price'])) $base['price'] = $defaults['price'];
  }
  // Tableaux : images / benefits / actives / badges
  foreach (['images','benefits','actives','badges'] as $k) {
    if (empty($base[$k]) && !empty($defaults[$k])) {
      $base[$k] = $defaults[$k];
    }
  }
  return $base;
}

// Index secours par slug
$defaultsBySlug = $catalogDefaults;

// Si aucun JSON valide, on prend le secours complet
if (!$products) {
  $products = array_values($catalogDefaults);
} else {
  // Merge par slug pour compléter
  $out = [];
  foreach ($products as $p) {
    $s = isset($p['slug']) ? (string)$p['slug'] : '';
    if ($s !== '' && isset($defaultsBySlug[$s])) {
      $out[] = mergeProduct($p, $defaultsBySlug[$s]);
    } else {
      $out[] = $p; // inconnu dans le secours → tel quel
    }
  }
  $products = $out;
}

/* ---------- 3) Trouve le produit ---------- */
$product = null;
foreach ($products as $p) {
  if (isset($p['slug']) && $p['slug'] === $slug) { $product = $p; break; }
}
if (!$product) {
  header('Location: shop.html', true, 302);
  exit;
}

/* ---------- 4) URLs absolues (compatibles sous-dossier) ---------- */
$scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$origin   = $scheme . '://' . $host;
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
if ($basePath === '') $basePath = '/';

$canonical = $origin . $basePath . '/product.php?slug=' . rawurlencode($slug); // passe à /produit/<slug> quand rewrite OK

$title    = ($product['name'] ?? 'Produit') . ' — Boukla';
$desc     = $product['short'] ?? ($product['description'] ?? '');
$mainImg  = !empty($product['images'][0]) ? (string)$product['images'][0] : '';
$mainAbs  = $mainImg ? ($origin . $basePath . '/' . ltrim($mainImg, '/')) : '';
$priceTxt = money_fr($product['price'] ?? 0);
$currency = $product['currency'] ?? 'EUR';

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title><?=h($title)?></title>

  <!-- Canonical & SEO -->
  <link id="canonical" rel="canonical" href="<?=h($canonical)?>">
  <meta name="description" content="<?=h($desc)?>"/>

  <!-- Fonts & CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="<?=h($basePath)?>/boukla.css">

  <!-- Open Graph -->
  <meta property="og:type" content="product">
  <meta property="og:title" content="<?=h($title)?>">
  <meta property="og:description" content="<?=h($desc)?>">
  <meta property="og:image" content="<?=h($mainAbs)?>">
  <meta property="og:url" content="<?=h($canonical)?>">
  <meta name="twitter:card" content="summary_large_image">

  <!-- JSON-LD Product -->
  <script type="application/ld+json">
  <?=json_encode([
    "@context"=>"https://schema.org",
    "@type"=>"Product",
    "name"=>$product['name'] ?? 'Produit',
    "description"=>$product['description'] ?? ($product['short'] ?? ""),
    "image"=>$mainAbs ?: null,
    "sku"=>$slug,
    "brand"=>["@type"=>"Brand","name"=>"Boukla"],
    "offers"=>[
      "@type"=>"Offer",
      "url"=>$canonical,
      "priceCurrency"=>$currency,
      "price"=>(string)($product['price'] ?? 0),
      "availability"=>"https://schema.org/InStock"
    ]
  ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)?>
  </script>
</head>
<body>
  <!-- Header simple -->
  <header class="header">
    <a class="logo" href="<?=h($basePath)?>/index.html" aria-label="Boukla">
      <img src="<?=h($basePath)?>/assets/images/logo-boukla-3.svg" alt="Boukla" height="32" width="120" decoding="async"/>
    </a>
  </header>

  <!-- Breadcrumb -->
  <nav class="breadcrumb container">
    <a href="<?=h($basePath)?>/index.html">Home</a> <span aria-hidden="true">/</span>
    <a href="<?=h($basePath)?>/shop.html">Shop</a> <span aria-hidden="true">/</span>
    <strong id="bc-name"><?=h($product['name'] ?? 'Produit')?></strong>
  </nav>

  <!-- Fiche produit -->
  <main class="container product-page">
    <article class="product">
      <div class="product__media">
        <img id="p-image" src="<?=h($mainImg)?>" alt="<?=h($product['name'] ?? '')?>" width="900" height="1125" loading="eager">
        <div id="p-thumbs" class="product__thumbs" aria-label="Vignettes des images">
          <?php foreach (($product['images'] ?? []) as $src): ?>
            <button type="button" class="thumb" data-src="<?=h($src)?>">
              <img src="<?=h($src)?>" alt="">
            </button>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="product__content">
        <h1 id="p-title" class="h1"><?=h($product['name'] ?? 'Produit')?></h1>

        <?php if (!empty($product['short'])): ?>
          <p id="p-short" class="p-short"><?=h($product['short'])?></p>
        <?php endif; ?>

        <p class="p-price"><span id="p-price"><?=$priceTxt?></span></p>

        <?php if (!empty($product['badges'])): ?>
          <div id="p-badges" class="p-badges">
            <?php foreach ($product['badges'] as $b): ?>
              <span class="p-badge"><?=h($b)?></span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($product['description'])): ?>
          <div id="p-desc-wrap" class="p-block">
            <h2 class="p-h2">Description</h2>
            <p id="p-desc"><?=h($product['description'])?></p>
          </div>
        <?php endif; ?>

        <?php if (!empty($product['benefits'])): ?>
          <div id="p-benefits-wrap" class="p-block">
            <h2 class="p-h2">Bénéfices</h2>
            <ul id="p-benefits" class="p-list">
              <?php foreach ($product['benefits'] as $li): ?>
                <li><?=h($li)?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <?php if (!empty($product['actives'])): ?>
          <div id="p-actives-wrap" class="p-block">
            <h2 class="p-h2">Actifs clés</h2>
            <ul id="p-actives" class="p-list">
              <?php foreach ($product['actives'] as $li): ?>
                <li><?=h($li)?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <?php if (!empty($product['how_to'])): ?>
          <div id="p-howto-wrap" class="p-block">
            <h2 class="p-h2">Mode d’emploi</h2>
            <p id="p-howto"><?=h($product['how_to'])?></p>
          </div>
        <?php endif; ?>

        <div class="p-actions">
          <a href="<?=h($basePath)?>/shop.html" class="btn btn--ghost">← Retour shop</a>
          <!-- Bouton existant, branché pour cart.js -->
          <button class="btn btn--primary"
                  data-add-to-cart
                  data-id="<?=h($slug)?>"
                  data-name="<?=h($product['name'] ?? 'Produit')?>"
                  data-price="<?=h($product['price'] ?? 0)?>"
                  data-image="<?=h($mainImg)?>">
            Ajouter au panier
          </button>
        </div>
      </div>
    </article>
  </main>

  <footer class="footer">
    <div class="container"><small>© Boukla — All rights reserved.</small></div>
  </footer>

  <!-- JS — Mini-panier -->
  <script defer src="<?=h($basePath)?>/cart.js"></script>

  <!-- JS minimal : thumbs -> image principale -->
  <script>
    (function(){
      const main = document.getElementById('p-image');
      document.querySelectorAll('#p-thumbs .thumb').forEach(btn => {
        btn.addEventListener('click', () => {
          const src = btn.getAttribute('data-src');
          if (src) main.setAttribute('src', src);
        });
      });
    })();
  </script>
</body>
</html>



