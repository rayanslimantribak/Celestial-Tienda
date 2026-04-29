<?php
// config.php - Conexión a la base de datos
$host = 'localhost';
$user = 'celestial_user';
$password = '1234';
$database = 'celestial_db';

// Crear conexión
$conn = new mysqli($host, $user, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]));
}

// Establecer charset
$conn->set_charset("utf8");

// Iniciar sesión para mantener al usuario logueado
session_start();
?>
