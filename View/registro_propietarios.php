<?php
require_once __DIR__ . '/../Controller/propietariosController.php';

$controller = new PropietarioController();
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
    'numero_cuenta' => '',
    'tipo_cuenta' => '',
    'entidad_bancaria' => '',
];

$status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS);
if ($status === 'created') {
    $alertMessage = 'Propietario registrado correctamente.';
    $alertType = 'success';
} elseif ($status === 'updated') {
    $alertMessage = 'Propietario actualizado correctamente.';
    $alertType = 'success';
} elseif ($status === 'deleted') {
    $alertMessage = 'Propietario eliminado correctamente.';
    $alertType = 'success';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $manageAction = trim($_POST['manage_action'] ?? '');

    if ($manageAction === 'delete') {
        $doc = $_POST['target_doc'] ?? '';
        $resultado = $controller->eliminar($doc);
        if ($resultado['ok']) {
            header('Location: registro_propietarios.php?status=deleted');
            exit;
        }
        $alertMessage = $resultado['msg'];
        $alertType = 'error';
    } elseif ($manageAction === 'load_edit') {
        $doc = $_POST['target_doc'] ?? '';
        $propietario = $controller->obtenerPorDocumento($doc);
        if ($propietario) {
            $parts = explode(' ', $propietario['fullName'], 2);
            $formData = [
                'nombre' => $parts[0] ?? '',
                'apellido' => $parts[1] ?? '',
                'tipo_doc' => $propietario['documentCategory'] ?? '',
                'numero_doc' => $propietario['documentIdentifier'] ?? '',
                'telefono' => $propietario['phoneNumber'] ?? '',
                'email' => $propietario['emailAddress'] ?? '',
                'direccion' => $propietario['address'] ?? '',
                'numero_cuenta' => $propietario['accountIdentifier'] ?? '',
                'tipo_cuenta' => $propietario['accountCategory'] ?? '',
                'entidad_bancaria' => $propietario['financialInstitution'] ?? '',
            ];
            $editMode = true;
        } else {
            $alertMessage = 'No se encontró el propietario solicitado.';
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
            'numero_cuenta' => $_POST['numero_cuenta'] ?? '',
            'tipo_cuenta' => $_POST['tipo_cuenta'] ?? '',
            'entidad_bancaria' => $_POST['entidad_bancaria'] ?? '',
        ];

        $resultado = $formAction === 'update'
            ? $controller->actualizar($data)
            : $controller->guardar($data);

        if ($resultado['ok']) {
            $target = $formAction === 'update' ? 'updated' : 'created';
            header('Location: registro_propietarios.php?status=' . $target);
            exit;
        }

        $alertMessage = $resultado['msg'];
        $alertType = 'error';
        $formData = $resultado['data'] ?? $data;
        $editMode = ($formAction === 'update');
    }
}

