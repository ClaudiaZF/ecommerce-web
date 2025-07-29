<?php
require_once 'conexion.php';

$nombre = trim($_POST['nombre']);
$email = trim($_POST['email']);
$telefono = trim($_POST['telefono']);
$direccion = trim($_POST['direccion']);

if ($nombre && $email) {
    // Verificar si el cliente ya existe por email
    $stmt = $conn->prepare("SELECT id FROM clientes WHERE email = ? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        // Si existe, actualizar el cliente
        $id = $row['id'];
        $stmt->close();
        $stmt = $conn->prepare("UPDATE clientes SET nombre = ?, telefono = ?, direccion = ? WHERE id = ?");
        $stmt->bind_param('sssi', $nombre, $telefono, $direccion, $id);
        if ($stmt->execute()) {
            echo "<div style='color:#1976d2;font-weight:bold;text-align:center;margin-top:40px;'>Cliente actualizado correctamente.</div>";
        } else {
            echo "<div style='color:#d32f2f;font-weight:bold;text-align:center;margin-top:40px;'>Error al actualizar el cliente.</div>";
        }
        $stmt->close();
    } else {
        // Si no existe, crear el cliente
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO clientes (nombre, email, telefono, direccion) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $nombre, $email, $telefono, $direccion);
        if ($stmt->execute()) {
            echo "<div style='color:#388e3c;font-weight:bold;text-align:center;margin-top:40px;'>Cliente registrado correctamente.</div>";
        } else {
            echo "<div style='color:#d32f2f;font-weight:bold;text-align:center;margin-top:40px;'>Error al registrar el cliente.</div>";
        }
        $stmt->close();
    }
} else {
    echo "<div style='color:#d32f2f;font-weight:bold;text-align:center;margin-top:40px;'>Datos inválidos.</div>";
}
?>
<div style="text-align:center; margin-top:24px;">
    <a href="../agregar_cliente.html" class="btn-pedido-nav">Volver</a>
    <a href="../index.php" class="btn-pedido-nav">Inicio</a>
</div>
