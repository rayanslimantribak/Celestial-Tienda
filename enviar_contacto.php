<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false]);
    exit;
}

$nombre = $_POST['nombre'] ?? '';
$email = $_POST['email'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$mensaje = $_POST['mensaje'] ?? '';

if (empty($nombre) || empty($email) || empty($mensaje)) {
    echo json_encode(['success' => false]);
    exit;
}

// Configurar el email
$to = "rayanslimantribak@elpuig.xeill.net";
$subject = "Nuevo mensaje de contacto - Celestial";

$contenido = "Has recibido un nuevo mensaje de contacto:\n\n";
$contenido .= "Nombre: $nombre\n";
$contenido .= "Email: $email\n";
if (!empty($telefono)) {
    $contenido .= "Teléfono: $telefono\n";
}
$contenido .= "\nMensaje:\n$mensaje\n";

$headers = "From: $email\r\n";
$headers .= "Reply-To: $email\r\n";

// Enviar email
$enviado = mail($to, $subject, $contenido, $headers);

if ($enviado) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
