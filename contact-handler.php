<?php
// contact-handler.php
header_remove('X-Powered-By');

function respond($ok, $msg, $isAjax){
  if ($isAjax){
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success'=>$ok, 'message'=>$msg], JSON_UNESCAPED_UNICODE);
  } else {
    // Réponse simple HTML (fallback post → action)
    $status = $ok ? 'Succès' : 'Erreur';
    $color  = $ok ? '#136b2b' : '#9d1b1b';
    echo "<!doctype html><meta charset='utf-8'><title>Contact — $status</title>
    <div style='max-width:680px;margin:10vh auto;font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;line-height:1.6;'>
      <h1 style='font:700 24px/1.2 \"Playfair Display\",Georgia,serif;text-transform:uppercase;letter-spacing:.3em;margin:0 0 8px;'>Contact — $status</h1>
      <p style='color:$color;'>$msg</p>
      <p><a href='contact.php'>Retour à la page Contact</a></p>
    </div>";
  }
  exit;
}

$isAjax = isset($_POST['ajax']);
$name   = trim((string)($_POST['name'] ?? ''));
$email  = trim((string)($_POST['email'] ?? ''));
$msg    = trim((string)($_POST['message'] ?? ''));
$hp     = trim((string)($_POST['website'] ?? '')); // honeypot

if ($hp !== '') respond(false, 'Vérification anti-spam.', $isAjax);
if ($name === '' || $email === '' || $msg === '') respond(false, 'Tous les champs sont requis.', $isAjax);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) respond(false, 'Adresse e-mail invalide.', $isAjax);
if (mb_strlen($name) > 100 || mb_strlen($email) > 160 || mb_strlen($msg) > 2000){
  respond(false, 'Contenu trop long.', $isAjax);
}

// Petit nettoyage anti header-injection
$email = str_replace(["\r","\n"], '', $email);

// 1) Essai d'envoi email (selon config PHP)
$to = 'contact@ton-domaine.com'; // <-- remplace par ta boîte
$subject = 'Nouveau message via Boukla';
$body = "Nom: $name\nEmail: $email\n\nMessage:\n$msg\n";
$headers = "From: Boukla <no-reply@ton-domaine.com>\r\nReply-To: $email\r\nContent-Type: text/plain; charset=utf-8\r\n";

$sent = false;
if (function_exists('mail')){
  $sent = @mail($to, $subject, $body, $headers);
}

// 2) Fallback log fichier si mail indisponible
if (!$sent){
  $line = date('c')." | $name <$email> | ".preg_replace('/\s+/',' ', $msg).PHP_EOL;
  @file_put_contents(__DIR__.'/contact-messages.log', $line, FILE_APPEND);
}

respond(true, 'Merci, votre message a été transmis.', $isAjax);
