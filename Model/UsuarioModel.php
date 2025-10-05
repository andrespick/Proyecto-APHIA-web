<?php
require_once '../Model/conexions.php'; // archivo de conexión a la BD

class UsuarioModel {

    public function verificarCredenciales($email, $password) {
        $conexion = (new Conexion())->conectar();

        // Buscar usuario por email
        $sql = "SELECT * FROM user_account WHERE emailAddress = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();

            // Comparar contraseña (si está hasheada en BD usa password_verify)
            if ($password === $usuario['hashedPassword']) {
                return $usuario; // credenciales válidas
            }
        }

        return false; // credenciales inválidas
    }
}
?>
