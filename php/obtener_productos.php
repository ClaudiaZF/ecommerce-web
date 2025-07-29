<?php
require_once __DIR__ . '/conexion.php';

class Producto {
    public $id;
    public $nombre;
    public $descripcion;
    public $precio;
    public $descuento;

    public function __construct($row) {
        $this->id = $row['id'];
        $this->nombre = $row['nombre'];
        $this->descripcion = $row['descripcion'];
        $this->precio = $row['precio'];
        $this->descuento = isset($row['descuento']) ? $row['descuento'] : 0;
    }
    public function toArray() {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio' => $this->precio,
            'descuento' => $this->descuento
        ];
    }
}

$sql = "SELECT * FROM productos";
$result = $conn->query($sql);
$productos = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $producto = new Producto($row);
        $productos[] = $producto->toArray();
    }
}
header('Content-Type: application/json');
echo json_encode($productos);
$conn->close();
?>
