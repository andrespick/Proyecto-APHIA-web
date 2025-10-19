<?php
// Ajusta la ruta según tu estructura
require_once __DIR__ . '/../Controller/clientesController.php';

$controller = new ClienteController();

// Acción: eliminar (viene por GET)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['doc'])) {
    $doc = $_GET['doc'];
    $ok = $controller->eliminar($doc);
    // redirigir para evitar re-envío al recargar
    header("Location: resgistro_clientes.php");
    exit;
}

// Acción: editar -> cargar datos para rellenar el formulario
$editMode = false;
$editClient = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && !empty($_GET['doc'])) {
    $doc = $_GET['doc'];
    $editClient = $controller->obtenerPorDocumento($doc);
    if ($editClient) {
        // separa fullName en nombre y apellido (intento simple)
        $parts = explode(' ', $editClient['fullName'], 2);
        $editClient['nombre'] = $parts[0] ?? '';
        $editClient['apellido'] = $parts[1] ?? '';
        $editMode = true;
    }
}

// Si se envía formulario (crear o actualizar)
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // recolectar campos seguros
    $data = [
        'nombre' => $_POST['nombre'] ?? '',
        'apellido' => $_POST['apellido'] ?? '',
        'tipo_doc' => $_POST['tipo_doc'] ?? '',
        'numero_doc' => $_POST['numero_doc'] ?? '',
        'telefono' => $_POST['telefono'] ?? '',
        'email' => $_POST['email'] ?? '',
        'direccion' => $_POST['direccion'] ?? '',
        'genero' => $_POST['genero'] ?? ''
    ];

    if (!empty($_POST['form_action']) && $_POST['form_action'] === 'update') {
        $res = $controller->actualizar($data);
        $message = $res['ok'] ? 'Cliente actualizado correctamente' : ('Error: '.$res['msg']);
    } else {
        $res = $controller->guardar($data);
        $message = $res['ok'] ? 'Cliente creado correctamente' : ('Error: '.$res['msg']);
    }

    // después de procesar, redirige para limpiar POST y mostrar cambios
    header("Location: resgistro_clientes.php");
    exit;
}

