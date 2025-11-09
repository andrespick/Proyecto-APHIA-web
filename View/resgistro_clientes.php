<?php
require_once __DIR__ . '/../Controller/clientesController.php';

$controller = new ClienteController();
$alertMessage = null;
$alertType = null;
$editMode = false;
$formData = [
    'nombre' => '',
    'apellido' => '',
    'tipo_doc' => '',
    'numero_doc' => '',
    'telefono' => '',
    'email' => '',
    'direccion' => '',
    'genero' => '',
];

$status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS);
if ($status === 'created') {
    $alertMessage = 'Cliente registrado correctamente.';
    $alertType = 'success';
} elseif ($status === 'updated') {
    $alertMessage = 'Cliente actualizado correctamente.';
    $alertType = 'success';
} elseif ($status === 'deleted') {
    $alertMessage = 'Cliente eliminado correctamente.';
    $alertType = 'success';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $manageAction = trim($_POST['manage_action'] ?? '');

    if ($manageAction === 'delete') {
        $doc = $_POST['target_doc'] ?? '';
        $resultado = $controller->eliminar($doc);
        if ($resultado['ok']) {
            header('Location: resgistro_clientes.php?status=deleted');
            exit;
        }
        $alertMessage = $resultado['msg'];
        $alertType = 'error';
    } elseif ($manageAction === 'load_edit') {
        $doc = $_POST['target_doc'] ?? '';
        $cliente = $controller->obtenerPorDocumento($doc);
        if ($cliente) {
            $parts = explode(' ', $cliente['fullName'], 2);
            $formData = [
                'nombre' => $parts[0] ?? '',
                'apellido' => $parts[1] ?? '',
                'tipo_doc' => $cliente['documentCategory'] ?? '',
                'numero_doc' => $cliente['documentIdentifier'] ?? '',
                'telefono' => $cliente['phoneNumber'] ?? '',
                'email' => $cliente['emailAddress'] ?? '',
                'direccion' => $cliente['address'] ?? '',
                'genero' => $cliente['gender'] ?? '',
            ];
            $editMode = true;
        } else {
            $alertMessage = 'No se encontró el cliente solicitado.';
            $alertType = 'error';
        }
    } elseif (!empty($_POST['form_action'])) {
        $formAction = $_POST['form_action'];
        $data = [
            'nombre' => $_POST['nombre'] ?? '',
            'apellido' => $_POST['apellido'] ?? '',
            'tipo_doc' => $_POST['tipo_doc'] ?? '',
            'numero_doc' => $_POST['numero_doc'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'email' => $_POST['email'] ?? '',
            'direccion' => $_POST['direccion'] ?? '',
            'genero' => $_POST['genero'] ?? '',
        ];

        $resultado = $formAction === 'update'
            ? $controller->actualizar($data)
            : $controller->guardar($data);

        if ($resultado['ok']) {
            $target = $formAction === 'update' ? 'updated' : 'created';
            header('Location: resgistro_clientes.php?status=' . $target);
            exit;
        }

        $alertMessage = $resultado['msg'];
        $alertType = 'error';
        $formData = $resultado['data'] ?? $data;
        $editMode = ($formAction === 'update');
    }
}

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
  <link rel="shortcut icon" href="img/logo.svg" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="container">
    <!-- SIDEBAR -->
    <aside class="sidebar">
      <div class="user"><i class="fa-solid fa-user-shield"></i><span>Administrador</span></div>
      <nav class="menu">
        <a href="#"><i class="fas fa-users"></i><span>CLIENTES</span></a>
        <a href="registro_propietarios.php"><i class="fas fa-user-tie"></i><span>PROPIETARIOS</span></a>
        <a href="registro_codeudor.php"><i class="fas fa-user-shield"></i><span>CODEUDORES</span></a>
        <a href="registro_inmuebles.php"><i class="fas fa-building"></i><span>INMUEBLES</span></a>
        <a href="#"><i class="fas fa-file-contract"></i><span>CONTRATOS</span></a>
      </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
      <div class="contenedor">
        <div class="header">
          <div>Registro de Clientes</div>
          <div class="icono"><i class="fa-solid fa-user"></i></div>
        </div>

        <!-- FORMULARIO -->
        <form class="formulario" action="resgistro_clientes.php" method="POST">
          <input type="hidden" name="form_action" value="<?= $editMode ? 'update' : 'create' ?>">

          <?php if (!empty($alertMessage)): ?>
            <div class="alert <?= $alertType === 'success' ? 'alert-success' : 'alert-error' ?>">
              <?= htmlspecialchars($alertMessage) ?>
            </div>
          <?php endif; ?>

          <div class="campo-grupo">
            <div class="campo">
              <label for="nombre">Nombre:</label>
              <input type="text" id="nombre" name="nombre" required value="<?= htmlspecialchars($formData['nombre']) ?>">
            </div>
            <div class="campo">
              <label for="apellido">Apellido:</label>
              <input type="text" id="apellido" name="apellido" required value="<?= htmlspecialchars($formData['apellido']) ?>">
            </div>
          </div>

          <div class="campo-grupo">
            <div class="campo">
              <label for="tipo_doc">Tipo de documento:</label>
              <select id="tipo_doc" name="tipo_doc" required>
                <option value="">Seleccione</option>
                <option value="CC" <?= ($formData['tipo_doc'] === 'CC') ? 'selected' : '' ?>>Cédula</option>
                <option value="TI" <?= ($formData['tipo_doc'] === 'TI') ? 'selected' : '' ?>>Tarjeta de Identidad</option>
                <option value="CE" <?= ($formData['tipo_doc'] === 'CE') ? 'selected' : '' ?>>Cédula de Extranjería</option>
              </select>
            </div>
            <div class="campo">
              <label for="numero_doc">Número de documento:</label>
              <input type="text" id="numero_doc" name="numero_doc" required value="<?= htmlspecialchars($formData['numero_doc']) ?>" <?= $editMode ? 'readonly' : '' ?>>
            </div>
          </div>

          <div class="campo-grupo">
            <div class="campo">
              <label for="telefono">Teléfono:</label>
              <input type="text" id="telefono" name="telefono" required value="<?= htmlspecialchars($formData['telefono']) ?>">
            </div>
            <div class="campo">
              <label for="email">Correo electrónico:</label>
              <input type="email" id="email" name="email" required value="<?= htmlspecialchars($formData['email']) ?>">
            </div>
          </div>

          <div class="campo-grupo">
            <div class="campo">
              <label for="direccion">Dirección:</label>
              <input type="text" id="direccion" name="direccion" required value="<?= htmlspecialchars($formData['direccion']) ?>">
            </div>
            <div class="campo">
              <label>Género:</label>
              <div class="radio-group">
                <label><input type="radio" name="genero" value="M" <?= ($formData['genero'] === 'M') ? 'checked' : '' ?>> Masculino</label>
                <label><input type="radio" name="genero" value="F" <?= ($formData['genero'] === 'F') ? 'checked' : '' ?>> Femenino</label>
              </div>
            </div>
          </div>

          <div class="botones">
            <button type="submit" class="btn-guardar"><?= $editMode ? 'Actualizar' : 'Guardar' ?></button>
            <?php if ($editMode): ?>
              <a class="btn-limpiar" href="resgistro_clientes.php">Cancelar</a>
            <?php else: ?>
              <button type="reset" class="btn-limpiar">Limpiar</button>
            <?php endif; ?>
          </div>
        </form>

        <!-- LISTADO -->
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
                      <form method="POST" action="resgistro_clientes.php" style="display:inline;">
                        <input type="hidden" name="manage_action" value="load_edit">
                        <input type="hidden" name="target_doc" value="<?= htmlspecialchars($c['documentIdentifier']) ?>">
                        <button type="submit" title="Editar" style="background:none;border:none;color:#7e57c2;cursor:pointer;">
                          <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                      </form>
                      <form method="POST" action="resgistro_clientes.php" style="display:inline;margin-left:10px;" onsubmit="return confirm('¿Eliminar este cliente?');">
                        <input type="hidden" name="manage_action" value="delete">
                        <input type="hidden" name="target_doc" value="<?= htmlspecialchars($c['documentIdentifier']) ?>">
                        <button type="submit" title="Eliminar" style="background:none;border:none;color:#e53935;cursor:pointer;">
                          <i class="fa-solid fa-trash"></i>
                        </button>
                      </form>
                      <form method="POST" action="subir_documentos.php" style="display:inline;margin-left:10px;">
                        <input type="hidden" name="doc" value="<?= htmlspecialchars($c['documentIdentifier']) ?>">
                        <input type="hidden" name="return" value="resgistro_clientes.php">
                        <button type="submit" title="Subir documentos" style="background:none;border:none;color:#388e3c;cursor:pointer;">
                          <i class="fa-solid fa-file-upload"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5">No hay clientes registrados</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const searchInput = document.querySelector('.buscador input');
      const rows = Array.from(document.querySelectorAll('tbody tr'));

      searchInput.addEventListener('input', function () {
        const term = (this.value || '').toLowerCase().trim();
        let visible = 0;
        rows.forEach(row => {
          const match = row.textContent.toLowerCase().includes(term);
          row.style.display = match ? '' : 'none';
          if (match) visible++;
        });
        document.querySelector('.listado table').classList.toggle('sin-resultados', visible === 0);
      });
    });
  </script>
</body>
</html>
