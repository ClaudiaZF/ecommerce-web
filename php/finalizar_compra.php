<?php
require_once 'conexion.php';
require_once 'Cliente.php';
require_once 'Compra.php';
session_start();
header('Content-Type: application/json');

$nombre = $_POST['cliente_nombre'];
$email = $_POST['cliente_email'];
$telefono = $_POST['cliente_telefono'];
$direccion = $_POST['cliente_direccion'];

$cliente = Cliente::buscarPorEmail($conn, $email);
if ($cliente) {
    $cliente_id = $cliente->id;
} else {
    $cliente_id = Cliente::crear($conn, $nombre, $email, $telefono, $direccion);
}

$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$total = 0;
$productos = [];
if (!empty($carrito)) {
    $ids = implode(',', array_map('intval', array_keys($carrito)));
    $sql = "SELECT * FROM productos WHERE id IN ($ids)";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $cantidad = $carrito[$id];
        $row['cantidad'] = $cantidad;
        $productos[] = $row;
        $precio = $row['descuento'] > 0 ? $row['precio'] * (1 - $row['descuento']/100) : $row['precio'];
        $total += $precio * $cantidad;
    }
}

$stmt = $conn->prepare("INSERT INTO compras (cliente_id, fecha, total) VALUES (?, NOW(), ?)");
$stmt->bind_param('id', $cliente_id, $total);
$stmt->execute();
$compra_id = $stmt->insert_id;
$stmt->close();

$stmt = $conn->prepare("INSERT INTO compra_productos (compra_id, producto_id, cantidad) VALUES (?, ?, ?)");
foreach ($productos as $p) {
    $stmt->bind_param('iii', $compra_id, $p['id'], $p['cantidad']);
    $stmt->execute();
}
$stmt->close();

unset($_SESSION['carrito']);

echo json_encode(['success' => true, 'mensaje' => '¡Compra registrada exitosamente!', 'compra_id' => $compra_id]);
?>
