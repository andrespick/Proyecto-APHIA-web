<?php
require_once __DIR__ . '/../Controller/propietariosController.php';
$controller = new PropietarioController();

// Acción: eliminar propietario
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['doc'])) {
    $controller->eliminar($_GET['doc']);
    header("Location: registro_propietarios.php");
    exit;
}

// Acción: editar
$editMode = false;
$editProp = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && !empty($_GET['doc'])) {
    $editProp = $controller->obtenerPorDocumento($_GET['doc']);
    if ($editProp) {
        $parts = explode(' ', $editProp['fullName'], 2);
        $editProp['nombre'] = $parts[0] ?? '';
        $editProp['apellido'] = $parts[1] ?? '';
        $editMode = true;
    }
}

// Guardar o actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        'entidad_bancaria' => $_POST['entidad_bancaria'] ?? ''
    ];

    if (!empty($_POST['form_action']) && $_POST['form_action'] === 'update') {
        $controller->actualizar($data);
    } else {
        $controller->guardar($data);
    }
    header("Location: registro_propietarios.php");
    exit;
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
  <link rel="shortcut icon" href="../img/logo.svg" />
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
        <a href="#"><i class="fas fa-user-shield"></i><span>CODEUDORES</span></a>
        <a href="#"><i class="fas fa-building"></i><span>INMUEBLES</span></a>
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

          <!-- Datos personales -->
          <div class="campo-grupo">
            <div class="campo">
              <label for="nombre">Nombre:</label>
              <input type="text" id="nombre" name="nombre" required value="<?= $editMode ? htmlspecialchars($editProp['nombre']) : '' ?>">
            </div>
            <div class="campo">
              <label for="apellido">Apellido:</label>
              <input type="text" id="apellido" name="apellido" required value="<?= $editMode ? htmlspecialchars($editProp['apellido']) : '' ?>">
            </div>
          </div>

          <div class="campo-grupo">
            <div class="campo">
              <label for="tipo_doc">Tipo de documento:</label>
              <select id="tipo_doc" name="tipo_doc" required>
                <option value="">Seleccione</option>
                <option value="CC" <?= ($editMode && $editProp['documentCategory']=='CC') ? 'selected' : '' ?>>Cédula</option>
                <option value="CE" <?= ($editMode && $editProp['documentCategory']=='CE') ? 'selected' : '' ?>>Cédula de Extranjería</option>
                <option value="NIT" <?= ($editMode && $editProp['documentCategory']=='NIT') ? 'selected' : '' ?>>NIT</option>
                <option value="PAS" <?= ($editMode && $editProp['documentCategory']=='PAS') ? 'selected' : '' ?>>Pasaporte</option>
              </select>
            </div>
            <div class="campo">
              <label for="numero_doc">Número de documento:</label>
              <input type="text" id="numero_doc" name="numero_doc" required value="<?= $editMode ? htmlspecialchars($editProp['documentIdentifier']) : '' ?>" <?= $editMode ? 'readonly' : '' ?>>
            </div>
          </div>

          <div class="campo-grupo">
            <div class="campo">
              <label for="telefono">Teléfono:</label>
              <input type="text" id="telefono" name="telefono" required value="<?= $editMode ? htmlspecialchars($editProp['phoneNumber']) : '' ?>">
            </div>
            <div class="campo">
              <label for="email">Correo electrónico:</label>
              <input type="email" id="email" name="email" required value="<?= $editMode ? htmlspecialchars($editProp['emailAddress']) : '' ?>">
            </div>
          </div>

          <div class="campo-grupo">
            <div class="campo">
              <label for="direccion">Dirección:</label>
              <input type="text" id="direccion" name="direccion" required value="<?= $editMode ? htmlspecialchars($editProp['address']) : '' ?>">
            </div>
          </div>

          <!-- Datos bancarios -->
          <h3 style="margin-top:20px;color:#444;">Datos Bancarios</h3>
          <div class="campo-grupo">
            <div class="campo">
              <label for="numero_cuenta">Número de cuenta:</label>
              <input type="text" id="numero_cuenta" name="numero_cuenta" value="<?= $editMode ? htmlspecialchars($editProp['accountIdentifier'] ?? '') : '' ?>">
            </div>
            <div class="campo">
              <label for="tipo_cuenta">Tipo de cuenta:</label>
              <select id="tipo_cuenta" name="tipo_cuenta">
                <option value="">Seleccione</option>
                <option value="Ahorros" <?= ($editMode && ($editProp['accountCategory'] ?? '')=='Ahorros') ? 'selected' : '' ?>>Ahorros</option>
                <option value="Corriente" <?= ($editMode && ($editProp['accountCategory'] ?? '')=='Corriente') ? 'selected' : '' ?>>Corriente</option>
                <option value="Digital" <?= ($editMode && ($editProp['accountCategory'] ?? '')=='Digital') ? 'selected' : '' ?>>Digital</option>
              </select>
            </div>
          </div>

          <div class="campo-grupo">
            <div class="campo">
              <label for="entidad_bancaria">Entidad bancaria:</label>
              <select id="entidad_bancaria" name="entidad_bancaria">
                <option value="">Seleccione</option>
                <option value="Bancolombia" <?= ($editMode && ($editProp['financialInstitution'] ?? '')=='Bancolombia') ? 'selected' : '' ?>>Bancolombia</option>
                <option value="Nequi" <?= ($editMode && ($editProp['financialInstitution'] ?? '')=='Nequi') ? 'selected' : '' ?>>Nequi</option>
                <option value="BBVA" <?= ($editMode && ($editProp['financialInstitution'] ?? '')=='BBVA') ? 'selected' : '' ?>>BBVA</option>
                <option value="Davivienda" <?= ($editMode && ($editProp['financialInstitution'] ?? '')=='Davivienda') ? 'selected' : '' ?>>Davivienda</option>
                <option value="Otro" <?= ($editMode && ($editProp['financialInstitution'] ?? '')=='Otro') ? 'selected' : '' ?>>Otro</option>
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
              <?php if (!empty($propietarios)): foreach ($propietarios as $p): ?>
                <tr>
                  <td><?= htmlspecialchars($p['fullName']) ?></td>
                  <td><?= htmlspecialchars($p['documentCategory']) . ' ' . htmlspecialchars($p['documentIdentifier']) ?></td>
                  <td><?= htmlspecialchars($p['phoneNumber']) ?></td>
                  <td><?= htmlspecialchars($p['emailAddress']) ?></td>
                  <td><?= htmlspecialchars($p['accountIdentifier'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($p['financialInstitution'] ?? '-') ?></td>
                  <td class="acciones">
                    <a href="registro_propietarios.php?action=edit&doc=<?= urlencode($p['documentIdentifier']) ?>" title="Editar" style="color:#7e57c2;"><i class="fa-solid fa-pen-to-square"></i></a>
                    <a href="registro_propietarios.php?action=delete&doc=<?= urlencode($p['documentIdentifier']) ?>" onclick="return confirm('¿Eliminar este propietario?');" title="Eliminar" style="margin-left:10px;color:#e53935;"><i class="fa-solid fa-trash"></i></a>
                    <a href="subir_documentos.php?doc=<?= urlencode($p['documentIdentifier']) ?>" title="Subir documentos" style="margin-left:10px;color:#388e3c;"><i class="fa-solid fa-file-upload"></i></a>
                    <a href="ver_documentos.php?doc=<?= urlencode($p['documentIdentifier']) ?>" title="Ver documentos" style="margin-left:10px;color:#1565c0;"><i class="fa-solid fa-file-lines"></i></a>
                  </td>
                </tr>
              <?php endforeach; else: ?>
                <tr><td colspan="7">No hay propietarios registrados</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</body>
</html>