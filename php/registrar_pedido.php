<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/Pedido.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descripcion = trim($_POST['descripcion'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $producto_id = intval($_POST['producto_id'] ?? 0);
    $unidades = intval($_POST['unidades'] ?? 1);
    $observaciones = trim($_POST['observaciones'] ?? '');

    if ($descripcion && $tipo && $producto_id && $unidades > 0) {
        // Verificar que el producto exista
        $stmtProd = $conn->prepare("SELECT nombre FROM productos WHERE id = ?");
        $stmtProd->bind_param('i', $producto_id);
        $stmtProd->execute();
        $stmtProd->bind_result($nombre_producto);
        $existe = $stmtProd->fetch();
        $stmtProd->close();
        if (!$existe) {
            echo '<p style="color:red;">El producto seleccionado no existe.</p>';
            $conn->close();
            exit;
        }
        // Insertar el pedido
        $stmt = $conn->prepare("INSERT INTO pedidos (descripcion, tipo, producto_id, unidades, observaciones) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssis', $descripcion, $tipo, $producto_id, $unidades, $observaciones);
        if ($stmt->execute()) {
            echo '<p style="color:green;">¡Pedido registrado correctamente para el producto: <b>' . htmlspecialchars($nombre_producto) . '</b>!</p>';
        } else {
            echo '<p style="color:red;">Error al registrar el pedido: ' . $conn->error . '</p>';
        }
        $stmt->close();
    } else {
        echo '<p style="color:red;">Todos los campos obligatorios deben estar completos y válidos.</p>';
    }
}
$conn->close();
?>