$propietarios = $controller->index();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Propietario</title>
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
        <a href="resgistro_clientes.php"><i class="fas fa-users"></i><span>CLIENTES</span></a>
        <a href="#"><i class="fas fa-user-tie"></i><span>PROPIETARIOS</span></a>
        <a href="registro_codeudor.php"><i class="fas fa-user-shield"></i><span>CODEUDORES</span></a>
        <a href="registro_inmuebles.php"><i class="fas fa-building"></i><span>INMUEBLES</span></a>
        <a href="#"><i class="fas fa-file-contract"></i><span>CONTRATOS</span></a>
      </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
      <div class="contenedor">
        <div class="header">
          <div>Registro de Propietarios</div>
          <div class="icono"><i class="fa-solid fa-user-tie"></i></div>
        </div>

        <!-- FORMULARIO -->
        <form class="formulario" action="registro_propietarios.php" method="POST">
          <input type="hidden" name="form_action" value="<?= $editMode ? 'update' : 'create' ?>">

          <?php if (!empty($alertMessage)): ?>
            <div class="alert <?= $alertType === 'success' ? 'alert-success' : 'alert-error' ?>">
              <?= htmlspecialchars($alertMessage) ?>
            </div>
          <?php endif; ?>

          <!-- Datos personales -->
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
                <option value="CE" <?= ($formData['tipo_doc'] === 'CE') ? 'selected' : '' ?>>Cédula de Extranjería</option>
                <option value="NIT" <?= ($formData['tipo_doc'] === 'NIT') ? 'selected' : '' ?>>NIT</option>
                <option value="PAS" <?= ($formData['tipo_doc'] === 'PAS') ? 'selected' : '' ?>>Pasaporte</option>
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
          </div>

          <!-- Datos bancarios -->
          <h3 style="margin-top:20px;color:#444;">Datos Bancarios</h3>
          <div class="campo-grupo">
            <div class="campo">
              <label for="numero_cuenta">Número de cuenta:</label>
              <input type="text" id="numero_cuenta" name="numero_cuenta" value="<?= htmlspecialchars($formData['numero_cuenta']) ?>">
            </div>
            <div class="campo">
              <label for="tipo_cuenta">Tipo de cuenta:</label>
              <select id="tipo_cuenta" name="tipo_cuenta">
                <option value="">Seleccione</option>
                <option value="Ahorros" <?= ($formData['tipo_cuenta'] === 'Ahorros') ? 'selected' : '' ?>>Ahorros</option>
                <option value="Corriente" <?= ($formData['tipo_cuenta'] === 'Corriente') ? 'selected' : '' ?>>Corriente</option>
                <option value="Digital" <?= ($formData['tipo_cuenta'] === 'Digital') ? 'selected' : '' ?>>Digital</option>
              </select>
            </div>
          </div>

          <div class="campo-grupo">
            <div class="campo">
              <label for="entidad_bancaria">Entidad bancaria:</label>
              <select id="entidad_bancaria" name="entidad_bancaria">
                <option value="">Seleccione</option>
                <option value="Bancolombia" <?= ($formData['entidad_bancaria'] === 'Bancolombia') ? 'selected' : '' ?>>Bancolombia</option>
                <option value="Nequi" <?= ($formData['entidad_bancaria'] === 'Nequi') ? 'selected' : '' ?>>Nequi</option>
                <option value="BBVA" <?= ($formData['entidad_bancaria'] === 'BBVA') ? 'selected' : '' ?>>BBVA</option>
                <option value="Davivienda" <?= ($formData['entidad_bancaria'] === 'Davivienda') ? 'selected' : '' ?>>Davivienda</option>
                <option value="Otro" <?= ($formData['entidad_bancaria'] === 'Otro') ? 'selected' : '' ?>>Otro</option>
              </select>
            </div>
          </div>

          <div class="botones">
            <button type="submit" class="btn-guardar"><?= $editMode ? 'Actualizar' : 'Guardar' ?></button>
            <?php if ($editMode): ?>
              <a class="btn-limpiar" href="registro_propietarios.php">Cancelar</a>
            <?php else: ?>
              <button type="reset" class="btn-limpiar">Limpiar</button>
            <?php endif; ?>
          </div>
        </form>

        <!-- LISTADO DE PROPIETARIOS -->
        <div class="listado">
          <h2>Lista de Propietarios</h2>
          <div class="buscador">
            <input type="text" placeholder="Buscar propietario...">
            <i class="fa-solid fa-magnifying-glass"></i>
          </div>

          <table>
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Documento</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Cuenta</th>
                <th>Banco</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($propietarios)): ?>
                <?php foreach ($propietarios as $p): ?>
                  <tr>
                    <td><?= htmlspecialchars($p['fullName']) ?></td>
                    <td><?= htmlspecialchars($p['documentCategory']) . ' ' . htmlspecialchars($p['documentIdentifier']) ?></td>
                    <td><?= htmlspecialchars($p['phoneNumber']) ?></td>
                    <td><?= htmlspecialchars($p['emailAddress']) ?></td>
                    <td><?= htmlspecialchars($p['accountIdentifier'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($p['financialInstitution'] ?? 'N/A') ?></td>
                    <td class="acciones">
                      <form method="POST" action="registro_propietarios.php" style="display:inline;">
                        <input type="hidden" name="manage_action" value="load_edit">
                        <input type="hidden" name="target_doc" value="<?= htmlspecialchars($p['documentIdentifier']) ?>">
                        <button type="submit" title="Editar" style="background:none;border:none;color:#7e57c2;cursor:pointer;">
                          <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                      </form>
                      <form method="POST" action="registro_propietarios.php" style="display:inline;margin-left:10px;" onsubmit="return confirm('¿Eliminar este propietario?');">
                        <input type="hidden" name="manage_action" value="delete">
                        <input type="hidden" name="target_doc" value="<?= htmlspecialchars($p['documentIdentifier']) ?>">
                        <button type="submit" title="Eliminar" style="background:none;border:none;color:#e53935;cursor:pointer;">
                          <i class="fa-solid fa-trash"></i>
                        </button>
                      </form>
                      <form method="POST" action="subir_documentos.php" style="display:inline;margin-left:10px;">
                        <input type="hidden" name="doc" value="<?= htmlspecialchars($p['documentIdentifier']) ?>">
                        <input type="hidden" name="return" value="registro_propietarios.php">
                        <button type="submit" title="Subir documentos" style="background:none;border:none;color:#388e3c;cursor:pointer;">
                          <i class="fa-solid fa-file-upload"></i>
                        </button>
                      </form>
                      <form method="POST" action="ver_documentos.php" style="display:inline;margin-left:10px;">
                        <input type="hidden" name="doc" value="<?= htmlspecialchars($p['documentIdentifier']) ?>">
                        <input type="hidden" name="return" value="registro_propietarios.php">
                        <button type="submit" title="Ver documentos" style="background:none;border:none;color:#1565c0;cursor:pointer;">
                          <i class="fa-solid fa-file-lines"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7">No hay propietarios registrados</td>
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
