<?php
class Compra {
    public $id;
    public $cliente_id;
    public $fecha;
    public $total;
    public $productos; // array de productos comprados

    public function __construct($id, $cliente_id, $fecha, $total, $productos = []) {
        $this->id = $id;
        $this->cliente_id = $cliente_id;
        $this->fecha = $fecha;
        $this->total = $total;
        $this->productos = $productos;
    }
}
?>
