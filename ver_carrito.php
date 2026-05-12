<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

$usuario_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM carrito WHERE usuario_id = $usuario_id");
$total = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Mi Carrito - Celestial</title>
    <style>
        .carrito-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .carrito-item {
            display: flex;
            align-items: center;
            gap: 20px;
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .carrito-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        .carrito-info {
            flex: 1;
        }
        .carrito-info h3 {
            margin: 0 0 5px 0;
            color: #333;
        }
        .carrito-info p {
            margin: 0;
            color: #666;
        }
        .carrito-precio {
            font-weight: bold;
            color: #333;
            font-size: 1.2em;
        }
        .carrito-eliminar {
            background: #ff4444;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .carrito-total {
            text-align: right;
            font-size: 1.5em;
            margin: 20px 0;
            padding: 15px;
            background: white;
            border-radius: 10px;
        }
        .botones-accion {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 20px;
        }
        body.modo-oscuro .carrito-item {
            background: #2d2d44;
        }
        body.modo-oscuro .carrito-info h3 {
            color: white;
        }
        body.modo-oscuro .carrito-info p {
            color: #ccc;
        }
        body.modo-oscuro .carrito-precio {
            color: white;
        }
        body.modo-oscuro .carrito-total {
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
    </nav>
</header>

<section class="hero">
    <div class="carrito-container">
        <h2 style="color: white; text-align: center;">Mi Carrito</h2>
        
        <?php if ($result->num_rows == 0): ?>
            <div class="carrito-item" style="justify-content: center;">
                <p>Tu carrito está vacío</p>
            </div>
            <div class="botones-accion">
                <button class="cta-button" onclick="location.href='productos.html'" style="background: #7bd5ff; width: auto; padding: 12px 30px;">Ver Productos</button>
            </div>
        <?php else: ?>
            <?php while($item = $result->fetch_assoc()): 
                $total += $item['producto_precio'] * $item['cantidad'];
            ?>
                <div class="carrito-item" id="item-<?php echo $item['id']; ?>">
                    <img src="<?php echo $item['producto_imagen']; ?>" alt="<?php echo $item['producto_nombre']; ?>">
                    <div class="carrito-info">
                        <h3><?php echo $item['producto_nombre']; ?></h3>
                        <p>Cantidad: <?php echo $item['cantidad']; ?></p>
                    </div>
                    <div class="carrito-precio">
                        <?php echo number_format($item['producto_precio'] * $item['cantidad'], 2); ?> €
                    </div>
                    <button class="carrito-eliminar" onclick="eliminarDelCarrito(<?php echo $item['id']; ?>)">Eliminar</button>
                </div>
            <?php endwhile; ?>
            
            <div class="carrito-total">
                Total: <?php echo number_format($total, 2); ?> €
            </div>
            
            <div class="botones-accion">
                <button class="cta-button" onclick="location.href='checkout.php'" style="background: #00C851; width: auto; padding: 12px 30px;">Finalizar Compra</button>
                <button class="cta-button" onclick="location.href='productos.html'" style="background: #7bd5ff; width: auto; padding: 12px 30px;">Seguir Comprando</button>
            </div>
        <?php endif; ?>
    </div>
</section>

<footer><p>© Celestial</p></footer>

<script src="auth.js"></script>
<script src="traductor.js"></script>
<script src="oscuro.js"></script>
<script>
function eliminarDelCarrito(id) {
    if (confirm('¿Eliminar producto del carrito?')) {
        fetch('eliminar_carrito.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('item-' + id).remove();
                location.reload();
            }
        });
    }
}
</script>
</body>
</html>
