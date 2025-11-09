<?php
require_once '../Model/UsuarioModel.php';
session_start();

$formOrigin = $_POST['form_origin'] ?? '';
$redirectBase = ($formOrigin === 'index') ? '../index.php' : '../View/login.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        header("Location: {$redirectBase}?error=campos_vacios");
        exit;
    }

    $usuarioModel = new UsuarioModel();
    $usuario = $usuarioModel->verificarCredenciales($email, $password);

    if ($usuario) {
        if (strcasecmp($usuario['state'] ?? '', 'ACTIVE') !== 0) {
            header("Location: {$redirectBase}?error=usuario_inactivo");
            exit;
        }

        $_SESSION['usuario'] = [
            'id' => $usuario['accountId'],
            'nombre' => $usuario['userName'],
            'categoria' => $usuario['userCategory'],
            'email' => $usuario['emailAddress']
        ];

        // Redirección según categoría
        switch (strtolower($usuario['userCategory'])) {
            case 'system administrator':
                header("Location: ../View/gestion_usuarios_sysadmin.php");
                break;
            case 'administrator':
                header("Location: ../View/administrator_dashboard.php");
                break;
            case 'advisor':
                header("Location: ../View/advisor_dashboard.php");
                break;
            default:
                header("Location: {$redirectBase}?error=categoria_invalida");
                break;
        }
        exit;
    } else {
        header("Location: {$redirectBase}?error=credenciales_invalidas");
        exit;
    }
} else {
    header("Location: {$redirectBase}");
    exit;
}
?>
