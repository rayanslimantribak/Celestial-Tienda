<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.html'); exit; }
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario_id = $_SESSION['user_id'];
    $producto_id = $_POST['producto_id'];
    $producto_nombre = $_POST['producto_nombre'];
    $producto_precio = $_POST['producto_precio'];
    $producto_imagen = $_POST['producto_imagen'];
    $check = $conn->prepare("SELECT id, cantidad FROM carrito WHERE usuario_id = ? AND producto_id = ?");
    $check->bind_param("ii", $usuario_id, $producto_id);
    $check->execute();
    $res = $check->get_result();
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $nueva = $row['cantidad'] + 1;
        $upd = $conn->prepare("UPDATE carrito SET cantidad = ? WHERE id = ?");
        $upd->bind_param("ii", $nueva, $row['id']);
        $upd->execute();
        $upd->close();
    } else {
        $ins = $conn->prepare("INSERT INTO carrito (usuario_id, producto_id, producto_nombre, producto_precio, producto_imagen, cantidad) VALUES (?, ?, ?, ?, ?, 1)");
        $ins->bind_param("iisssi", $usuario_id, $producto_id, $producto_nombre, $producto_precio, $producto_imagen);
        $ins->execute();
        $ins->close();
    }
    $check->close();
    header('Location: ver_carrito.php');
    exit;
}
?>
