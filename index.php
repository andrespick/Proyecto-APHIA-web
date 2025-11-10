<?php
$errorMessage = null;
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'campos_vacios':
            $errorMessage = 'Por favor completa todos los campos.';
            break;
        case 'email_invalido':
            $errorMessage = 'El correo electronico no es valido.';
            break;
        case 'credenciales_invalidas':
            $errorMessage = 'Correo o contrasena incorrectos.';
            break;
        case 'categoria_invalida':
            $errorMessage = 'Tu categoria de usuario no tiene acceso.';
            break;
        case 'usuario_inactivo':
            $errorMessage = 'Tu usuario no esta activo porfavor comunicate con el administrador de sistemas.';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="View/styles/LoginStyle.css">
  <link rel="shortcut icon" href="View/img/logo.svg" />
  <title>Login</title>
</head>

<body>

  <!--Caja contenedora login-->
  <div class="container">
    <img src="View/img/logo.png" alt="logo" class="logo">


    <div class="form-container">
      <h1 class="title">INICIAR SESION</h1>
      <?php if ($errorMessage): ?>
      <div class="error-message"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
      <?php endif; ?>
      <form method="POST" action="Controller/loginController.php">
        <input type="hidden" name="form_origin" value="index">
        <input type="email" name="email" placeholder="Correo electrónico" class="username" required>
        <input type="password" name="password" placeholder="Contraseña" class="password" required>
        <button type="submit">Iniciar sesión</button>

        <!-- Enlace para recuperar contraseña -->
        <div class="forgot-password">
          <a href="View/recoverP.php">¿Olvidaste tu contraseña?</a>

      </form>
    </div>
    <div class="images-box">
      <img src="/View/img/logo.png" alt="">
</body>

</html>
