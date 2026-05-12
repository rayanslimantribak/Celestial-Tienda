<?php
require_once 'config.php';

// Solo el administrador (id = 1) puede acceder
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header('Location: login.html');
    exit;
}

// Cambiar estado del pedido si viene por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pedido_id']) && isset($_POST['estado'])) {
    $pedido_id = $_POST['pedido_id'];
    $estado = $_POST['estado'];
    $stmt = $conn->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
    $stmt->bind_param("si", $estado, $pedido_id);
    $stmt->execute();
    $stmt->close();
}

// Obtener todos los pedidos con datos del usuario y dirección
$pedidos = $conn->query("
    SELECT p.*, u.nombre as usuario_nombre, u.email as usuario_email,
           d.direccion, d.ciudad, d.codigo_postal, d.telefono, d.nombre_completo
    FROM pedidos p
    LEFT JOIN usuarios u ON p.usuario_id = u.id
    LEFT JOIN direcciones d ON p.direccion_id = d.id
    ORDER BY p.fecha DESC
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Administrar Pedidos - Celestial</title>
    <style>
        .pedido {
            background: white;
            padding: 20px;
            margin: 15px 0;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .pedido-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .estado-select {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        select, button {
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .btn-actualizar {
            background: #4caf50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .pedido-total {
            font-weight: bold;
            font-size: 1.2em;
            color: #00C851;
            text-align: right;
            margin-top: 15px;
        }
        .admin-header {
            background: #ff9800;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        body.modo-oscuro .pedido {
            background: #2d2d44;
            color: white;
        }
    </style>
</head>
<body>
<header>
    <div class="logo"><img src="https://i.ibb.co/0pFFxgx6/Celestial-4-removebg-preview.png"></div>
    <nav>
        <a href="inicio.html">Inicio</a>
        <a href="productos.html">Productos</a>
        <a href="presentacion.html">Nosotros</a>
        <a href="contacto.html">Contacto</a>
        <a href="ver_carrito.php">Carrito</a>
        <a href="perfil.html">Perfil</a>
    </nav>
</header>

<section class="hero">
    <div style="max-width: 900px; margin: 0 auto;">
        <div class="admin-header">
            <h2>Panel de Administración - Todos los Pedidos</h2>
        </div>

<?php
if ($pedidos->num_rows == 0) {
    echo '<div class="pedido" style="text-align: center;">No hay pedidos realizados todavía.</div>';
} else {
    while($pedido = $pedidos->fetch_assoc()) {
        echo '<div class="pedido">';
        echo '<div class="pedido-header">';
        echo '<strong>Pedido #' . $pedido['id'] . '</strong>';
        echo '<form method="POST" class="estado-select">';
        echo '<input type="hidden" name="pedido_id" value="' . $pedido['id'] . '">';
        echo '<select name="estado">';
        echo '<option value="pendiente"' . ($pedido['estado'] == 'pendiente' ? ' selected' : '') . '>Pendiente</option>';
        echo '<option value="en_preparacion"' . ($pedido['estado'] == 'en_preparacion' ? ' selected' : '') . '>En preparación</option>';
        echo '<option value="enviado"' . ($pedido['estado'] == 'enviado' ? ' selected' : '') . '>Enviado</option>';
        echo '<option value="entregado"' . ($pedido['estado'] == 'entregado' ? ' selected' : '') . '>Entregado</option>';
        echo '</select>';
        echo '<button type="submit" class="btn-actualizar">Actualizar</button>';
        echo '</form>';
        echo '</div>';
        
        echo '<p><strong>Cliente:</strong> ' . $pedido['usuario_nombre'] . ' (' . $pedido['usuario_email'] . ')</p>';
        echo '<p><strong>Fecha:</strong> ' . $pedido['fecha'] . '</p>';
        
        if ($pedido['direccion']) {
            echo '<div style="background: #f0f0f0; padding: 10px; border-radius: 5px; margin: 10px 0;">';
            echo '<p><strong>Dirección de envío:</strong></p>';
            echo '<p><strong>' . htmlspecialchars($pedido['nombre_completo']) . '</strong></p>';
            echo '<p>' . $pedido['direccion'] . '</p>';
            echo '<p>' . $pedido['ciudad'] . ' - ' . $pedido['codigo_postal'] . '</p>';
            echo '<p>Teléfono: ' . $pedido['telefono'] . '</p>';
            echo '</div>';
        }
        
        // Productos comprados
        $detalles = $conn->query("SELECT * FROM pedidos_detalles WHERE pedido_id = " . $pedido['id']);
        if ($detalles->num_rows > 0) {
            echo '<p><strong>Productos:</strong></p><ul>';
            while($detalle = $detalles->fetch_assoc()) {
                echo '<li>' . $detalle['producto_nombre'] . ' x ' . $detalle['cantidad'] . ' - ' . number_format($detalle['producto_precio'] * $detalle['cantidad'], 2) . ' €</li>';
            }
            echo '</ul>';
        }
        
        echo '<div class="pedido-total">Total: ' . number_format($pedido['total'], 2) . ' €</div>';
        echo '</div>';
    }
}
?>

        <div style="text-align: center; margin-top: 20px;">
            <a href="perfil.html" class="cta-button" style="width: auto; padding: 10px 25px; background: #ff9800;">Volver al Perfil</a>
        </div>
    </div>
</section>

<footer><p>© Celestial</p></footer>

<script src="auth.js"></script>
<script src="traductor.js"></script>
<script src="oscuro.js"></script>
</body>
</html>
