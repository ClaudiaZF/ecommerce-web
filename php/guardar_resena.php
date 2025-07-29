<?php
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['flash'] = 'Token de seguridad inválido. Intenta nuevamente.';
        header('Location: ../index.html');
        exit;
    }
}

$usuario = $_POST['usuario'];
$calificacion = $_POST['calificacion'];
$comentario = $_POST['comentario'];
$producto_id = $_POST['producto_id'];

$sql = "INSERT INTO resenas (producto_id, usuario, calificacion, comentario, fecha) VALUES (?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);

$stmt->bind_param('isis', $producto_id, $usuario, $calificacion, $comentario);

if ($stmt->execute()) {
    $_SESSION['flash'] = '¡Reseña guardada exitosamente!';
    header('Location: ../index.html');
    exit;
} else {
    $_SESSION['flash'] = 'Error al guardar la reseña: ' . $stmt->error . ' | producto_id enviado: ' . $producto_id;
    header('Location: ../index.html');
    exit;
}

$stmt->close();
$conn->close();
?>
