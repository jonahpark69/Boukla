<?php
// admin-orders.php — Liste simple des commandes
require_once 'db.php';
$orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <title>Commandes Boukla — Admin</title>
  <link rel="stylesheet" href="boukla.css">
  <style>
    body { font-family: Inter, Arial, sans-serif; background: #faf9f7; }
    table { border-collapse: collapse; width: 100%; background: #fff; margin: 30px auto; max-width: 1100px; box-shadow: 0 4px 24px #0001; }
    th, td { padding: 10px 14px; border: 1px solid #eee; }
    th { background: #f2c94c; color: #1a0903; text-align: left; }
    tr:nth-child(even) { background: #f8f6f3; }
    h1 { text-align: center; margin-top: 32px; }
    .ref { font-family: monospace; font-size: 15px; }
  </style>
</head>
<body>
  <h1>Commandes Boukla</h1>
  <table>
    <tr>
      <th>Réf</th>
      <th>Date</th>
      <th>Client</th>
      <th>Email</th>
      <th>Total</th>
      <th>Paiement</th>
    </tr>
    <?php foreach($orders as $o): ?>
    <tr>
      <td class="ref"><?= htmlspecialchars($o['ref']) ?></td>
      <td><?= htmlspecialchars($o['created_at']) ?></td>
      <td><?= htmlspecialchars($o['first'].' '.$o['last']) ?></td>
      <td><?= htmlspecialchars($o['email']) ?></td>
      <td><?= number_format($o['total']/100, 2, ',', ' ') ?> €</td>
      <td><?= htmlspecialchars($o['pay_method']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
