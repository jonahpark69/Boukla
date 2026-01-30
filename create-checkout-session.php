<?php
// create-checkout-session.php
// Reçoit le POST de checkout.php, vérifie le panier, prépare la commande,
// puis redirige : soit vers Stripe Checkout (si activé), soit vers une page "succès" (mock).

session_start();

// ---- CONFIG ----
$MOCK_MODE = true;                  // ← true = pas de Stripe, redirection vers pay-success.php
$FREE_SHIPPING_THRESHOLD = 15000;   // 150,00 € (en centimes)
$FLAT_SHIPPING = 700;               // 7,00 € (en centimes)

// ---- Garde-fous ----
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  $_SESSION['checkout_error'] = "Accès non autorisé.";
  header('Location: checkout.php');
  exit;
}

// ---- 1) Récup données form ----
$first = trim($_POST['first_name'] ?? '');
$last  = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$addr1 = trim($_POST['addr1'] ?? '');
$addr2 = trim($_POST['addr2'] ?? '');
$zip   = trim($_POST['zip'] ?? '');
$city  = trim($_POST['city'] ?? '');
$country = trim($_POST['country'] ?? 'FR');

$sameBilling = isset($_POST['same_billing']);

$bill_addr1 = trim($_POST['b_addr1'] ?? '');
$bill_zip   = trim($_POST['b_zip'] ?? '');
$bill_city  = trim($_POST['b_city'] ?? '');
$bill_country = trim($_POST['b_country'] ?? $country);

$pay_method = $_POST['pay_method'] ?? 'card';

// Champs requis minimum
$required = [
  'Prénom' => $first,
  'Nom'    => $last,
  'E-mail' => $email,
  'Adresse'=> $addr1,
  'Code postal' => $zip,
  'Ville'  => $city,
  'Pays'   => $country,
];
foreach ($required as $label => $val) {
  if ($val === '') {
    $_SESSION['checkout_error'] = "Champ manquant : $label";
    header('Location: checkout.php');
    exit;
  }
}

// Adresse de facturation
if ($sameBilling) {
  $bill_addr1 = $addr1; $bill_zip = $zip; $bill_city = $city; $bill_country = $country;
} else {
  if ($bill_addr1 === '' || $bill_zip === '' || $bill_city === '') {
    $_SESSION['checkout_error'] = "Adresse de facturation incomplète.";
    header('Location: checkout.php');
    exit;
  }
}

// ---- 2) Panier ----
$items = $_SESSION['cart'] ?? null;

// Démo si panier vide (tu peux supprimer ce fallback)
if (!$items || !is_array($items) || !count($items)) {
  $items = [
    ['title'=>'Produit démo A', 'qty'=>1, 'price'=>1290],
    ['title'=>'Produit démo B', 'qty'=>2, 'price'=>990],
  ];
}

// Correction : conversion des prix en centimes si besoin
foreach ($items as &$it) {
  if (isset($it['price']) && $it['price'] < 100) {
    $it['price'] = round($it['price'] * 100);
  }
}
unset($it);

$subtotal = 0;
$lineItems = [];
foreach ($items as $it) {
  $qty = max(1, (int)($it['qty'] ?? 1));
  $price = (int)($it['price'] ?? 0); // en centimes
  $title = (string)($it['title'] ?? $it['name'] ?? 'Article');
  $subtotal += $price * $qty;

  // pour Stripe (si activé)
  $lineItems[] = [
    'quantity' => $qty,
    'price_data' => [
      'currency' => 'eur',
      'unit_amount' => $price,
      'product_data' => ['name' => $title],
    ],
  ];
}

$shipping = ($subtotal >= $FREE_SHIPPING_THRESHOLD) ? 0 : $FLAT_SHIPPING;
$total = $subtotal + $shipping;

// ---- 3) Objet commande (session) ----
$orderRef = 'BK-' . date('ymd') . '-' . strtoupper(substr(md5(uniqid('', true)), 0, 6));

