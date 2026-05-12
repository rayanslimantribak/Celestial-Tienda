<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.html'); exit; }
$pedido_id = $_GET['pedido_id'] ?? 0;
$usuario_id = $_SESSION['user_id'];
$pedido = $conn->query("SELECT * FROM pedidos WHERE id = $pedido_id AND usuario_id = $usuario_id");
if ($pedido->num_rows == 0) { header('Location: ver_carrito.php'); exit; }
$pedido_data = $pedido->fetch_assoc();
$direccion = $conn->query("SELECT * FROM direcciones WHERE id = " . $pedido_data['direccion_id'])->fetch_assoc();
$detalles = $conn->query("SELECT * FROM pedidos_detalles WHERE pedido_id = $pedido_id");
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><link rel="stylesheet" href="style.css"><title>Compra Exitosa - Celestial</title>
<style>.resumen{background:white;padding:20px;border-radius:10px;margin:20px 0}.total{font-size:1.5em;font-weight:bold;color:#00C851;text-align:right}body.modo-oscuro .resumen{background:#2d2d44;color:white}</style></head>
<body>
<header><div class="logo"><img src="https://i.ibb.co/0pFFxgx6/Celestial-4-removebg-preview.png"></div>
<nav><a href="inicio.html">Inicio</a><a href="productos.html">Productos</a><a href="presentacion.html">Nosotros</a><a href="contacto.html">Contacto</a><a href="ver_carrito.php">Carrito</a></nav></header>
<section class="hero"><div style="max-width:600px;margin:0 auto;text-align:center">
<h2 style="color:white">Compra Realizada con Éxito</h2>
<div class="resumen"><p><strong>Pedido Nº:</strong> <?= $pedido_data['id'] ?></p>
<p><strong>Fecha:</strong> <?= $pedido_data['fecha'] ?></p>
<h3>Productos comprados:</h3><ul><?php while($item = $detalles->fetch_assoc()): ?>
<li><?= $item['producto_nombre'] ?> x <?= $item['cantidad'] ?> - <?= number_format($item['producto_precio'] * $item['cantidad'],2) ?> €</li>
<?php endwhile; ?></ul>
<p class="total">Total: <?= number_format($pedido_data['total'],2) ?> €</p>
<h3>Datos de Envío:</h3><p><strong><?= htmlspecialchars($direccion['nombre_completo']) ?></strong></p>
<p><?= $direccion['direccion'] ?></p><p><?= $direccion['ciudad'] ?> - <?= $direccion['codigo_postal'] ?></p>
<p>Teléfono: <?= $direccion['telefono'] ?></p></div>
<a href="productos.html" class="cta-button">Seguir Comprando</a> <a href="mis_pedidos.php" class="cta-button" style="background:#7bd5ff">Ver Mis Pedidos</a>
</div></section>
<footer><p>© Celestial</p></footer>
<script src="auth.js"></script><script src="oscuro.js"></script>
</body>
</html>
