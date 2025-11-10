<?php
require_once __DIR__ . '/../Model/conexions.php';

class CodeudorModel {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->conectar();
    }

    // Crear nuevo codeudor
    public function crear($data) {
        $sql = "INSERT INTO PERSON (
                    personCategory, fullName, documentCategory, documentIdentifier,
                    address, phoneNumber, emailAddress
                ) VALUES ('Codeudor', ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $nombreCompleto = $data['nombre'] . ' ' . $data['apellido'];
        return $stmt->execute([
            $nombreCompleto,
            $data['tipo_doc'],
            $data['numero_doc'],
            $data['direccion'],
            $data['telefono'],
            $data['email']
        ]);
    }

    // Obtener todos los codeudores
    public function obtenerTodos() {
        $sql = "SELECT * FROM PERSON WHERE personCategory = 'Codeudor'";
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    // Obtener uno por documento
    public function obtenerPorDocumento($documento) {
        $stmt = $this->conn->prepare("SELECT * FROM PERSON WHERE documentIdentifier = ? AND personCategory = 'Codeudor'");
        $stmt->bind_param("s", $documento);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Actualizar
    public function actualizar($data) {
        $sql = "UPDATE PERSON SET fullName=?, documentCategory=?, address=?, phoneNumber=?, emailAddress=? 
                WHERE documentIdentifier=? AND personCategory='Codeudor'";
        $stmt = $this->conn->prepare($sql);
        $nombreCompleto = $data['nombre'] . ' ' . $data['apellido'];
        $stmt->bind_param("ssssss", $nombreCompleto, $data['tipo_doc'], $data['direccion'], $data['telefono'], $data['email'], $data['numero_doc']);
        return $stmt->execute();
    }

    // Eliminar
    public function eliminar($documento) {
        $stmt = $this->conn->prepare("DELETE FROM PERSON WHERE documentIdentifier=? AND personCategory='Codeudor'");
        $stmt->bind_param("s", $documento);
        return $stmt->execute();
    }
}
