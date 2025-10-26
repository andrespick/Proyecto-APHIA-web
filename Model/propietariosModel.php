<?php
require_once __DIR__ . '/../Model/conexions.php';

class PropietarioModel {
    private $conn;

    public function __construct() {
        $database = new Conexion();
        $this->conn = $database->conectar();
    }

    // Crear propietario
    public function guardar($data) {
        $this->conn->begin_transaction();
        try {
            $fullName = trim($data['nombre'] . ' ' . $data['apellido']);
            $stmt = $this->conn->prepare("INSERT INTO PERSON 
                (personCategory, fullName, documentCategory, documentIdentifier, address, phoneNumber, emailAddress)
                VALUES ('Owner', ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $fullName, $data['tipo_doc'], $data['numero_doc'], $data['direccion'], $data['telefono'], $data['email']);
            $stmt->execute();
            $personId = $this->conn->insert_id;

            // Insertar cuenta bancaria si existe
            if (!empty($data['numero_cuenta'])) {
                $stmt2 = $this->conn->prepare("INSERT INTO BANK_ACCOUNT 
                    (personId, accountIdentifier, accountCategory, financialInstitution) VALUES (?, ?, ?, ?)");
                $stmt2->bind_param("isss", $personId, $data['numero_cuenta'], $data['tipo_cuenta'], $data['entidad_bancaria']);
                $stmt2->execute();
            }

            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    // Listar propietarios
    public function obtenerTodos() {
        $sql = "SELECT p.*, b.accountIdentifier, b.accountCategory, b.financialInstitution 
                FROM PERSON p 
                LEFT JOIN BANK_ACCOUNT b ON p.personId = b.personId 
                WHERE p.personCategory = 'Owner'
                ORDER BY p.fullName ASC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Obtener por documento
    public function obtenerPorDocumento($documentIdentifier) {
        $sql = "SELECT p.*, b.accountIdentifier, b.accountCategory, b.financialInstitution 
                FROM PERSON p 
                LEFT JOIN BANK_ACCOUNT b ON p.personId = b.personId 
                WHERE p.documentIdentifier = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $documentIdentifier);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Actualizar propietario y cuenta bancaria
    public function actualizar($data) {
        $this->conn->begin_transaction();
        try {
            $fullName = trim($data['nombre'] . ' ' . $data['apellido']);
            $stmt = $this->conn->prepare("UPDATE PERSON 
                SET fullName=?, documentCategory=?, address=?, phoneNumber=?, emailAddress=? 
                WHERE documentIdentifier=?");
            $stmt->bind_param("ssssss", $fullName, $data['tipo_doc'], $data['direccion'], $data['telefono'], $data['email'], $data['numero_doc']);
            $stmt->execute();

            // Obtener personId
            $stmt2 = $this->conn->prepare("SELECT personId FROM PERSON WHERE documentIdentifier=?");
            $stmt2->bind_param("s", $data['numero_doc']);
            $stmt2->execute();
            $personId = $stmt2->get_result()->fetch_assoc()['personId'];

            // Verificar si tiene cuenta bancaria
            $stmt3 = $this->conn->prepare("SELECT accountId FROM BANK_ACCOUNT WHERE personId=?");
            $stmt3->bind_param("i", $personId);
            $stmt3->execute();
            $hasAccount = $stmt3->get_result()->num_rows > 0;

            if ($hasAccount) {
                $stmt4 = $this->conn->prepare("UPDATE BANK_ACCOUNT 
                    SET accountIdentifier=?, accountCategory=?, financialInstitution=? WHERE personId=?");
                $stmt4->bind_param("sssi", $data['numero_cuenta'], $data['tipo_cuenta'], $data['entidad_bancaria'], $personId);
                $stmt4->execute();
            } else if (!empty($data['numero_cuenta'])) {
                $stmt5 = $this->conn->prepare("INSERT INTO BANK_ACCOUNT 
                    (personId, accountIdentifier, accountCategory, financialInstitution) VALUES (?, ?, ?, ?)");
                $stmt5->bind_param("isss", $personId, $data['numero_cuenta'], $data['tipo_cuenta'], $data['entidad_bancaria']);
                $stmt5->execute();
            }

            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    // 🔹 Eliminar propietario con control de dependencias
    public function eliminar($documentIdentifier) {
        try {
            $this->conn->begin_transaction();

            // 1) Obtener el personId
            $stmt = $this->conn->prepare("SELECT personId FROM PERSON WHERE documentIdentifier = ? AND personCategory = 'Owner'");
            $stmt->bind_param("s", $documentIdentifier);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows === 0) {
                $this->conn->rollback();
                return false;
            }
            $personId = $res->fetch_assoc()['personId'];

            // 2) Buscar acuerdos asociados
            $stmtA = $this->conn->prepare("SELECT agreementId FROM PROPERTY_AGREEMENT WHERE associatedPersonId = ?");
            $stmtA->bind_param("i", $personId);
            $stmtA->execute();
            $agreements = $stmtA->get_result()->fetch_all(MYSQLI_ASSOC);
            $agreementIds = array_map(fn($r) => $r['agreementId'], $agreements);

            if (!empty($agreementIds)) {
                $in = implode(',', array_map('intval', $agreementIds));
                $this->conn->query("DELETE FROM FINANCIAL_RECORD WHERE agreementId IN ($in)");
                $this->conn->query("DELETE FROM AGREEMENT_CO_DEBTOR WHERE agreementId IN ($in)");
                $this->conn->query("DELETE FROM PROPERTY_AGREEMENT WHERE agreementId IN ($in)");
            }

            // 3) Borrar si aparece como codeudor
            $stmtCD = $this->conn->prepare("DELETE FROM AGREEMENT_CO_DEBTOR WHERE coDebtorId = ?");
            $stmtCD->bind_param("i", $personId);
            $stmtCD->execute();

            // 4) Eliminar archivos asociados
            $stmtF = $this->conn->prepare("SELECT filePath FROM FILE_RECORD WHERE personId = ?");
            $stmtF->bind_param("i", $personId);
            $stmtF->execute();
            $files = $stmtF->get_result()->fetch_all(MYSQLI_ASSOC);
            foreach ($files as $f) {
                $path = __DIR__ . '/../' . ltrim($f['filePath'], '/');
                if (is_file($path)) {
                    @unlink($path);
                }
            }
            $stmtDelFiles = $this->conn->prepare("DELETE FROM FILE_RECORD WHERE personId = ?");
            $stmtDelFiles->bind_param("i", $personId);
            $stmtDelFiles->execute();

            // 5) Eliminar cuentas bancarias
            $stmtBank = $this->conn->prepare("DELETE FROM BANK_ACCOUNT WHERE personId = ?");
            $stmtBank->bind_param("i", $personId);
            $stmtBank->execute();

            // 6) Eliminar propiedades (puedes cambiar a UPDATE ownerId=NULL si prefieres)
            $stmtProp = $this->conn->prepare("DELETE FROM PROPERTY WHERE ownerId = ?");
            $stmtProp->bind_param("i", $personId);
            $stmtProp->execute();

            // 7) Finalmente eliminar la persona
            $stmtDelPerson = $this->conn->prepare("DELETE FROM PERSON WHERE personId = ?");
            $stmtDelPerson->bind_param("i", $personId);
            $stmtDelPerson->execute();

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Error al eliminar propietario ({$documentIdentifier}): " . $e->getMessage());
            return false;
        }
    }

    // Subir documento
    public function guardarDocumento($personId, $rutaRelativa) {
        $stmt = $this->conn->prepare("INSERT INTO FILE_RECORD (personId, filePath, uploadDate) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $personId, $rutaRelativa);
        return $stmt->execute();
    }

    // Obtener documentos de un propietario
    public function obtenerDocumentos($personId) {
        $stmt = $this->conn->prepare("SELECT fileId, filePath, uploadDate FROM FILE_RECORD WHERE personId=? ORDER BY uploadDate DESC");
        $stmt->bind_param("i", $personId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
