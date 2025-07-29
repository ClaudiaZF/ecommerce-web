<?php
ini_set('session.gc_maxlifetime', 7200);
session_set_cookie_params(7200);
session_start();
header('Content-Type: application/json');
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), session_id(), time() + 7200, "/");
}
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 7200)) {
    session_unset();
    session_destroy();
    echo json_encode(['success' => false, 'expirado' => true]);
    exit;
} else {
    $_SESSION['LAST_ACTIVITY'] = time();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto_id = $_POST['producto_id'];
    if (isset($_SESSION['carrito'][$producto_id])) {
        unset($_SESSION['carrito'][$producto_id]);
    }
    echo json_encode(['success' => true, 'carrito' => $_SESSION['carrito']]);
    exit;
}

echo json_encode(['success' => false]);
exit;