// Obtener lista actualizada
$clientes = $controller->index();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Cliente</title>
  <link rel="stylesheet" href="Styles/style_administrator_dashboard.css">
  <link rel="stylesheet" href="Styles/registro_cliente.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="shortcut icon" href="../img/logo.svg" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="container">
    <!-- SIDEBAR -->
    <aside class="sidebar">
      <div class="user"><i class="fa-solid fa-user-shield"></i><span>Administrador</span></div>
      <nav class="menu">
        <a href="#"><i class="fas fa-users"></i><span>CLIENTES</span></a>
        <a href="#"><i class="fas fa-user-tie"></i><span>PROPIETARIOS</span></a>
        <a href="#"><i class="fas fa-user-shield"></i><span>CODEUDORES</span></a>
        <a href="#"><i class="fas fa-building"></i><span>INMUEBLES</span></a>
        <a href="#"><i class="fas fa-file-contract"></i><span>CONTRATOS</span></a>
      </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
      <div class="contenedor">
        <div class="header"><div>Registro de Clientes</div><div class="icono"><i class="fa-solid fa-user"></i></div></div>

        <!-- Mensaje (opcional) -->
        <?php if (!empty($message)): ?>
          <div class="mensaje"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Formulario -->
        <form class="formulario" action="resgistro_clientes.php" method="POST">
          <!-- indicamos si es update o create -->
          <input type="hidden" name="form_action" value="<?= $editMode ? 'update' : 'create' ?>">
          <div class="campo-grupo">
            <div class="campo">
              <label for="nombre">Nombre:</label>
              <input type="text" id="nombre" name="nombre" placeholder="Ingrese el nombre" required
                     value="<?= $editMode ? htmlspecialchars($editClient['nombre']) : '' ?>">
            </div>

            <div class="campo">
              <label for="apellido">Apellido:</label>
              <input type="text" id="apellido" name="apellido" placeholder="Ingrese el apellido" required
                     value="<?= $editMode ? htmlspecialchars($editClient['apellido']) : '' ?>">
            </div>
          </div>

          <div class="campo-grupo">
            <div class="campo">
              <label for="tipo_doc">Tipo de documento:</label>
              <select id="tipo_doc" name="tipo_doc" required>
                <option value="">Seleccione</option>
                <option value="CC" <?= ($editMode && $editClient['documentCategory']=='CC') ? 'selected' : '' ?>>Cédula</option>
                <option value="TI" <?= ($editMode && $editClient['documentCategory']=='TI') ? 'selected' : '' ?>>Tarjeta de Identidad</option>
                <option value="CE" <?= ($editMode && $editClient['documentCategory']=='CE') ? 'selected' : '' ?>>Cédula de Extranjería</option>
              </select>
            </div>

            <div class="campo">
              <label for="numero_doc">Número de documento:</label>
              <input type="text" id="numero_doc" name="numero_doc" placeholder="Ingrese el número" required
                     value="<?= $editMode ? htmlspecialchars($editClient['documentIdentifier']) : '' ?>"
                     <?= $editMode ? 'readonly' : '' /* para evitar cambiar PK en edición */ ?>>
            </div>
          </div>

          <div class="campo-grupo">
            <div class="campo">
              <label for="telefono">Teléfono:</label>
              <input type="text" id="telefono" name="telefono" placeholder="Ingrese el teléfono" required
                     value="<?= $editMode ? htmlspecialchars($editClient['phoneNumber']) : '' ?>">
            </div>

            <div class="campo">
              <label for="email">Correo electrónico:</label>
              <input type="email" id="email" name="email" placeholder="Ingrese el correo" required
                     value="<?= $editMode ? htmlspecialchars($editClient['emailAddress']) : '' ?>">
            </div>
          </div>

          <div class="campo-grupo">
            <div class="campo">
              <label for="direccion">Dirección:</label>
              <input type="text" id="direccion" name="direccion" placeholder="Ingrese la dirección" required
                     value="<?= $editMode ? htmlspecialchars($editClient['address']) : '' ?>">
            </div>

            <div class="campo">
              <label>Género:</label>
              <div class="radio-group">
                <label><input type="radio" name="genero" value="M" <?= ($editMode && ($editClient['gender'] ?? '')=='M') ? 'checked' : '' ?>> Masculino</label>
                <label><input type="radio" name="genero" value="F" <?= ($editMode && ($editClient['gender'] ?? '')=='F') ? 'checked' : '' ?>> Femenino</label>
              </div>
            </div>
          </div>

          <div class="botones">
            <button type="submit" class="btn-guardar"><?= $editMode ? 'Actualizar' : 'Guardar' ?></button>
            <?php if ($editMode): ?>
              <a class="btn-limpiar" href="resgistro_clientes.php" style="display:inline-block;padding:8px 12px;background:#e53935;color:#fff;border-radius:5px;text-decoration:none;margin-left:8px;">Cancelar</a>
            <?php else: ?>
              <button type="reset" class="btn-limpiar">Limpiar</button>
            <?php endif; ?>
          </div>
        </form>

        <!-- Listado -->
        <div class="listado">
          <h2>Lista de Clientes</h2>
          <div class="buscador">
            <input type="text" placeholder="Buscar cliente...">
            <i class="fa-solid fa-magnifying-glass"></i>
          </div>

          <table>
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Documento</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($clientes)): ?>
                <?php foreach ($clientes as $c): ?>
                  <tr>
                    <td><?= htmlspecialchars($c['fullName']) ?></td>
                    <td><?= htmlspecialchars($c['documentCategory']) . ' ' . htmlspecialchars($c['documentIdentifier']) ?></td>
                    <td><?= htmlspecialchars($c['phoneNumber']) ?></td>
                    <td><?= htmlspecialchars($c['emailAddress']) ?></td>
                    <td class="acciones">
                      <!-- EDIT: apunta a la misma vista con ?action=edit -->
                      <a href="resgistro_clientes.php?action=edit&doc=<?= urlencode($c['documentIdentifier']) ?>" title="Editar">
                        <i class="fa-solid fa-pen-to-square"></i>
                      </a>

                      <!-- DELETE: confirmación y llamada GET -->
                      <a href="resgistro_clientes.php?action=delete&doc=<?= urlencode($c['documentIdentifier']) ?>"
                         onclick="return confirm('¿Eliminar este cliente?');" title="Eliminar" style="margin-left:10px;color:#e53935;">
                        <i class="fa-solid fa-trash"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="5">No hay clientes registrados</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </div>
    </main>
  </div>
</body>
</html>
