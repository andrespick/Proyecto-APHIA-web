<?php
require_once __DIR__ . '/../Controller/PropertyController.php';
$controller = new PropertyController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $operation = $_POST['operation'] ?? '';

    if ($operation === 'delete') {
        $propId = isset($_POST['propertyId']) ? (int)$_POST['propertyId'] : 0;
        if ($propId > 0) {
            $controller->eliminar($propId);
        }
        header("Location: registro_inmuebles.php");
        exit;
    }

    if (!empty($_POST['form_action'])) {
        $data = [
            'propertyId' => isset($_POST['propertyId']) ? (int)$_POST['propertyId'] : null,
            'address' => trim($_POST['address'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'registrationIdentifier' => trim($_POST['registrationIdentifier'] ?? ''),
            'rentalValue' => trim($_POST['rentalValue'] ?? ''),
            'utilityContractIdentifiers' => trim($_POST['utilityContractIdentifiers'] ?? ''),
            'occupancyState' => trim($_POST['occupancyState'] ?? ''),
            'propertyType' => trim($_POST['propertyType'] ?? ''),
            'ownerId' => isset($_POST['ownerId']) ? (int)$_POST['ownerId'] : 0,
        ];

        if ($_POST['form_action'] === 'update') {
            $controller->actualizar($data);
        } else {
            $controller->crear($data);
        }
        header("Location: registro_inmuebles.php");
        exit;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    $controller->eliminar((int)$_GET['id']);
    header("Location: registro_inmuebles.php");
    exit;
}

// Editar (cargar datos)
$editMode = false;
$editProp = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && !empty($_GET['id'])) {
    $editProp = $controller->obtener((int)$_GET['id']);
    if ($editProp) $editMode = true;
}

// Datos para la vista
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
        <?php if ($editMode): ?>
          <input type="hidden" name="propertyId" value="<?= (int)$editProp['propertyId'] ?>">
        <?php endif; ?>

        <div class="campo-grupo">
          <div class="campo">
            <label for="address">Dirección:</label>
            <input type="text" id="address" name="address" required value="<?= $editMode ? htmlspecialchars($editProp['address']) : '' ?>">
          </div>
          <div class="campo">
            <label for="city">Ciudad:</label>
            <input type="text" id="city" name="city" required value="<?= $editMode ? htmlspecialchars($editProp['city']) : '' ?>">
          </div>
        </div>

        <div class="campo-grupo">
          <div class="campo">
            <label for="registrationIdentifier">Matrícula / Registro:</label>
            <input type="text" id="registrationIdentifier" name="registrationIdentifier" value="<?= $editMode ? htmlspecialchars($editProp['registrationIdentifier']) : '' ?>">
          </div>
          <div class="campo">
            <label for="rentalValue">Canon de arriendo:</label>
            <input type="number" step="0.01" id="rentalValue" name="rentalValue" value="<?= $editMode ? htmlspecialchars($editProp['rentalValue']) : '' ?>">
          </div>
        </div>

        <div class="campo-grupo">
          <div class="campo">
            <label for="utilityContractIdentifiers">Contratos de servicios:</label>
            <input type="text" id="utilityContractIdentifiers" name="utilityContractIdentifiers" placeholder="Gas:..., Energía:..., Agua:..." value="<?= $editMode ? htmlspecialchars($editProp['utilityContractIdentifiers']) : '' ?>">
          </div>
          <div class="campo">
            <label for="occupancyState">Estado ocupación:</label>
            <select id="occupancyState" name="occupancyState">
              <option value="">Seleccione</option>
              <option value="Disponible" <?= ($editMode && $editProp['occupancyState']=='Disponible') ? 'selected' : '' ?>>Disponible</option>
              <option value="Ocupado" <?= ($editMode && $editProp['occupancyState']=='Ocupado') ? 'selected' : '' ?>>Ocupado</option>
              <option value="Mantenimiento" <?= ($editMode && $editProp['occupancyState']=='Mantenimiento') ? 'selected' : '' ?>>Mantenimiento</option>
            </select>
          </div>
        </div>

        <div class="campo-grupo">
          <div class="campo">
            <label for="propertyType">Tipo de inmueble:</label>
            <select id="propertyType" name="propertyType" required>
              <option value="">Seleccione</option>
              <option value="Apartamento" <?= ($editMode && $editProp['propertyType']=='Apartamento') ? 'selected' : '' ?>>Apartamento</option>
              <option value="Casa" <?= ($editMode && $editProp['propertyType']=='Casa') ? 'selected' : '' ?>>Casa</option>
              <option value="Oficina" <?= ($editMode && $editProp['propertyType']=='Oficina') ? 'selected' : '' ?>>Oficina</option>
              <option value="Local" <?= ($editMode && $editProp['propertyType']=='Local') ? 'selected' : '' ?>>Local</option>
              <option value="Bodega" <?= ($editMode && $editProp['propertyType']=='Bodega') ? 'selected' : '' ?>>Bodega</option>
              <option value="Otro" <?= ($editMode && $editProp['propertyType']=='Otro') ? 'selected' : '' ?>>Otro</option>
            </select>
          </div>

          <div class="campo">
            <label for="ownerId">Propietario:</label>
            <select id="ownerId" name="ownerId" required>
              <option value="">Seleccione propietario</option>
              <?php foreach ($owners as $o): ?>
                <option value="<?= (int)$o['personId'] ?>" 
                  <?= ($editMode && (int)$editProp['ownerId']===(int)$o['personId']) ? 'selected' : '' ?>>
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
                  <a href="registro_inmuebles.php?action=edit&id=<?= (int)$i['propertyId'] ?>" title="Editar" style="color:#7e57c2;"><i class="fa-solid fa-pen-to-square"></i></a>
                  <form action="registro_inmuebles.php" method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar este inmueble?');">
                    <input type="hidden" name="operation" value="delete">
                    <input type="hidden" name="propertyId" value="<?= (int)$i['propertyId'] ?>">
                    <button type="submit" title="Eliminar" style="background:none;border:none;padding:0;margin-left:10px;color:#e53935;cursor:pointer;">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </form>
                  <!-- Subir documentos del inmueble -->
                  <a href="subir_documentos_inmueble.php?prop=<?= (int)$i['propertyId'] ?>" title="Subir documentos" style="margin-left:10px;color:#388e3c;"><i class="fa-solid fa-file-upload"></i></a>
                  <!-- Ver documentos -->
                  <a href="ver_documentos.php?prop=<?= (int)$i['propertyId'] ?>&return=registro_inmuebles.php" title="Ver documentos" style="margin-left:10px;color:#1565c0;"><i class="fa-solid fa-file-lines"></i></a>
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

<!-- Buscador + paginación + visor de documentos -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  // Filtro + paginación
  const searchInput = document.querySelector(".buscador input");
  const table = document.querySelector(".listado table");
  const rows = Array.from(table.querySelectorAll("tbody tr"));
  const rowsPerPage = 10;
  let currentPage = 1;

  const paginationContainer = document.createElement("div");
  paginationContainer.classList.add("paginacion");
  paginationContainer.style.textAlign = "center";
  paginationContainer.style.marginTop = "15px";
  table.parentElement.appendChild(paginationContainer);

  function renderTable() {
    const term = (searchInput.value || "").toLowerCase().trim();
    const filtered = rows.filter(r => r.textContent.toLowerCase().includes(term));
    const totalPages = Math.ceil(filtered.length / rowsPerPage) || 1;
    currentPage = Math.min(currentPage, totalPages) || 1;

    rows.forEach(r => r.style.display = "none");
    const start = (currentPage - 1) * rowsPerPage;
    filtered.slice(start, start + rowsPerPage).forEach(r => r.style.display = "");

    renderPagination(totalPages);
  }
  function renderPagination(totalPages) {
    paginationContainer.innerHTML = "";
    if (totalPages <= 1) return;

    const prev = document.createElement("button");
    prev.textContent = "← Anterior";
    prev.disabled = (currentPage === 1);
    prev.onclick = () => { currentPage--; renderTable(); };
    paginationContainer.appendChild(prev);

    for (let i=1; i<=totalPages; i++) {
      const b = document.createElement("button");
      b.textContent = i;
      b.className = (i === currentPage ? "activo" : "");
      b.onclick = () => { currentPage = i; renderTable(); };
      paginationContainer.appendChild(b);
    }

    const next = document.createElement("button");
    next.textContent = "Siguiente →";
    next.disabled = (currentPage === totalPages);
    next.onclick = () => { currentPage++; renderTable(); };
    paginationContainer.appendChild(next);
  }
  searchInput.addEventListener("input", () => { currentPage = 1; renderTable(); });
  renderTable();
});
</script>
</body>
</html>
