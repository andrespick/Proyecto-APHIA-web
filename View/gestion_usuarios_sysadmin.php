<?php
require_once __DIR__ . '/../Controller/SystemAdminController.php';

$controller = new SystemAdminController();
$alertMessage = null;
$alertType = null;
$editMode = false;
$usuarioEditar = null;
$formData = [
    'userName' => '',
    'documentCategory' => '',
    'documentIdentifier' => '',
    'emailAddress' => '',
    'hashedPassword' => '',
];

if (isset($_GET['status'])) {
    $status = $_GET['status'];
    if ($status === 'created') {
        $alertMessage = 'Usuario creado correctamente.';
        $alertType = 'success';
    } elseif ($status === 'updated') {
        $alertMessage = 'Usuario actualizado correctamente.';
        $alertType = 'success';
    } elseif ($status === 'state-changed') {
        $alertMessage = 'Estado del usuario actualizado.';
        $alertType = 'success';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formAction = $_POST['form_action'] ?? 'create';

    if ($formAction === 'create') {
        $data = [
            'userName' => trim($_POST['userName'] ?? ''),
            'documentCategory' => $_POST['documentCategory'] ?? '',
            'documentIdentifier' => trim($_POST['documentIdentifier'] ?? ''),
            'emailAddress' => trim($_POST['emailAddress'] ?? ''),
            'hashedPassword' => $_POST['hashedPassword'] ?? '',
            'state' => 'ACTIVE',
        ];

        $resultado = $controller->crear($data);
        if ($resultado['ok']) {
            header('Location: gestion_usuarios_sysadmin.php?status=created');
            exit;
        } else {
            $alertMessage = $resultado['msg'];
            $alertType = 'error';
            $formData = $data;
        }
    } elseif ($formAction === 'update') {
        $accountId = (int)($_POST['accountId'] ?? 0);
        $data = [
            'userName' => trim($_POST['userName'] ?? ''),
            'documentCategory' => $_POST['documentCategory'] ?? '',
            'documentIdentifier' => trim($_POST['documentIdentifier'] ?? ''),
            'emailAddress' => trim($_POST['emailAddress'] ?? ''),
            'hashedPassword' => $_POST['hashedPassword'] ?? '',
        ];

        $resultado = $controller->actualizar($accountId, $data);
        if ($resultado['ok']) {
            header('Location: gestion_usuarios_sysadmin.php?status=updated');
            exit;
        } else {
            $alertMessage = $resultado['msg'];
            $alertType = 'error';
            $formData = $data;
            $editMode = true;
            $usuarioEditar = $controller->obtenerPorId($accountId);
        }
    } elseif ($formAction === 'toggle') {
        $accountId = (int)($_POST['accountId'] ?? 0);
        $nuevoEstado = $_POST['state'] ?? 'INACTIVE';
        $controller->cambiarEstado($accountId, $nuevoEstado);
        header('Location: gestion_usuarios_sysadmin.php?status=state-changed');
        exit;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'edit' && !empty($_GET['id'])) {
    $usuarioEditar = $controller->obtenerPorId((int)$_GET['id']);
    if ($usuarioEditar) {
        $editMode = true;
        $formData = [
            'userName' => $usuarioEditar['userName'],
            'documentCategory' => $usuarioEditar['documentCategory'],
            'documentIdentifier' => $usuarioEditar['documentIdentifier'],
            'emailAddress' => $usuarioEditar['emailAddress'],
            'hashedPassword' => '',
        ];
    }
}

if (!$editMode && empty($alertMessage)) {
    $formData = [
        'userName' => '',
        'documentCategory' => '',
        'documentIdentifier' => '',
        'emailAddress' => '',
        'hashedPassword' => '',
    ];
}

$usuarios = $controller->index();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Usuarios - System Administrator</title>
  <link rel="stylesheet" href="Styles/style_administrator_dashboard.css">
  <link rel="stylesheet" href="Styles/registro_cliente.css">
  <link rel="stylesheet" href="Styles/system_admin_users.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="shortcut icon" href="img/logo.svg" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="container">
    <aside class="sidebar">
      <div class="user"><i class="fa-solid fa-gears"></i><span>System Administrator</span></div>
      <nav class="menu">
        <a href="#"><i class="fas fa-users-cog"></i><span>USUARIOS</span></a>
        <a href="#"><i class="fas fa-database"></i><span>CONFIGURACIÓN</span></a>
        <a href="#"><i class="fas fa-chart-line"></i><span>REPORTES</span></a>
        <a href="#"><i class="fas fa-shield-alt"></i><span>SEGURIDAD</span></a>
      </nav>
    </aside>

    <main class="main-content">
      <div class="contenedor">
        <div class="header">
          <div>
            Gestión de Usuarios
            <span class="subtitle">Administración de cuentas System Administrator</span>
          </div>
          <div class="icono"><i class="fa-solid fa-user-gear"></i></div>
        </div>

        <form class="formulario" action="gestion_usuarios_sysadmin.php" method="POST">
          <input type="hidden" name="form_action" value="<?= $editMode ? 'update' : 'create' ?>">
          <?php if ($editMode && $usuarioEditar): ?>
            <input type="hidden" name="accountId" value="<?= (int)$usuarioEditar['accountId'] ?>">
          <?php endif; ?>

          <div class="campo-grupo">
            <div class="campo">
              <label for="userName">Username:</label>
              <input type="text" id="userName" name="userName" required value="<?= htmlspecialchars($formData['userName'] ?? '') ?>">
            </div>
            <div class="campo">
              <label for="documentCategory">Tipo de documento:</label>
              <select id="documentCategory" name="documentCategory" required>
                <option value="">Seleccione</option>
                <option value="CC" <?= (($formData['documentCategory'] ?? '') === 'CC') ? 'selected' : '' ?>>Cédula</option>
                <option value="CE" <?= (($formData['documentCategory'] ?? '') === 'CE') ? 'selected' : '' ?>>Cédula de extranjería</option>
                <option value="NIT" <?= (($formData['documentCategory'] ?? '') === 'NIT') ? 'selected' : '' ?>>NIT</option>
                <option value="PAS" <?= (($formData['documentCategory'] ?? '') === 'PAS') ? 'selected' : '' ?>>Pasaporte</option>
                <option value="ID" <?= (($formData['documentCategory'] ?? '') === 'ID') ? 'selected' : '' ?>>Identificación</option>
              </select>
            </div>
          </div>

          <div class="campo-grupo">
            <div class="campo">
              <label for="documentIdentifier">Número de documento:</label>
              <input type="text" id="documentIdentifier" name="documentIdentifier" required value="<?= htmlspecialchars($formData['documentIdentifier'] ?? '') ?>">
            </div>
            <div class="campo">
              <label for="emailAddress">Correo electrónico:</label>
              <input type="email" id="emailAddress" name="emailAddress" required value="<?= htmlspecialchars($formData['emailAddress'] ?? '') ?>">
            </div>
          </div>

          <div class="campo-grupo">
            <div class="campo">
              <label for="hashedPassword">Contraseña <?= $editMode ? '(dejar en blanco para mantener)' : '' ?>:</label>
              <input type="password" id="hashedPassword" name="hashedPassword" <?= $editMode ? '' : 'required' ?> value="">
            </div>
          </div>

          <div class="botones">
            <button type="submit" class="btn-guardar"><?= $editMode ? 'Actualizar' : 'Guardar' ?></button>
            <?php if ($editMode): ?>
              <a class="btn-limpiar" href="gestion_usuarios_sysadmin.php">Cancelar</a>
            <?php else: ?>
              <button type="reset" class="btn-limpiar">Limpiar</button>
            <?php endif; ?>
          </div>

          <?php if (!empty($alertMessage)): ?>
            <div class="alert <?= $alertType === 'success' ? 'alert-success' : 'alert-error' ?>">
              <?= htmlspecialchars($alertMessage) ?>
            </div>
          <?php endif; ?>
        </form>

        <div class="listado">
          <h2>Listado de System Administrators</h2>
          <div class="buscador">
            <input type="text" placeholder="Buscar usuario...">
            <i class="fa-solid fa-magnifying-glass"></i>
          </div>

          <table>
            <thead>
              <tr>
                <th>Username</th>
                <th>Documento</th>
                <th>Correo</th>
                <th>Contraseña</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($usuarios)): ?>
                <?php foreach ($usuarios as $usuario): ?>
                  <tr>
                    <td><?= htmlspecialchars($usuario['userName']) ?></td>
                    <td><?= htmlspecialchars($usuario['documentCategory']) ?> <?= htmlspecialchars($usuario['documentIdentifier']) ?></td>
                    <td><?= htmlspecialchars($usuario['emailAddress']) ?></td>
                    <td><?= str_repeat('•', min(10, strlen($usuario['hashedPassword']))) ?></td>
                    <td>
                      <form class="estado-toggle" method="POST" action="gestion_usuarios_sysadmin.php">
                        <input type="hidden" name="form_action" value="toggle">
                        <input type="hidden" name="accountId" value="<?= (int)$usuario['accountId'] ?>">
                        <input type="hidden" name="state" value="<?= $usuario['state'] === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE' ?>">
                        <label class="switch">
                          <input type="checkbox" <?= $usuario['state'] === 'ACTIVE' ? 'checked' : '' ?> onchange="this.form.state.value = this.checked ? 'ACTIVE' : 'INACTIVE'; this.form.submit();">
                          <span class="slider"></span>
                        </label>
                      </form>
                    </td>
                    <td>
                      <div class="table-actions">
                        <a class="btn-editar" href="gestion_usuarios_sysadmin.php?action=edit&id=<?= (int)$usuario['accountId'] ?>" title="Editar">
                          <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6">No hay usuarios registrados.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
