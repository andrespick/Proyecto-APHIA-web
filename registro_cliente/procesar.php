<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = htmlspecialchars($_POST["nombre"]);
    $apellido = htmlspecialchars($_POST["apellido"]);
    $anio     = htmlspecialchars($_POST["anio"]);
    $prefijo  = htmlspecialchars($_POST["prefijo"]);
    $telefono = htmlspecialchars($_POST["telefono"]);
    $correo   = htmlspecialchars($_POST["correo"]);
    $tipo     = htmlspecialchars($_POST["tipo"]);
} else {
    echo "No se recibieron datos.";
    exit;
}
?>

<!DOCTYPE html>

<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Datos del Cliente</title>
  <link rel="stylesheet" href="registro_cliente.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
  <div class="contenedor">
    <header class="header">
      <div class="titulo-izq">
        <i class="bx bx-check-circle"></i>
        <span>CLIENTE REGISTRADO</span>
      </div>
      <div class="titulo-der">
        Datos Recibidos
      </div>
    </header>

```
<main class="formulario">
  <h2>Información del Cliente</h2>
  <p><strong>Nombre Completo:</strong> <?php echo $nombre . " " . $apellido; ?></p>
  <p><strong>Año de Nacimiento:</strong> <?php echo $anio; ?></p>
  <p><strong>Teléfono:</strong> <?php echo $prefijo . " " . $telefono; ?></p>
  <p><strong>Correo:</strong> <?php echo $correo; ?></p>
  <p><strong>Tipo de Cliente:</strong> <?php echo $tipo; ?></p>
</main>
```

  </div>
</body>
</html>
