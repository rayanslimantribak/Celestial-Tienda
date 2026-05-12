<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

$usuario_id = $_SESSION['user_id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // NUEVO: recoger nombre completo
    $nombre_completo = trim($_POST['nombre_completo'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');
    $codigo_postal = trim($_POST['codigo_postal'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');

    if (empty($nombre_completo) || empty($direccion) || empty($ciudad) || empty($codigo_postal) || empty($telefono)) {
        $error = 'Todos los campos son obligatorios';
    } else {
        // NUEVO: añadir nombre_completo a la inserción
        $stmt = $conn->prepare("INSERT INTO direcciones (usuario_id, nombre_completo, direccion, ciudad, codigo_postal, telefono) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $usuario_id, $nombre_completo, $direccion, $ciudad, $codigo_postal, $telefono);
        $stmt->execute();
        $direccion_id = $stmt->insert_id;
        $stmt->close();

        $carrito = $conn->query("SELECT * FROM carrito WHERE usuario_id = $usuario_id");

        if ($carrito->num_rows == 0) {
            $error = 'El carrito está vacío';
        } else {
            $total = 0;
            $productos = [];
            while ($item = $carrito->fetch_assoc()) {
                $total += $item['producto_precio'] * $item['cantidad'];
                $productos[] = $item;
            }

            $stmt2 = $conn->prepare("INSERT INTO pedidos (usuario_id, total, estado, direccion_id) VALUES (?, ?, 'pendiente', ?)");
            $stmt2->bind_param("idi", $usuario_id, $total, $direccion_id);
            $stmt2->execute();
            $pedido_id = $stmt2->insert_id;

            $stmt3 = $conn->prepare("INSERT INTO pedidos_detalles (pedido_id, producto_nombre, producto_precio, cantidad) VALUES (?, ?, ?, ?)");
            foreach ($productos as $item) {
                $stmt3->bind_param("isdi", $pedido_id, $item['producto_nombre'], $item['producto_precio'], $item['cantidad']);
                $stmt3->execute();
            }

            $conn->query("DELETE FROM carrito WHERE usuario_id = $usuario_id");
            header("Location: compra_exitosa.php?pedido_id=$pedido_id");
            exit;
        }
    }
}

// Obtener direcciones guardadas del usuario (incluyendo el nombre completo)
$direcciones_guardadas = $conn->query("SELECT * FROM direcciones WHERE usuario_id = $usuario_id ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Finalizar Compra - Celestial</title>
    <style>
        .checkout-form { max-width: 500px; margin: 0 auto; }
        .error { background: #ff4444; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; }
        .direccion-guardada { background: #f0f0f0; padding: 10px; margin: 10px 0; border-radius: 5px; cursor: pointer; }
        body.modo-oscuro .direccion-guardada { background: #1a1a2e; color: white; }
        label { display: block; margin-top: 10px; font-weight: bold; }
    </style>
</head>
<body>
<header>
    <div class="logo"><img src="https://i.ibb.co/0pFFxgx6/Celestial-4-removebg-preview.png"></div>
    <nav>
        <a href="inicio.html" data-key="inicio">Inicio</a>
        <a href="productos.html" data-key="productos">Productos</a>
        <a href="presentacion.html" data-key="nosotros">Nosotros</a>
        <a href="contacto.html" data-key="contacto">Contacto</a>
        <a href="ver_carrito.php" data-key="carrito">Carrito</a>
    </nav>
</header>

<section class="hero">
    <div class="card checkout-form">
        <h2 data-key="datos_envio">Datos de Envío</h2>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($direcciones_guardadas->num_rows > 0): ?>
            <div style="margin-bottom: 20px;">
                <p><strong data-key="usar_direccion">Usar una dirección guardada:</strong></p>
                <?php while ($dir = $direcciones_guardadas->fetch_assoc()): ?>
                    <div class="direccion-guardada" onclick="usarDireccion(<?php echo $dir['id']; ?>)">
                        <p><strong><?php echo htmlspecialchars($dir['nombre_completo']); ?></strong></p>
                        <p><?php echo $dir['direccion']; ?></p>
                        <p><?php echo $dir['ciudad']; ?> - <?php echo $dir['codigo_postal']; ?></p>
                        <p data-key="telefono">Teléfono: <?php echo $dir['telefono']; ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label data-key="nombre_completo">Nombre completo (con apellidos)</label>
            <input type="text" name="nombre_completo" id="nombre_completo" placeholder="Ej: Antonio Alcázar Gallardo" required>

            <label data-key="direccion_completa">Dirección completa</label>
            <input type="text" name="direccion" id="direccion" placeholder="Calle, número, piso..." required>

            <label data-key="ciudad">Ciudad</label>
            <input type="text" name="ciudad" id="ciudad" placeholder="Ciudad" required>

            <label data-key="codigo_postal">Código Postal</label>
            <input type="text" name="codigo_postal" id="codigo_postal" placeholder="Código Postal" required>

            <label data-key="telefono">Teléfono</label>
            <input type="tel" name="telefono" id="telefono" placeholder="Teléfono de contacto" required>

            <button type="submit" class="cta-button" data-key="confirmar_compra">Confirmar Compra</button>
        </form>

        <div style="text-align: center; margin-top: 15px;">
            <a href="ver_carrito.php" data-key="volver_carrito">Volver al Carrito</a>
        </div>
    </div>
</section>

<footer><p data-key="derechos">© Celestial</p></footer>

<script src="auth.js"></script>
<script src="traductor.js"></script>
<script src="oscuro.js"></script>
<script>
function usarDireccion(id) {
    fetch('obtener_direccion.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('nombre_completo').value = data.nombre_completo;
                document.getElementById('direccion').value = data.direccion;
                document.getElementById('ciudad').value = data.ciudad;
                document.getElementById('codigo_postal').value = data.codigo_postal;
                document.getElementById('telefono').value = data.telefono;
            }
        });
}
</script>
</body>
</html>
