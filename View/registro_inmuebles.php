<?php
require_once __DIR__ . '/../Controller/PropertyController.php';

$controller = new PropertyController();
$alertMessage = null;
$alertType = null;
$editMode = false;
$formData = [
    'propertyId' => null,
    'address' => '',
    'city' => '',
    'registrationIdentifier' => '',
    'rentalValue' => '',
    'utilityContractIdentifiers' => '',
    'occupancyState' => '',
    'propertyType' => '',
    'ownerId' => '',
];

$status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS);
if ($status === 'created') {
    $alertMessage = 'Inmueble registrado correctamente.';
    $alertType = 'success';
} elseif ($status === 'updated') {
    $alertMessage = 'Inmueble actualizado correctamente.';
    $alertType = 'success';
} elseif ($status === 'deleted') {
    $alertMessage = 'Inmueble eliminado correctamente.';
    $alertType = 'success';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $manageAction = trim($_POST['manage_action'] ?? '');

    if ($manageAction === 'delete') {
        $propId = isset($_POST['propertyId']) ? (int)$_POST['propertyId'] : 0;
        $resultado = $controller->eliminar($propId);
        if ($resultado['ok']) {
            header('Location: registro_inmuebles.php?status=deleted');
            exit;
        }
        $alertMessage = $resultado['msg'];
        $alertType = 'error';
    } elseif ($manageAction === 'load_edit') {
        $propId = isset($_POST['propertyId']) ? (int)$_POST['propertyId'] : 0;
        $inmueble = $controller->obtener($propId);
        if ($inmueble) {
            $formData = [
                'propertyId' => (int)$inmueble['propertyId'],
                'address' => $inmueble['address'] ?? '',
                'city' => $inmueble['city'] ?? '',
                'registrationIdentifier' => $inmueble['registrationIdentifier'] ?? '',
                'rentalValue' => $inmueble['rentalValue'] ?? '',
                'utilityContractIdentifiers' => $inmueble['utilityContractIdentifiers'] ?? '',
                'occupancyState' => $inmueble['occupancyState'] ?? '',
                'propertyType' => $inmueble['propertyType'] ?? '',
                'ownerId' => (int)($inmueble['ownerId'] ?? 0),
            ];
            $editMode = true;
        } else {
            $alertMessage = 'No se encontró el inmueble solicitado.';
            $alertType = 'error';
        }
    } elseif (!empty($_POST['form_action'])) {
        $formAction = $_POST['form_action'];
        $data = [
            'propertyId' => isset($_POST['propertyId']) ? (int)$_POST['propertyId'] : null,
            'address' => $_POST['address'] ?? '',
            'city' => $_POST['city'] ?? '',
            'registrationIdentifier' => $_POST['registrationIdentifier'] ?? '',
            'rentalValue' => $_POST['rentalValue'] ?? '',
            'utilityContractIdentifiers' => $_POST['utilityContractIdentifiers'] ?? '',
            'occupancyState' => $_POST['occupancyState'] ?? '',
            'propertyType' => $_POST['propertyType'] ?? '',
            'ownerId' => isset($_POST['ownerId']) ? (int)$_POST['ownerId'] : 0,
        ];

        $resultado = $formAction === 'update'
            ? $controller->actualizar($data)
            : $controller->crear($data);

        if ($resultado['ok']) {
            $target = $formAction === 'update' ? 'updated' : 'created';
            header('Location: registro_inmuebles.php?status=' . $target);
            exit;
        }

        $alertMessage = $resultado['msg'];
        $alertType = 'error';
        $formData = $resultado['data'] ?? $data;
        $editMode = ($formAction === 'update');
    }
}

