<?php
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No has iniciado sesión']);
    exit;
}

$usuario_id = $_SESSION['user_id'];

// Evitar que el administrador (id=1) se elimine a sí mismo
if ($usuario_id == 1) {
    echo json_encode(['success' => false, 'message' => 'No se puede eliminar la cuenta de administrador']);
    exit;
}

// Eliminar datos relacionados del usuario
$conn->query("DELETE FROM carrito WHERE usuario_id = $usuario_id");
$conn->query("DELETE FROM pedidos_detalles WHERE pedido_id IN (SELECT id FROM pedidos WHERE usuario_id = $usuario_id)");
$conn->query("DELETE FROM pedidos WHERE usuario_id = $usuario_id");
$conn->query("DELETE FROM direcciones WHERE usuario_id = $usuario_id");

// Eliminar el usuario
$stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'Cuenta eliminada correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar la cuenta']);
}

$stmt->close();
$conn->close();
?>
