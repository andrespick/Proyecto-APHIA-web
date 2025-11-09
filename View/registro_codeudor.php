<?php
require_once __DIR__ . '/../Controller/codeudorController.php';
$controller = new CodeudorController();
$editMode = false;
$editCodeudor = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $manageAction = $_POST['manage_action'] ?? '';

    if ($manageAction === 'delete') {
        $doc = trim($_POST['target_doc'] ?? '');
        if ($doc !== '') {
            $controller->eliminar($doc);
        }
        header("Location: registro_codeudor.php");
        exit;
    }

    if ($manageAction === 'load_edit') {
        $doc = trim($_POST['target_doc'] ?? '');
        if ($doc !== '') {
            $editCodeudor = $controller->obtenerPorDocumento($doc);
            if ($editCodeudor) {
                $parts = explode(' ', $editCodeudor['fullName'], 2);
                $editCodeudor['nombre'] = $parts[0] ?? '';
                $editCodeudor['apellido'] = $parts[1] ?? '';
                $editMode = true;
            }
        }
    } elseif (!empty($_POST['form_action'])) {
        $data = [
            'nombre' => $_POST['nombre'] ?? '',
            'apellido' => $_POST['apellido'] ?? '',
            'tipo_doc' => $_POST['tipo_doc'] ?? '',
            'numero_doc' => $_POST['numero_doc'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'email' => $_POST['email'] ?? '',
            'direccion' => $_POST['direccion'] ?? ''
        ];

        if ($_POST['form_action'] === 'update') {
            $controller->actualizar($data);
        } else {
            $controller->guardar($data);
        }
        header("Location: registro_codeudor.php");
        exit;
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

        <div class="campo-grupo">
          <div class="campo">
            <label>Nombre:</label>
            <input type="text" name="nombre" required value="<?= $editMode ? htmlspecialchars($editCodeudor['nombre']) : '' ?>">
          </div>
          <div class="campo">
            <label>Apellido:</label>
            <input type="text" name="apellido" required value="<?= $editMode ? htmlspecialchars($editCodeudor['apellido']) : '' ?>">
          </div>
        </div>

        <div class="campo-grupo">
          <div class="campo">
            <label>Tipo documento:</label>
            <select name="tipo_doc" required>
              <option value="">Seleccione</option>
              <option value="CC" <?= ($editMode && $editCodeudor['documentCategory']=='CC') ? 'selected' : '' ?>>Cédula</option>
              <option value="CE" <?= ($editMode && $editCodeudor['documentCategory']=='CE') ? 'selected' : '' ?>>Cédula de Extranjería</option>
              <option value="PAS" <?= ($editMode && $editCodeudor['documentCategory']=='PAS') ? 'selected' : '' ?>>Pasaporte</option>
            </select>
          </div>
          <div class="campo">
            <label>Número documento:</label>
            <input type="text" name="numero_doc" required value="<?= $editMode ? htmlspecialchars($editCodeudor['documentIdentifier']) : '' ?>" <?= $editMode ? 'readonly' : '' ?>>
          </div>
        </div>

        <div class="campo-grupo">
          <div class="campo">
            <label>Teléfono:</label>
            <input type="text" name="telefono" required value="<?= $editMode ? htmlspecialchars($editCodeudor['phoneNumber']) : '' ?>">
          </div>
          <div class="campo">
            <label>Correo:</label>
            <input type="email" name="email" required value="<?= $editMode ? htmlspecialchars($editCodeudor['emailAddress']) : '' ?>">
          </div>
        </div>

        <div class="campo">
          <label>Dirección:</label>
          <input type="text" name="direccion" required value="<?= $editMode ? htmlspecialchars($editCodeudor['address']) : '' ?>">
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
                <td><?= htmlspecialchars($c['documentCategory']).' '.htmlspecialchars($c['documentIdentifier']) ?></td>
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

</body>
</html>
