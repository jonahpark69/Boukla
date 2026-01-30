<?php
// set-cart.php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart = json_decode(file_get_contents('php://input'), true);
    if (is_array($cart)) {
        $_SESSION['cart'] = $cart;
        echo json_encode(['success' => true]);
        exit;
    }
}
echo json_encode(['success' => false]);
http_response_code(400);
