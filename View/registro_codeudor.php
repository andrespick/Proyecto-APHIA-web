<?php
require_once __DIR__ . '/../Controller/codeudorController.php';

$controller = new CodeudorController();
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
];

$status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS);
if ($status === 'created') {
    $alertMessage = 'Codeudor registrado correctamente.';
    $alertType = 'success';
} elseif ($status === 'updated') {
    $alertMessage = 'Codeudor actualizado correctamente.';
    $alertType = 'success';
} elseif ($status === 'deleted') {
    $alertMessage = 'Codeudor eliminado correctamente.';
    $alertType = 'success';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $manageAction = trim($_POST['manage_action'] ?? '');

    if ($manageAction === 'delete') {
        $doc = $_POST['target_doc'] ?? '';
        $resultado = $controller->eliminar($doc);
        if ($resultado['ok']) {
            header('Location: registro_codeudor.php?status=deleted');
            exit;
        }
        $alertMessage = $resultado['msg'];
        $alertType = 'error';
    } elseif ($manageAction === 'load_edit') {
        $doc = $_POST['target_doc'] ?? '';
        $codeudor = $controller->obtenerPorDocumento($doc);
        if ($codeudor) {
            $parts = explode(' ', $codeudor['fullName'], 2);
            $formData = [
                'nombre' => $parts[0] ?? '',
                'apellido' => $parts[1] ?? '',
                'tipo_doc' => $codeudor['documentCategory'] ?? '',
                'numero_doc' => $codeudor['documentIdentifier'] ?? '',
                'telefono' => $codeudor['phoneNumber'] ?? '',
                'email' => $codeudor['emailAddress'] ?? '',
                'direccion' => $codeudor['address'] ?? '',
            ];
            $editMode = true;
        } else {
            $alertMessage = 'No se encontró el codeudor solicitado.';
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
        ];

        $resultado = $formAction === 'update'
            ? $controller->actualizar($data)
            : $controller->guardar($data);

        if ($resultado['ok']) {
            $target = $formAction === 'update' ? 'updated' : 'created';
            header('Location: registro_codeudor.php?status=' . $target);
            exit;
        }

        $alertMessage = $resultado['msg'];
        $alertType = 'error';
        $formData = $resultado['data'] ?? $data;
        $editMode = ($formAction === 'update');
    }
}

