<?php
ini_set('session.gc_maxlifetime', 7200);
session_set_cookie_params(7200);
session_start();
header('Content-Type: application/json');
require_once 'conexion.php';

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), session_id(), time() + 7200, "/");
}

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 7200)) {
    session_unset();
    session_destroy();
    echo json_encode(['productos' => [], 'expirado' => true]);
    exit;
} else {
    $_SESSION['LAST_ACTIVITY'] = time();
}

$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$productos = [];

if (!empty($carrito)) {
    $ids = implode(',', array_map('intval', array_keys($carrito)));
    $sql = "SELECT * FROM productos WHERE id IN ($ids)";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $row['cantidad'] = isset($carrito[$id]) ? $carrito[$id] : 1;
        $productos[] = $row;
    }
}

echo json_encode(['productos' => $productos]);
