<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No has iniciado sesión']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No se recibieron datos']);
    exit;
}

$usuario_id = $_SESSION['user_id'];
$producto_id = $data['producto_id'] ?? 1;
$producto_nombre = $data['producto_nombre'] ?? 'Producto Test';
$producto_precio = $data['producto_precio'] ?? 9.99;
$producto_imagen = $data['producto_imagen'] ?? '';
$talla = $data['talla'] ?? 'M';

$insert = $conn->prepare("INSERT INTO carrito (usuario_id, producto_id, producto_nombre, producto_precio, producto_imagen, talla, cantidad) VALUES (?, ?, ?, ?, ?, ?, 1)");
$insert->bind_param("iissssi", $usuario_id, $producto_id, $producto_nombre, $producto_precio, $producto_imagen, $talla);

if ($insert->execute()) {
    echo json_encode(['success' => true, 'message' => 'Producto añadido']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error BD: ' . $conn->error]);
}

$insert->close();
$conn->close();
?>
