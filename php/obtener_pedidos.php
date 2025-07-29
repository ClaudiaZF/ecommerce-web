<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/Pedido.php';

$sql = "SELECT p.id, p.descripcion, p.tipo, p.producto_id, pr.nombre as producto_nombre, p.unidades, p.observaciones, p.fecha FROM pedidos p LEFT JOIN productos pr ON p.producto_id = pr.id ORDER BY p.fecha DESC";
$result = $conn->query($sql);
$pedidos = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pedidos[] = $row;
    }
}
header('Content-Type: application/json');
echo json_encode($pedidos);
$conn->close();
?>
