<?php
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$id = $_GET['id'] ?? 0;
$usuario_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT nombre_completo, direccion, ciudad, codigo_postal, telefono FROM direcciones WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        'success' => true,
        'nombre_completo' => $row['nombre_completo'],
        'direccion' => $row['direccion'],
        'ciudad' => $row['ciudad'],
        'codigo_postal' => $row['codigo_postal'],
        'telefono' => $row['telefono']
    ]);
} else {
    echo json_encode(['success' => false]);
}

$stmt->close();
$conn->close();
?>
