<?php
require_once __DIR__ . '/conexions.php';

class SystemAdminModel {
    private $conn;
    private $allowedCategories = ['System Administrator', 'Advisor', 'administrator'];
    private $allowedCategoriesSql;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->conectar();
        $this->allowedCategoriesSql = $this->buildAllowedCategoriesSql();
    }

    private function buildAllowedCategoriesSql() {
        $escaped = array_map(function ($category) {
            return $this->conn->real_escape_string($category);
        }, $this->allowedCategories);
        return "('" . implode("','", $escaped) . "')";
    }

    private function getAllowedCategoriesSql() {
        if ($this->allowedCategoriesSql === null) {
            $this->allowedCategoriesSql = $this->buildAllowedCategoriesSql();
        }
        return $this->allowedCategoriesSql;
    }

    public function obtenerCategoriasDisponibles() {
        return $this->allowedCategories;
    }

    public function esCategoriaValida($categoria) {
        return in_array($categoria, $this->allowedCategories, true);
    }

    public function obtenerTodos() {
        $sql = "SELECT accountId, userName, documentCategory, documentIdentifier, emailAddress, hashedPassword, state, userCategory
                FROM USER_ACCOUNT
                WHERE userCategory IN " . $this->getAllowedCategoriesSql() . "
                ORDER BY userCategory, userName";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function obtenerPorId($accountId) {
        $sql = "SELECT accountId, userName, documentCategory, documentIdentifier, emailAddress, hashedPassword, state, userCategory
                FROM USER_ACCOUNT
                WHERE accountId = ? AND userCategory IN " . $this->getAllowedCategoriesSql();
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $accountId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function existeUsername($userName, $excludeId = null) {
        $sql = "SELECT accountId FROM USER_ACCOUNT
                WHERE userName = ? AND userCategory IN " . $this->getAllowedCategoriesSql();
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
                WHERE documentIdentifier = ? AND userCategory IN " . $this->getAllowedCategoriesSql();
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
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'sssssss',
            $data['userName'],
            $data['documentCategory'],
            $data['documentIdentifier'],
            $data['emailAddress'],
            $data['hashedPassword'],
            $data['state'],
            $data['userCategory']
        );
        return $stmt->execute();
    }

    public function actualizar($accountId, $data) {
        if (!empty($data['hashedPassword'])) {
            $sql = "UPDATE USER_ACCOUNT
                    SET userName = ?, documentCategory = ?, documentIdentifier = ?, emailAddress = ?, hashedPassword = ?, userCategory = ?
                    WHERE accountId = ? AND userCategory IN " . $this->getAllowedCategoriesSql();
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param(
                'ssssssi',
                $data['userName'],
                $data['documentCategory'],
                $data['documentIdentifier'],
                $data['emailAddress'],
                $data['hashedPassword'],
                $data['userCategory'],
                $accountId
            );
        } else {
            $sql = "UPDATE USER_ACCOUNT
                    SET userName = ?, documentCategory = ?, documentIdentifier = ?, emailAddress = ?, userCategory = ?
                    WHERE accountId = ? AND userCategory IN " . $this->getAllowedCategoriesSql();
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param(
                'sssssi',
                $data['userName'],
                $data['documentCategory'],
                $data['documentIdentifier'],
                $data['emailAddress'],
                $data['userCategory'],
                $accountId
            );
        }
        return $stmt->execute();
    }

    public function cambiarEstado($accountId, $nuevoEstado) {
        $sql = "UPDATE USER_ACCOUNT SET state = ?
                WHERE accountId = ? AND userCategory IN " . $this->getAllowedCategoriesSql();
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('si', $nuevoEstado, $accountId);
        return $stmt->execute();
    }
}
