<?php
class Pago {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function registrarPago($cliente_id, $monto, $metodo, $referencia = null) {
        $stmt = $this->conn->prepare("INSERT INTO pagos (cliente_id, monto, metodo, referencia, fecha) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param('idss', $cliente_id, $monto, $metodo, $referencia);
        return $stmt->execute();
    }
}
?>
