<?php
require_once __DIR__ . '/conexions.php';

class SystemAdminModel {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->conectar();
    }

    public function obtenerTodos() {
        $sql = "SELECT accountId, userName, documentCategory, documentIdentifier, emailAddress, hashedPassword, state
                FROM USER_ACCOUNT
                WHERE userCategory = 'System Administrator'
                ORDER BY userName";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function obtenerPorId($accountId) {
        $sql = "SELECT accountId, userName, documentCategory, documentIdentifier, emailAddress, hashedPassword, state
                FROM USER_ACCOUNT
                WHERE accountId = ? AND userCategory = 'System Administrator'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $accountId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function existeUsername($userName, $excludeId = null) {
        $sql = "SELECT accountId FROM USER_ACCOUNT
                WHERE userName = ? AND userCategory = 'System Administrator'";
        if ($excludeId !== null) {
            $sql .= " AND accountId <> ?";
        }
        $stmt = $this->conn->prepare($sql);
        if ($excludeId !== null) {
            $stmt->bind_param('si', $userName, $excludeId);
        } else {
            $stmt->bind_param('s', $userName);
        }
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function existeDocumento($documentIdentifier, $excludeId = null) {
        $sql = "SELECT accountId FROM USER_ACCOUNT
                WHERE documentIdentifier = ? AND userCategory = 'System Administrator'";
        if ($excludeId !== null) {
            $sql .= " AND accountId <> ?";
        }
        $stmt = $this->conn->prepare($sql);
        if ($excludeId !== null) {
            $stmt->bind_param('si', $documentIdentifier, $excludeId);
        } else {
            $stmt->bind_param('s', $documentIdentifier);
        }
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function crear($data) {
        $sql = "INSERT INTO USER_ACCOUNT
                (userName, documentCategory, documentIdentifier, emailAddress, hashedPassword, state, userCategory)
                VALUES (?, ?, ?, ?, ?, ?, 'System Administrator')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'ssssss',
            $data['userName'],
            $data['documentCategory'],
            $data['documentIdentifier'],
            $data['emailAddress'],
            $data['hashedPassword'],
            $data['state']
        );
        return $stmt->execute();
    }

    public function actualizar($accountId, $data) {
        if (!empty($data['hashedPassword'])) {
            $sql = "UPDATE USER_ACCOUNT
                    SET userName = ?, documentCategory = ?, documentIdentifier = ?, emailAddress = ?, hashedPassword = ?
                    WHERE accountId = ? AND userCategory = 'System Administrator'";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param(
                'sssssi',
                $data['userName'],
                $data['documentCategory'],
                $data['documentIdentifier'],
                $data['emailAddress'],
                $data['hashedPassword'],
                $accountId
            );
        } else {
            $sql = "UPDATE USER_ACCOUNT
                    SET userName = ?, documentCategory = ?, documentIdentifier = ?, emailAddress = ?
                    WHERE accountId = ? AND userCategory = 'System Administrator'";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param(
                'ssssi',
                $data['userName'],
                $data['documentCategory'],
                $data['documentIdentifier'],
                $data['emailAddress'],
                $accountId
            );
        }
        return $stmt->execute();
    }

    public function cambiarEstado($accountId, $nuevoEstado) {
        $sql = "UPDATE USER_ACCOUNT SET state = ?
                WHERE accountId = ? AND userCategory = 'System Administrator'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('si', $nuevoEstado, $accountId);
        return $stmt->execute();
    }
}
