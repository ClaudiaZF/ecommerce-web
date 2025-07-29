<?php
$host = 'localhost';
$usuario = 'admin';
$contrasena = 'root';
$base_datos = 'ecommerce';

$conn = new mysqli($host, $usuario, $contrasena, $base_datos);

if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}
?>
