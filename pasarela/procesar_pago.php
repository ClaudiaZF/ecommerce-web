<?php
// Procesamiento super simple del formulario de pago
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['cardName'] ?? '';
    $numero = $_POST['cardNumber'] ?? '';
    $expiracion = $_POST['expiry'] ?? '';
    $cvv = $_POST['cvv'] ?? '';

    // Simular cliente_id (en producción obtén de sesión)
    $cliente_id = 1;
    $monto = 0;
    $metodo = 'tarjeta';
    $referencia = substr($numero, -4);

    require_once '../php/conexion.php';
    require_once '../php/Pago.php';
    require_once '../php/Compra.php';

    // Calcular el total del carrito
    $productos = [];
    $res = $conn->query("SELECT p.id, p.precio, c.cantidad FROM carrito c JOIN productos p ON c.producto_id = p.id WHERE c.cliente_id = $cliente_id");
    while ($row = $res->fetch_assoc()) {
        $productos[] = $row;
        $monto += $row['precio'] * $row['cantidad'];
    }

    $pago = new Pago($conn);
    $pago->registrarPago($cliente_id, $monto, $metodo, $referencia);

    // Registrar la compra
    $compra = new Compra($conn);
    $compra_id = $compra->registrarCompra($cliente_id, $monto);
    foreach ($productos as $prod) {
        $compra->agregarDetalle($compra_id, $prod['id'], $prod['cantidad'], $prod['precio']);
    }

    // Vaciar el carrito
    $conn->query("DELETE FROM carrito WHERE cliente_id = $cliente_id");

    echo '<h2>¡Pago y compra registrados!</h2>';
    echo '<p>Nombre en la tarjeta: ' . htmlspecialchars($nombre) . '</p>';
    echo '<p>Monto pagado: ' . number_format($monto, 0, ",", ".") . '</p>';
    echo '<a href="../carrito.html">Volver al carrito</a>';
} else {
    echo '<h2>Acceso inválido</h2>';
}
?>
