<?php
// register.php
require_once 'config.php';

// Recibir datos del formulario
$data = json_decode(file_get_contents('php://input'), true);

$nombre = $data['nombre'];
$email = $data['email'];
$password = $data['password'];

// Validar que el email no exista
$check_sql = "SELECT id FROM usuarios WHERE email = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $email);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'El email ya está registrado']);
    exit;
}

// Hash de la contraseña
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$joinDate = date('d/m/Y');

// Insertar nuevo usuario
$sql = "INSERT INTO usuarios (nombre, email, password, joinDate) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $nombre, $email, $hashed_password, $joinDate);

if ($stmt->execute()) {
    $user_id = $stmt->insert_id;
    
    // Guardar datos del usuario en sesión
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $nombre;
    $_SESSION['user_email'] = $email;
    $_SESSION['joinDate'] = $joinDate;
    
    echo json_encode([
        'success' => true, 
        'user' => [
            'id' => $user_id,
            'name' => $nombre,
            'email' => $email,
            'joinDate' => $joinDate
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al registrar']);
}

$stmt->close();
$conn->close();
?>
