<?php
require_once 'config.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { echo json_encode(['success' => false]); exit; }
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];
$usuario_id = $_SESSION['user_id'];
$stmt = $conn->prepare("DELETE FROM carrito WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $id, $usuario_id);
$stmt->execute();
echo json_encode(['success' => true]);
$stmt->close();
$conn->close();
?>
