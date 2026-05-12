<?php
require_once 'config.php';
echo "✅ Conectado a la base de datos<br>";
$result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
$row = $result->fetch_assoc();
echo "📊 Usuarios registrados: " . $row['total'];
?>
