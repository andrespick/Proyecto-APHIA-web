<?php
require_once __DIR__ . '/../Model/conexions.php';

class PropertyModel {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->conectar();
    }

    /** Lista de propietarios para el combo (solo personCategory='Owner') */
    public function obtenerPropietarios() {
        $sql = "SELECT personId, fullName, documentIdentifier 
                FROM PERSON 
                WHERE personCategory='Owner'
                ORDER BY fullName ASC";
        $res = $this->conn->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /** Crear inmueble */
    public function crear($data) {
        $sql = "INSERT INTO PROPERTY 
                (address, city, registrationIdentifier, rentalValue, utilityContractIdentifiers, occupancyState, propertyType, ownerId)
                VALUES (?, ?, ?, NULLIF(?, ''), ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssssssi",
            $data['address'],
            $data['city'],
            $data['registrationIdentifier'],
            $data['rentalValue'],
            $data['utilityContractIdentifiers'],
            $data['occupancyState'],
            $data['propertyType'],
            $data['ownerId']
        );
        return $stmt->execute();
    }

    /** Actualizar inmueble */
    public function actualizar($data) {
        $sql = "UPDATE PROPERTY
                SET address=?, city=?, registrationIdentifier=?, rentalValue=NULLIF(?, ''), utilityContractIdentifiers=?, occupancyState=?, propertyType=?, ownerId=?
                WHERE propertyId=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssssssii",
            $data['address'],
            $data['city'],
            $data['registrationIdentifier'],
            $data['rentalValue'],
            $data['utilityContractIdentifiers'],
            $data['occupancyState'],
            $data['propertyType'],
            $data['ownerId'],
            $data['propertyId']
        );
        return $stmt->execute();
    }

    /** Eliminar inmueble (borra también docs si hay FK con ON DELETE CASCADE) */
    public function eliminar($propertyId) {
        // Si no tienes FK cascade, descomenta estas dos líneas:
        // $stmtD = $this->conn->prepare("DELETE FROM FILE_RECORD WHERE propertyId=?");
        // $stmtD->bind_param("i", $propertyId); $stmtD->execute();

        $stmt = $this->conn->prepare("DELETE FROM PROPERTY WHERE propertyId=?");
        $stmt->bind_param("i", $propertyId);
        return $stmt->execute();
    }

    /** Obtener todos (con nombre del propietario) */
    public function obtenerTodos() {
        $sql = "SELECT p.propertyId, p.address, p.city, p.registrationIdentifier, p.rentalValue,
                       p.utilityContractIdentifiers, p.occupancyState, p.propertyType, p.ownerId,
                       o.fullName AS ownerName, o.documentIdentifier AS ownerDoc
                FROM PROPERTY p
                INNER JOIN PERSON o ON o.personId = p.ownerId
                ORDER BY p.propertyId DESC";
        $res = $this->conn->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /** Obtener uno por id */
    public function obtenerPorId($propertyId) {
        $stmt = $this->conn->prepare(
            "SELECT p.*, o.fullName AS ownerName, o.documentIdentifier AS ownerDoc
             FROM PROPERTY p
             INNER JOIN PERSON o ON o.personId = p.ownerId
             WHERE p.propertyId=?"
        );
        $stmt->bind_param("i", $propertyId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /** Documentos del inmueble */
    public function obtenerDocumentos($propertyId) {
        $stmt = $this->conn->prepare("SELECT fileId, filePath, uploadDate FROM FILE_RECORD WHERE propertyId=? ORDER BY uploadDate DESC");
        $stmt->bind_param("i", $propertyId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /** Guardar documento (ruta relativa) */
    public function guardarDocumento($propertyId, $rutaRelativa) {
        $stmt = $this->conn->prepare("INSERT INTO FILE_RECORD (propertyId, filePath, uploadDate) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $propertyId, $rutaRelativa);
        return $stmt->execute();
    }

    /** Eliminar documento por ruta (y bajar del FS) */
    public function eliminarDocumento($propertyId, $filePath) {
        $stmt = $this->conn->prepare("DELETE FROM FILE_RECORD WHERE propertyId=? AND filePath=?");
        $stmt->bind_param("is", $propertyId, $filePath);
        $ok = $stmt->execute();
        if ($ok) {
            $normalized = str_replace('\\', '/', $filePath);
            $normalized = preg_replace('#^(\./|../)+#', '', $normalized);
            $abs = dirname(__DIR__) . '/' . $normalized;
            if (is_file($abs)) {
                @unlink($abs);
            }
        }
        return $ok;
    }
}
