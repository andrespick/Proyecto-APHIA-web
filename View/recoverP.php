<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="Styles/style_recoverP.css">
  <link rel="shortcut icon" href="../img/logo.svg" />
  <title>Recuperar Contraseña</title>
</head>
<body>
  <div class="container">
    <div class="card">
      <img src="../img/logo.png" alt="Logo Aphia" class="logo">
      <h1 class="title">Recuperación Contraseña</h1>

      <form method="POST" action="ASIGNAR_RUTA_PARA_RECUPERAR_CONTRASEÑA">
        <input
          type="email"
          name="email"
          placeholder="Introduce tu dirección de correo electrónico:"
          class="email-input"
          required>
        <button type="submit" class="reset-btn">Restablecer Contraseña</button>
      </form>
    </div>
  </div>
</body>
</html>