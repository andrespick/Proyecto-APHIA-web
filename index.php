<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="View/styles/LoginStyle.css">
  <link rel="shortcut icon" href="img/logo.svg" />
  <title>Login</title>
</head>

<body>

  <!--Caja contenedora login-->
  <div class="container">
    <img src="img/logo.png" alt="logo" class="logo">


    <div class="form-container">
      <h1 class="title">INICIAR SESION</h1>
      <form method="POST" action="Controller/loginController.php">
        <input type="email" name="email" placeholder="Correo electrónico" class="username" required>
        <input type="password" name="password" placeholder="Contraseña" class="password" required>
        <button type="submit">Iniciar sesión</button>

        <!-- Enlace para recuperar contraseña -->
        <div class="forgot-password">
          <a href="View/recoverP.php">¿Olvidaste tu contraseña?</a>

      </form>
    </div>
    <div class="images-box">
      <img src="/img/logo.png" alt="">
</body>

</html>