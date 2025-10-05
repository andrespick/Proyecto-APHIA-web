<?php
require_once '../Model/UsuarioModel.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        header("Location: ../views/login.php?error=campos_vacios");
        exit;
    }

    $usuarioModel = new UsuarioModel();
    $usuario = $usuarioModel->verificarCredenciales($email, $password);

    if ($usuario) {
        $_SESSION['usuario'] = [
            'id' => $usuario['accountId'],
            'nombre' => $usuario['userName'],
            'categoria' => $usuario['userCategory'],
            'email' => $usuario['emailAddress']
        ];

        // Redirección según categoría
        switch (strtolower($usuario['userCategory'])) {
            case 'system administrator':
                header("Location: ../View/admin_dashboard.php");
                break;
            case 'administrator':
                header("Location: ../View/administrator_dashboard.php");
                break;
            case 'advisor':
                header("Location: ../View/advisor_dashboard.php");
                break;
            default:
                header("Location: ../View/login.php?error=categoria_invalida");
                break;
        }
        exit;
    } else {
        header("Location: ../View/login.php?error=credenciales_invalidas");
        exit;
    }
} else {
    header("Location: ../View/login.php");
    exit;
}
?>
