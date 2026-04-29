<?php
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$usuario_id = $_SESSION['user_id'];
$producto_id = $data['producto_id'];
$producto_nombre = $data['producto_nombre'];
$producto_precio = $data['producto_precio'];
$producto_imagen = $data['producto_imagen'];

// Verificar si ya existe en el carrito
$check = $conn->prepare("SELECT id, cantidad FROM carrito WHERE usuario_id = ? AND producto_id = ?");
$check->bind_param("ii", $usuario_id, $producto_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    // Si existe, actualizar cantidad
    $row = $result->fetch_assoc();
    $nueva_cantidad = $row['cantidad'] + 1;
    $update = $conn->prepare("UPDATE carrito SET cantidad = ? WHERE id = ?");
    $update->bind_param("ii", $nueva_cantidad, $row['id']);
    $update->execute();
    $update->close();
} else {
    // Si no existe, insertar nuevo
    $insert = $conn->prepare("INSERT INTO carrito (usuario_id, producto_id, producto_nombre, producto_precio, producto_imagen, cantidad) VALUES (?, ?, ?, ?, ?, 1)");
    $insert->bind_param("iisds", $usuario_id, $producto_id, $producto_nombre, $producto_precio, $producto_imagen);
    $insert->execute();
    $insert->close();
}

$check->close();
echo json_encode(['success' => true, 'message' => 'Producto añadido al carrito']);
?>
