<?php
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión']);
    exit;
}

$usuario_id = $_SESSION['user_id'];

$result = $conn->query("SELECT * FROM carrito WHERE usuario_id = $usuario_id");

if ($result->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'El carrito está vacío']);
    exit;
}

$total = 0;
$productos = [];
while($item = $result->fetch_assoc()) {
    $subtotal = $item['producto_precio'] * $item['cantidad'];
    $total += $subtotal;
    $productos[] = $item;
}

$stmt = $conn->prepare("INSERT INTO pedidos (usuario_id, total, estado) VALUES (?, ?, 'completado')");
$stmt->bind_param("id", $usuario_id, $total);
$stmt->execute();
$pedido_id = $stmt->insert_id;

$stmt2 = $conn->prepare("INSERT INTO pedidos_detalles (pedido_id, producto_nombre, producto_precio, cantidad, talla) VALUES (?, ?, ?, ?, ?)");

foreach ($productos as $item) {
    $talla = $item['talla'] ?? 'M';
    $stmt2->bind_param("isdii", $pedido_id, $item['producto_nombre'], $item['producto_precio'], $item['cantidad'], $talla);
    $stmt2->execute();
}

$conn->query("DELETE FROM carrito WHERE usuario_id = $usuario_id");

echo json_encode([
    'success' => true, 
    'message' => 'Compra realizada con éxito',
    'pedido_id' => $pedido_id,
    'total' => $total
]);

$stmt->close();
$stmt2->close();
$conn->close();
?>
