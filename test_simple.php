<?php
$conn = new mysqli('localhost', 'celestial_user', '1234', 'celestial_db');
if ($conn->connect_error) {
    die("❌ Error: " . $conn->connect_error);
}
echo "✅ Conectado correctamente a celestial_db";
$conn->close();
?>
