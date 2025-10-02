<!DOCTYPE html>

<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro Cliente</title>
  <link rel="stylesheet" href="registro_cliente.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
  <div class="contenedor">
    <!-- Encabezado -->
    <header class="header">
      <div class="titulo-izq">
        <i class="bx bx-user"></i>
        <span>CLIENTES</span>
      </div>
      <div class="titulo-der">
        REGISTRO CLIENTE
      </div>
    </header>

```
<!-- Contenido -->
<main class="formulario">
  <!-- Formulario que envía por POST -->
  <form action="procesar.php" method="POST">
    <div class="campo nombre">
      <label>Nombre Completo:</label>
      <input type="text" name="nombre" placeholder="Nombre" required>
      <input type="text" name="apellido" placeholder="Apellido" required>
    </div>

    <div class="campo">
      <label>Año de Nacimiento:</label>
      <select name="anio" required>
        <option value="">Seleccione</option>
        <option>1990</option>
        <option>1995</option>
        <option>2000</option>
      </select>
    </div>

    <div class="campo telefono">
      <label>Número de Teléfono:</label>
      <select name="prefijo">
        <option>+53</option>
        <option>+57</option>
        <option>+34</option>
      </select>
      <input type="text" name="telefono" required>
    </div>

    <div class="campo">
      <label>Correo Electrónico:</label>
      <input type="email" name="correo" placeholder="ejemplo@mail.com" required>
    </div>

    <div class="campo">
      <label>Tipo de Cliente:</label>
      <div class="radio-group">
        <label><input type="radio" name="tipo" value="Arrendatario" required> Arrendatario</label>
        <label><input type="radio" name="tipo" value="Propietario" required> Propietario</label>
      </div>
    </div>

    <button type="submit" class="btn-guardar">GUARDAR</button>
  </form>
</main>
  </div>
</body>
</html>