$codeudores = $controller->index();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registro de Codeudores</title>
<link rel="stylesheet" href="Styles/style_administrator_dashboard.css">
<link rel="stylesheet" href="Styles/registro_cliente.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="shortcut icon" href="img/logo.svg" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="container">
  <aside class="sidebar">
    <div class="user"><i class="fa-solid fa-user-shield"></i><span>Administrador</span></div>
    <nav class="menu">
      <a href="resgistro_clientes.php"><i class="fas fa-users"></i><span>CLIENTES</span></a>
      <a href="registro_propietarios.php"><i class="fas fa-user-tie"></i><span>PROPIETARIOS</span></a>
      <a href="registro_codeudor.php"><i class="fas fa-user-shield"></i><span>CODEUDORES</span></a>
      <a href="registro_inmuebles.php"><i class="fas fa-building"></i><span>INMUEBLES</span></a>
      <a href="#"><i class="fas fa-file-contract"></i><span>CONTRATOS</span></a>
    </nav>
  </aside>

  <main class="main-content">
    <div class="contenedor">
      <div class="header">
        <div>Registro de Codeudores</div>
        <div class="icono"><i class="fa-solid fa-user-shield"></i></div>
      </div>

      <form class="formulario" action="registro_codeudor.php" method="POST">
        <input type="hidden" name="form_action" value="<?= $editMode ? 'update' : 'create' ?>">

        <?php if (!empty($alertMessage)): ?>
          <div class="alert <?= $alertType === 'success' ? 'alert-success' : 'alert-error' ?>">
            <?= htmlspecialchars($alertMessage) ?>
          </div>
        <?php endif; ?>

        <div class="campo-grupo">
          <div class="campo">
            <label>Nombre:</label>
            <input type="text" name="nombre" required value="<?= htmlspecialchars($formData['nombre']) ?>">
          </div>
          <div class="campo">
            <label>Apellido:</label>
            <input type="text" name="apellido" required value="<?= htmlspecialchars($formData['apellido']) ?>">
          </div>
        </div>

        <div class="campo-grupo">
          <div class="campo">
            <label>Tipo documento:</label>
            <select name="tipo_doc" required>
              <option value="">Seleccione</option>
              <option value="CC" <?= ($formData['tipo_doc'] === 'CC') ? 'selected' : '' ?>>Cédula</option>
              <option value="CE" <?= ($formData['tipo_doc'] === 'CE') ? 'selected' : '' ?>>Cédula de Extranjería</option>
              <option value="PAS" <?= ($formData['tipo_doc'] === 'PAS') ? 'selected' : '' ?>>Pasaporte</option>
            </select>
          </div>
          <div class="campo">
            <label>Número documento:</label>
            <input type="text" name="numero_doc" required value="<?= htmlspecialchars($formData['numero_doc']) ?>" <?= $editMode ? 'readonly' : '' ?>>
          </div>
        </div>

        <div class="campo-grupo">
          <div class="campo">
            <label>Teléfono:</label>
            <input type="text" name="telefono" required value="<?= htmlspecialchars($formData['telefono']) ?>">
          </div>
          <div class="campo">
            <label>Correo:</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($formData['email']) ?>">
          </div>
        </div>

        <div class="campo">
          <label>Dirección:</label>
          <input type="text" name="direccion" required value="<?= htmlspecialchars($formData['direccion']) ?>">
        </div>

        <div class="botones">
          <button type="submit" class="btn-guardar"><?= $editMode ? 'Actualizar' : 'Guardar' ?></button>
          <?php if ($editMode): ?>
            <a class="btn-limpiar" href="registro_codeudor.php">Cancelar</a>
          <?php else: ?>
            <button type="reset" class="btn-limpiar">Limpiar</button>
          <?php endif; ?>
        </div>
      </form>

      <div class="listado">
        <h2>Lista de Codeudores</h2>
        <div class="buscador">
          <input type="text" placeholder="Buscar codeudor...">
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
            <?php if (!empty($codeudores)): foreach ($codeudores as $c): ?>
              <tr>
                <td><?= htmlspecialchars($c['fullName']) ?></td>
                <td><?= htmlspecialchars($c['documentCategory']) . ' ' . htmlspecialchars($c['documentIdentifier']) ?></td>
                <td><?= htmlspecialchars($c['phoneNumber']) ?></td>
                <td><?= htmlspecialchars($c['emailAddress']) ?></td>
                <td class="acciones">
                  <form method="POST" action="registro_codeudor.php" style="display:inline;">
                    <input type="hidden" name="manage_action" value="load_edit">
                    <input type="hidden" name="target_doc" value="<?= htmlspecialchars($c['documentIdentifier']) ?>">
                    <button type="submit" title="Editar" style="background:none;border:none;color:#7e57c2;cursor:pointer;">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                  </form>
                  <form method="POST" action="registro_codeudor.php" style="display:inline;margin-left:10px;" onsubmit="return confirm('¿Eliminar este codeudor?');">
                    <input type="hidden" name="manage_action" value="delete">
                    <input type="hidden" name="target_doc" value="<?= htmlspecialchars($c['documentIdentifier']) ?>">
                    <button type="submit" title="Eliminar" style="background:none;border:none;color:#e53935;cursor:pointer;">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </form>
                  <form method="POST" action="subir_documentos.php" style="display:inline;margin-left:10px;">
                    <input type="hidden" name="doc" value="<?= htmlspecialchars($c['documentIdentifier']) ?>">
                    <input type="hidden" name="return" value="registro_codeudor.php">
                    <button type="submit" title="Subir documentos" style="background:none;border:none;color:#388e3c;cursor:pointer;">
                      <i class="fa-solid fa-file-upload"></i>
                    </button>
                  </form>
                  <form method="POST" action="ver_documentos.php" style="display:inline;margin-left:10px;">
                    <input type="hidden" name="doc" value="<?= htmlspecialchars($c['documentIdentifier']) ?>">
                    <input type="hidden" name="return" value="registro_codeudor.php">
                    <button type="submit" title="Ver documentos" style="background:none;border:none;color:#1565c0;cursor:pointer;">
                      <i class="fa-solid fa-file-lines"></i>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colspan="5">No hay codeudores registrados</td></tr>
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
