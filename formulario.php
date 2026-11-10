<?php
// Formulario de contacto de MINPOLA
// Cambiá la dirección de $email_to si querés recibir las consultas en otro correo.
$email_to = "pjvalenzuela@gmail.com";
$email_subject = "Nueva consulta desde la web de MINPOLA";

function limpiar($valor) {
  // Quita espacios y saltos de línea para evitar que se inyecten cabeceras en el correo
  return trim(str_replace(array("\r", "\n"), ' ', strip_tags($valor)));
}

$enviado = false;
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre   = limpiar($_POST['nombre'] ?? '');
  $apellido = limpiar($_POST['apellido'] ?? '');
  $telefono = limpiar($_POST['telefono'] ?? '');
  $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
  $mensaje  = trim(strip_tags($_POST['mensaje'] ?? ''));

  if ($nombre === '' || !$email || $mensaje === '') {
    $error = "Completá tu nombre, un email válido y tu mensaje.";
  } else {
    $email_message  = "Detalles del formulario de contacto:\n\n";
    $email_message .= "Nombre: " . $nombre . "\n";
    $email_message .= "Apellido: " . $apellido . "\n";
    $email_message .= "WhatsApp o teléfono: " . $telefono . "\n";
    $email_message .= "Email: " . $email . "\n\n";
    $email_message .= "Mensaje:\n" . $mensaje . "\n";

    // El remitente tiene que ser de tu propio dominio para que el correo no vaya a spam
    $dominio = preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'] ?? 'localhost');
    $headers  = "From: MINPOLA Web <no-reply@" . $dominio . ">\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    $enviado = @mail($email_to, '=?UTF-8?B?' . base64_encode($email_subject) . '?=', $email_message, $headers);
    if (!$enviado) {
      $error = "No pudimos enviar tu mensaje. Escribinos por WhatsApp y te respondemos enseguida.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $enviado ? 'Mensaje enviado' : 'No se pudo enviar'; ?> | MINPOLA</title>
  <link rel="icon" type="image/png" href="images/minpola-logo.png">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700;800&display=swap" rel="stylesheet">
  <style>
    body{margin:0;min-height:100vh;display:grid;place-items:center;font-family:Montserrat,Arial,sans-serif;background:linear-gradient(160deg,#0A6CF5,#0337A8);color:#fff;padding:24px;box-sizing:border-box}
    .caja{max-width:460px;text-align:center}
    img{width:96px;height:96px;border-radius:50%;box-shadow:0 0 0 3px #fff}
    h1{font-size:1.8rem;margin:22px 0 10px}
    p{color:#E3EEFF;line-height:1.6}
    a{display:inline-block;margin:8px 6px 0;padding:14px 24px;border-radius:999px;font-weight:700;text-decoration:none}
    .wa{background:#25D366;color:#fff}
    .volver{background:#fff;color:#0337A8}
  </style>
</head>
<body>
  <div class="caja">
    <img src="images/minpola-logo.png" alt="MINPOLA">
    <?php if ($enviado): ?>
      <h1>¡Gracias, <?php echo htmlspecialchars($nombre); ?>!</h1>
      <p>Recibimos tu mensaje y te respondemos a la brevedad. Si querés una respuesta más rápida, escribinos por WhatsApp.</p>
    <?php else: ?>
      <h1>No se pudo enviar</h1>
      <p><?php echo htmlspecialchars($error ?: "Volvé a la página e intentá de nuevo."); ?></p>
    <?php endif; ?>
    <a class="wa" href="https://wa.me/5491125725820?text=<?php echo rawurlencode('Hola MINPOLA, vi su página y quiero hacer crecer mi negocio. Mi negocio es: '); ?>">Escribir por WhatsApp</a>
    <a class="volver" href="index.html">Volver a la web</a>
  </div>
</body>
</html>