$order = [
  'ref' => $orderRef,
  'created_at' => date('c'),
  'customer' => [
    'first' => $first, 'last' => $last, 'email' => $email, 'phone' => $phone,
  ],
  'shipping_address' => [
    'addr1' => $addr1, 'addr2' => $addr2, 'zip' => $zip, 'city' => $city, 'country' => $country,
  ],
  'billing_address' => [
    'addr1' => $bill_addr1, 'zip' => $bill_zip, 'city' => $bill_city, 'country' => $bill_country,
  ],
  'same_billing' => $sameBilling,
  'pay_method' => $pay_method,
  'items' => $items,
  'subtotal' => $subtotal,
  'shipping' => $shipping,
  'total' => $total,
];

// Enregistrement de la commande en base
require_once 'db.php';
$stmt = $pdo->prepare("INSERT INTO orders (ref, created_at, first, last, email, phone, addr1, addr2, zip, city, country, billing_addr1, billing_zip, billing_city, billing_country, same_billing, pay_method, subtotal, shipping, total)
VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([
  $orderRef, $first, $last, $email, $phone, $addr1, $addr2, $zip, $city, $country,
  $bill_addr1, $bill_zip, $bill_city, $bill_country, $sameBilling ? 1 : 0, $pay_method, $subtotal, $shipping, $total
]);
$orderId = $pdo->lastInsertId();
foreach ($items as $it) {
  $name = $it['title'] ?? $it['name'] ?? '';
  $qty = (int)$it['qty'];
  $price = (int)$it['price'];
  $pdo->prepare("INSERT INTO order_items (order_id, product_name, qty, price) VALUES (?, ?, ?, ?)")
      ->execute([$orderId, $name, $qty, $price]);
}

// Garde une trace (utile pour la page succès / logs)
$_SESSION['last_order'] = $order;

// ---- 4) Redirection paiement ----

// A) MODE MOCK (fonctionne tout de suite en local)
if ($MOCK_MODE) {
  // Simule un paiement OK
  header('Location: pay-success.php?ref=' . urlencode($orderRef));
  exit;
}

// B) STRIPE CHECKOUT (à activer quand tu es prêt)
require __DIR__ . '/vendor/autoload.php';
$stripeSecret = getenv('STRIPE_SECRET_KEY'); // définis la variable d'env ou remplace par ta clé
if (!$stripeSecret) {
  // Sécurité : évite de crasher si pas de clé
  $_SESSION['checkout_error'] = "Stripe n'est pas configuré (clé secrète manquante).";
  header('Location: checkout.php');
  exit;
}

\Stripe\Stripe::setApiKey($stripeSecret);

try {
  // Frais de port en tant que line_item (si > 0)
  if ($shipping > 0) {
    $lineItems[] = [
      'quantity' => 1,
      'price_data' => [
        'currency' => 'eur',
        'unit_amount' => $shipping,
        'product_data' => ['name' => 'Livraison'],
      ],
    ];
  }

  $session = \Stripe\Checkout\Session::create([
    'mode' => 'payment',
    'success_url' => originUrl() . '/pay-success.php?ref=' . urlencode($orderRef) . '&session_id={CHECKOUT_SESSION_ID}',
    'cancel_url'  => originUrl() . '/pay-cancel.php?ref=' . urlencode($orderRef),
    'customer_email' => $email,
    'line_items' => $lineItems,
    'metadata' => [
      'order_ref' => $orderRef,
      'first' => $first,
      'last'  => $last,
    ],
  ]);

  header('Location: ' . $session->url);
  exit;

} catch (\Throwable $e) {
  error_log('Stripe error: ' . $e->getMessage());
  $_SESSION['checkout_error'] = "Impossible de créer la session de paiement.";
  header('Location: checkout.php');
  exit;
}

// ---- helper pour success/cancel URL (si ton projet est dans un sous-dossier)
function originUrl(): string {
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
  $dir  = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
  return $scheme . '://' . $host . $dir;
}
