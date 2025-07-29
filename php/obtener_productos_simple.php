<?php
require_once __DIR__ . '/conexion.php';
$sql = "SELECT id, nombre FROM productos ORDER BY nombre ASC";
$result = $conn->query($sql);
$productos = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }
}
header('Content-Type: application/json');
echo json_encode($productos);
$conn->close();
?>
