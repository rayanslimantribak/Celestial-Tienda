<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

$usuario_id = $_SESSION['user_id'];
$nombre_usuario = $_SESSION['user_name'];

// Obtener pedidos del usuario logueado
$pedidos = $conn->query("
    SELECT p.*, d.direccion, d.ciudad, d.codigo_postal, d.telefono, d.nombre_completo
    FROM pedidos p
    LEFT JOIN direcciones d ON p.direccion_id = d.id
    WHERE p.usuario_id = $usuario_id
    ORDER BY p.fecha DESC
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Mis Pedidos - Celestial</title>
    <style>
        .pedido {
            background: white;
            padding: 20px;
            margin: 15px 0;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .pedido-total {
            font-weight: bold;
            font-size: 1.2em;
            color: #00C851;
            text-align: right;
        }
        .detalles {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        .detalles ul {
            margin: 10px 0 0 20px;
        }
        body.modo-oscuro .pedido {
            background: #2d2d44;
            color: white;
        }
        body.modo-oscuro .detalles {
            border-top-color: #555;
        }
        .resumen-cliente {
            background: #7bd5ff;
            color: #333;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        body.modo-oscuro .resumen-cliente {
            background: #1a1a2e;
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
        <a href="perfil.html">Mi Perfil</a>
    </nav>
</header>

<section class="hero">
    <div style="max-width: 800px; margin: 0 auto;">
        <div class="resumen-cliente">
            <h2>Mis Pedidos</h2>
            <p>Cliente: <?php echo htmlspecialchars($nombre_usuario); ?></p>
        </div>

<?php
if ($pedidos->num_rows == 0) {
    echo '<div class="pedido" style="text-align: center;">No has realizado ningún pedido todavía.</div>';
} else {
    while($pedido = $pedidos->fetch_assoc()) {
        echo '<div class="pedido">';
        echo '<p><strong>Pedido #' . $pedido['id'] . '</strong></p>';
        echo '<p>Fecha: ' . $pedido['fecha'] . '</p>';
        echo '<p>Estado: <span style="color: #00C851;">' . $pedido['estado'] . '</span></p>';
        
        // Dirección de envío
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
            echo '<div class="detalles"><strong>Productos comprados:</strong><ul>';
            while($detalle = $detalles->fetch_assoc()) {
                echo '<li>' . $detalle['producto_nombre'] . ' x ' . $detalle['cantidad'] . ' - ' . number_format($detalle['producto_precio'] * $detalle['cantidad'], 2) . ' €</li>';
            }
            echo '</ul></div>';
        }
        
        echo '<div class="pedido-total">Total: ' . number_format($pedido['total'], 2) . ' €</div>';
        echo '</div>';
    }
}
?>

        <div style="text-align: center; margin-top: 20px;">
            <a href="perfil.html" class="cta-button" style="width: auto; padding: 10px 25px;">Volver al Perfil</a>
        </div>
    </div>
</section>

<footer><p>© Celestial</p></footer>

<script src="auth.js"></script>
<script src="traductor.js"></script>
<script src="oscuro.js"></script>
</body>
</html>