$inmuebles = $controller->index();
$owners = $controller->propietarios();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel Inmuebles</title>
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
      <a href="registro_propietarios.php"><i class="fas fa-user-tie"></i><span>PROPIETARIOS</span></a>
      <a href="registro_codeudor.php"><i class="fas fa-user-shield"></i><span>CODEUDORES</span></a>
      <a href="registro_inmuebles.php"><i class="fas fa-building"></i><span>INMUEBLES</span></a>
      <a href="#"><i class="fas fa-file-contract"></i><span>CONTRATOS</span></a>
    </nav>
  </aside>

  <!-- MAIN -->
  <main class="main-content">
    <div class="contenedor">
      <div class="header">
        <div>Registro de Inmuebles</div>
        <div class="icono"><i class="fa-solid fa-building"></i></div>
      </div>

      <!-- FORM -->
      <form class="formulario" action="registro_inmuebles.php" method="POST">
        <input type="hidden" name="form_action" value="<?= $editMode ? 'update' : 'create' ?>">
        <?php if ($editMode && !empty($formData['propertyId'])): ?>
          <input type="hidden" name="propertyId" value="<?= (int)$formData['propertyId'] ?>">
        <?php endif; ?>

        <?php if (!empty($alertMessage)): ?>
          <div class="alert <?= $alertType === 'success' ? 'alert-success' : 'alert-error' ?>">
            <?= htmlspecialchars($alertMessage) ?>
          </div>
        <?php endif; ?>

        <div class="campo-grupo">
          <div class="campo">
            <label for="address">Dirección:</label>
            <input type="text" id="address" name="address" required value="<?= htmlspecialchars($formData['address']) ?>">
          </div>
          <div class="campo">
            <label for="city">Ciudad:</label>
            <input type="text" id="city" name="city" required value="<?= htmlspecialchars($formData['city']) ?>">
          </div>
        </div>

        <div class="campo-grupo">
          <div class="campo">
            <label for="registrationIdentifier">Matrícula / Registro:</label>
            <input type="text" id="registrationIdentifier" name="registrationIdentifier" value="<?= htmlspecialchars($formData['registrationIdentifier']) ?>">
          </div>
          <div class="campo">
            <label for="rentalValue">Canon de arriendo:</label>
            <input type="number" step="0.01" id="rentalValue" name="rentalValue" value="<?= htmlspecialchars($formData['rentalValue']) ?>">
          </div>
        </div>

        <div class="campo-grupo">
          <div class="campo">
            <label for="utilityContractIdentifiers">Contratos de servicios:</label>
            <input type="text" id="utilityContractIdentifiers" name="utilityContractIdentifiers" placeholder="Gas:..., Energía:..., Agua:..." value="<?= htmlspecialchars($formData['utilityContractIdentifiers']) ?>">
          </div>
          <div class="campo">
            <label for="occupancyState">Estado ocupación:</label>
            <select id="occupancyState" name="occupancyState">
              <option value="">Seleccione</option>
              <option value="Disponible" <?= ($formData['occupancyState'] === 'Disponible') ? 'selected' : '' ?>>Disponible</option>
              <option value="Ocupado" <?= ($formData['occupancyState'] === 'Ocupado') ? 'selected' : '' ?>>Ocupado</option>
              <option value="Mantenimiento" <?= ($formData['occupancyState'] === 'Mantenimiento') ? 'selected' : '' ?>>Mantenimiento</option>
            </select>
          </div>
        </div>

        <div class="campo-grupo">
          <div class="campo">
            <label for="propertyType">Tipo de inmueble:</label>
            <select id="propertyType" name="propertyType" required>
              <option value="">Seleccione</option>
              <option value="Apartamento" <?= ($formData['propertyType'] === 'Apartamento') ? 'selected' : '' ?>>Apartamento</option>
              <option value="Casa" <?= ($formData['propertyType'] === 'Casa') ? 'selected' : '' ?>>Casa</option>
              <option value="Oficina" <?= ($formData['propertyType'] === 'Oficina') ? 'selected' : '' ?>>Oficina</option>
              <option value="Local" <?= ($formData['propertyType'] === 'Local') ? 'selected' : '' ?>>Local</option>
              <option value="Bodega" <?= ($formData['propertyType'] === 'Bodega') ? 'selected' : '' ?>>Bodega</option>
              <option value="Otro" <?= ($formData['propertyType'] === 'Otro') ? 'selected' : '' ?>>Otro</option>
            </select>
          </div>

          <div class="campo">
            <label for="ownerId">Propietario:</label>
            <select id="ownerId" name="ownerId" required>
              <option value="">Seleccione propietario</option>
              <?php foreach ($owners as $o): ?>
                <option value="<?= (int)$o['personId'] ?>" <?= ((int)$formData['ownerId'] === (int)$o['personId']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($o['fullName']) ?> (<?= htmlspecialchars($o['documentIdentifier']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="botones">
          <button type="submit" class="btn-guardar"><?= $editMode ? 'Actualizar' : 'Guardar' ?></button>
          <?php if ($editMode): ?>
            <a class="btn-limpiar" href="registro_inmuebles.php">Cancelar</a>
          <?php else: ?>
            <button type="reset" class="btn-limpiar">Limpiar</button>
          <?php endif; ?>
        </div>
      </form>

      <!-- LISTADO -->
      <div class="listado">
        <h2>Lista de Inmuebles</h2>
        <div class="buscador">
          <input type="text" placeholder="Buscar inmueble...">
          <i class="fa-solid fa-magnifying-glass"></i>
        </div>

        <table>
          <thead>
            <tr>
              <th>Dirección</th>
              <th>Ciudad</th>
              <th>Tipo</th>
              <th>Ocupación</th>
              <th>Canon</th>
              <th>Propietario</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($inmuebles)): foreach ($inmuebles as $i): ?>
              <tr>
                <td><?= htmlspecialchars($i['address']) ?></td>
                <td><?= htmlspecialchars($i['city']) ?></td>
                <td><?= htmlspecialchars($i['propertyType']) ?></td>
                <td><?= htmlspecialchars($i['occupancyState']) ?></td>
                <td><?= htmlspecialchars($i['rentalValue']) ?></td>
                <td><?= htmlspecialchars($i['ownerName']) ?></td>
                <td class="acciones">
                  <form action="registro_inmuebles.php" method="POST" style="display:inline;">
                    <input type="hidden" name="manage_action" value="load_edit">
                    <input type="hidden" name="propertyId" value="<?= (int)$i['propertyId'] ?>">
                    <button type="submit" title="Editar" style="background:none;border:none;color:#7e57c2;cursor:pointer;">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                  </form>
                  <form action="registro_inmuebles.php" method="POST" style="display:inline;margin-left:10px;" onsubmit="return confirm('¿Eliminar este inmueble?');">
                    <input type="hidden" name="manage_action" value="delete">
                    <input type="hidden" name="propertyId" value="<?= (int)$i['propertyId'] ?>">
                    <button type="submit" title="Eliminar" style="background:none;border:none;color:#e53935;cursor:pointer;">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colspan="7">No hay inmuebles registrados</td></tr>
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
