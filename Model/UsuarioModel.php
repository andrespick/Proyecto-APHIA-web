<?php
require_once '../Model/conexions.php'; // archivo de conexión a la BD

class UsuarioModel {

    public function verificarCredenciales($email, $password) {
        $conexion = (new Conexion())->conectar();

        // Buscar usuario por email. La columna hashedPassword puede contener hashes modernos
        // generados con password_hash o valores legados en texto plano que aún no han sido migrados.
        $sql = "SELECT * FROM user_account WHERE emailAddress = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();
            $almacenada = $usuario['hashedPassword'] ?? '';

            if ($this->esHashSeguro($almacenada)) {
                if (password_verify($password, $almacenada)) {
                    if (password_needs_rehash($almacenada, PASSWORD_DEFAULT)) {
                        $usuario['hashedPassword'] = $this->actualizarPasswordHash($conexion, (int)$usuario['accountId'], password_hash($password, PASSWORD_DEFAULT));
                    }
                    return $usuario; // credenciales válidas
                }
            } else {
                if ($almacenada !== '' && hash_equals($almacenada, $password)) {
                    $usuario['hashedPassword'] = $this->actualizarPasswordHash($conexion, (int)$usuario['accountId'], password_hash($password, PASSWORD_DEFAULT));
                    return $usuario; // credenciales válidas tras migrar hash
                }
            }
        }

        return false; // credenciales inválidas
    }

    private function esHashSeguro(string $valor): bool
    {
        $info = password_get_info($valor);
        return is_array($info) && ($info['algo'] ?? 0) !== 0;
    }

    private function actualizarPasswordHash($conexion, int $accountId, string $nuevoHash): string
    {
        $sql = "UPDATE user_account SET hashedPassword = ? WHERE accountId = ?";
        $stmt = $conexion->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('si', $nuevoHash, $accountId);
            $stmt->execute();
        }

        return $nuevoHash;
    }
}
?>
