<?php
session_start();
require_once 'conexion.php';

$compra_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($compra_id <= 0) {
    echo '<h2>Compra no encontrada</h2>';
    exit;
}

$sql = "SELECT c.id, c.fecha, c.total, cl.nombre, cl.email, cl.telefono, cl.direccion FROM compras c JOIN clientes cl ON c.cliente_id = cl.id WHERE c.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $compra_id);
$stmt->execute();
$result = $stmt->get_result();
$compra = $result->fetch_assoc();
$stmt->close();

if (!$compra) {
    echo '<h2>Compra no encontrada</h2>';
    exit;
}

$sql = "SELECT p.nombre, cp.cantidad, p.precio, p.descuento FROM compra_productos cp JOIN productos p ON cp.producto_id = p.id WHERE cp.compra_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $compra_id);
$stmt->execute();
$result = $stmt->get_result();
$productos = [];
while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
}
$stmt->close();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Compra</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        .detalle-compra-container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.08);
            padding: 32px 24px 24px 24px;
        }
        .detalle-titulo {
            font-size: 2rem;
            color: #1976d2;
            margin-bottom: 18px;
            text-align: center;
        }
        .detalle-cliente {
            margin-bottom: 18px;
            background: #f7fafd;
            border-radius: 8px;
            padding: 14px 18px;
        }
        .detalle-productos {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .detalle-productos th, .detalle-productos td {
            padding: 10px 7px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
        }
        .detalle-productos th {
            background: #e3eafc;
            color: #1976d2;
        }
        .detalle-total {
            text-align: right;
            font-size: 1.2rem;
            color: #388e3c;
            font-weight: bold;
        }
        @media (max-width: 600px) {
            .detalle-compra-container { padding: 12px 2vw; }
            .detalle-titulo { font-size: 1.2rem; }
            .detalle-productos th, .detalle-productos td { padding: 7px 2px; font-size: 0.95rem; }
        }
    </style>
</head>
<body style="background:#f3f6fa;">
    <div class="detalle-compra-container">
        <div class="detalle-titulo">🧾 Detalle de la Compra</div>
        <div class="detalle-cliente">
            <b>Cliente:</b> <?php echo htmlspecialchars($compra['nombre']); ?><br>
            <b>Email:</b> <?php echo htmlspecialchars($compra['email']); ?><br>
            <b>Teléfono:</b> <?php echo htmlspecialchars($compra['telefono']); ?><br>
            <b>Dirección:</b> <?php echo htmlspecialchars($compra['direccion']); ?><br>
            <b>Fecha:</b> <?php echo date('d/m/Y H:i', strtotime($compra['fecha'])); ?>
        </div>
        <table class="detalle-productos">
            <thead>
                <tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th></tr>
            </thead>
            <tbody>
            <?php $total = 0; foreach($productos as $p): 
                $precio = $p['descuento'] > 0 ? $p['precio'] * (1 - $p['descuento']/100) : $p['precio'];
                $subtotal = $precio * $p['cantidad'];
                $total += $subtotal;
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                    <td><?php echo number_format($precio, 0, ',', '.'); ?></td>
                    <td><?php echo $p['cantidad']; ?></td>
                    <td><?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="detalle-total">Total: $<?php echo number_format($total, 0, ',', '.'); ?></div>
        <div style="text-align:center;margin-top:24px;">
            <a href="../index.php" style="color:#1976d2;text-decoration:underline;font-weight:bold;">&larr; Volver a la tienda</a>
        </div>
    </div>
</body>
</html>
