<?php
require_once 'conexion.php';

class Resena {
    public $id;
    public $producto_id;
    public $usuario;
    public $calificacion;
    public $comentario;
    public $fecha;

    public function __construct($row) {
        $this->id = $row['id'];
        $this->producto_id = $row['producto_id'];
        $this->usuario = $row['usuario'];
        $this->calificacion = $row['calificacion'];
        $this->comentario = $row['comentario'];
        $this->fecha = $row['fecha'];
    }
    public function toArray() {
        return [
            'id' => $this->id,
            'producto_id' => $this->producto_id,
            'usuario' => $this->usuario,
            'calificacion' => $this->calificacion,
            'comentario' => $this->comentario,
            'fecha' => $this->fecha
        ];
    }
}

$producto_id = isset($_GET['producto_id']) ? intval($_GET['producto_id']) : 0;
$sql = "SELECT * FROM resenas WHERE producto_id = $producto_id ORDER BY fecha DESC";
$result = $conn->query($sql);
$resenas = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $resena = new Resena($row);
        $resenas[] = $resena->toArray();
    }
}
header('Content-Type: application/json');
echo json_encode($resenas);
$conn->close();
?>
