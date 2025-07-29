<?php
require_once 'conexion.php';

$nombre = trim($_POST['nombre']);
$descripcion = trim($_POST['descripcion']);
$precio = floatval($_POST['precio']);

if ($nombre && $descripcion && $precio > 0) {
    // Verificar si el producto ya existe por nombre
    $stmt = $conn->prepare("SELECT id FROM productos WHERE nombre = ? LIMIT 1");
    $stmt->bind_param('s', $nombre);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        // Si existe, actualizar el producto
        $id = $row['id'];
        $stmt->close();
        $stmt = $conn->prepare("UPDATE productos SET descripcion = ?, precio = ? WHERE id = ?");
        $stmt->bind_param('sdi', $descripcion, $precio, $id);
        if ($stmt->execute()) {
            echo "<div style='color:#1976d2;font-weight:bold;text-align:center;margin-top:40px;'>Producto actualizado correctamente.</div>";
        } else {
            echo "<div style='color:#d32f2f;font-weight:bold;text-align:center;margin-top:40px;'>Error al actualizar el producto.</div>";
        }
        $stmt->close();
    } else {
        // Si no existe, crear el producto
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio) VALUES (?, ?, ?)");
        $stmt->bind_param('ssd', $nombre, $descripcion, $precio);
        if ($stmt->execute()) {
            echo "<div style='color:#388e3c;font-weight:bold;text-align:center;margin-top:40px;'>Producto registrado correctamente.</div>";
        } else {
            echo "<div style='color:#d32f2f;font-weight:bold;text-align:center;margin-top:40px;'>Error al registrar el producto.</div>";
        }
        $stmt->close();
    }
} else {
    echo "<div style='color:#d32f2f;font-weight:bold;text-align:center;margin-top:40px;'>Datos inválidos.</div>";
}
?>
<div style="text-align:center; margin-top:24px;">
    <a href="../agregar_producto.html" class="btn-pedido-nav">Volver</a>
    <a href="../index.php" class="btn-pedido-nav">Inicio</a>
</div>
