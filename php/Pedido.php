<?php
class Pedido {
    public $id;
    public $descripcion;
    public $tipo;
    public $producto_id;
    public $unidades;
    public $observaciones;

    public function __construct($row) {
        $this->id = $row['id'] ?? null;
        $this->descripcion = $row['descripcion'];
        $this->tipo = $row['tipo'];
        $this->producto_id = $row['producto_id'];
        $this->unidades = $row['unidades'];
        $this->observaciones = $row['observaciones'] ?? '';
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'descripcion' => $this->descripcion,
            'tipo' => $this->tipo,
            'producto_id' => $this->producto_id,
            'unidades' => $this->unidades,
            'observaciones' => $this->observaciones
        ];
    }

    public static function buscar($conn, $criterios = []) {
        $sql = "SELECT * FROM pedidos WHERE 1=1";
        $params = [];
        if (!empty($criterios['tipo'])) {
            $sql .= " AND tipo = ?";
            $params[] = $criterios['tipo'];
        }
        if (!empty($criterios['producto_id'])) {
            $sql .= " AND producto_id = ?";
            $params[] = $criterios['producto_id'];
        }
        if (!empty($criterios['descripcion'])) {
            $sql .= " AND descripcion LIKE ?";
            $params[] = '%' . $criterios['descripcion'] . '%';
        }
        $stmt = $conn->prepare($sql);
        if ($params) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $pedidos = [];
        while ($row = $result->fetch_assoc()) {
            $pedidos[] = new Pedido($row);
        }
        return $pedidos;
    }
}
?>
