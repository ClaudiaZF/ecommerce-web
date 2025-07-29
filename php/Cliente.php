<?php
class Cliente {
    public $id;
    public $nombre;
    public $email;
    public $telefono;
    public $direccion;

    public function __construct($id, $nombre, $email, $telefono, $direccion) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->telefono = $telefono;
        $this->direccion = $direccion;
    }

    // Buscar cliente por email
    public static function buscarPorEmail($conn, $email) {
        $stmt = $conn->prepare("SELECT id, nombre, email, telefono, direccion FROM clientes WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return new Cliente($row['id'], $row['nombre'], $row['email'], $row['telefono'], $row['direccion']);
        }
        return null;
    }

    // Crear cliente y devolver el id
    public static function crear($conn, $nombre, $email, $telefono, $direccion) {
        $stmt = $conn->prepare("INSERT INTO clientes (nombre, email, telefono, direccion) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $nombre, $email, $telefono, $direccion);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
        return $id;
    }
}
?>
