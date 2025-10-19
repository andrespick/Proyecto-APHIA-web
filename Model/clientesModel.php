<?php
require_once __DIR__ . '/../Model/conexions.php';

class ClienteModel {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->conectar();
    }

    // Crear cliente
    public function crear($data) {
        $sql = "INSERT INTO PERSON (
                    personCategory, fullName, documentCategory, documentIdentifier,
                    address, phoneNumber, emailAddress
                ) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $nombreCompleto = $data['nombre'] . ' ' . $data['apellido'];
        return $stmt->execute([
            'Cliente',
            $nombreCompleto,
            $data['tipo_doc'],
            $data['numero_doc'],
            $data['direccion'],
            $data['telefono'],
            $data['email']
        ]);
    }

    // Obtener todos los clientes
    public function obtenerTodos() {
        $sql = "SELECT * FROM PERSON WHERE personCategory = 'Cliente' ORDER BY personId DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Buscar cliente por documento
    public function buscarPorDocumento($numero_doc) {
        $sql = "SELECT * FROM PERSON WHERE documentIdentifier = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $numero_doc);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Actualizar cliente
    public function actualizar($data) {
        $sql = "UPDATE PERSON 
                SET fullName = ?, documentCategory = ?, address = ?, phoneNumber = ?, emailAddress = ?
                WHERE documentIdentifier = ?";
        $stmt = $this->conn->prepare($sql);
        $nombreCompleto = $data['nombre'] . ' ' . $data['apellido'];
        $stmt->bind_param(
            "ssssss",
            $nombreCompleto,
            $data['tipo_doc'],
            $data['direccion'],
            $data['telefono'],
            $data['email'],
            $data['numero_doc']
        );
        return $stmt->execute();
    }

    // Eliminar cliente
    public function eliminar($numero_doc) {
        $sql = "DELETE FROM PERSON WHERE documentIdentifier = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $numero_doc);
        return $stmt->execute();
    }
}
?>
