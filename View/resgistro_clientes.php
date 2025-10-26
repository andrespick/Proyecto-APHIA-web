<?php
require_once __DIR__ . '/../Controller/clientesController.php';
$controller = new ClienteController();

// Acción: eliminar
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['doc'])) {
    $doc = $_GET['doc'];
    $controller->eliminar($doc);
    header("Location: resgistro_clientes.php");
    exit;
}

// Acción: editar
$editMode = false;
$editClient = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && !empty($_GET['doc'])) {
    $doc = $_GET['doc'];
    $editClient = $controller->obtenerPorDocumento($doc);
    if ($editClient) {
        $parts = explode(' ', $editClient['fullName'], 2);
        $editClient['nombre'] = $parts[0] ?? '';
        $editClient['apellido'] = $parts[1] ?? '';
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
        'genero' => $_POST['genero'] ?? ''
    ];

    if (!empty($_POST['form_action']) && $_POST['form_action'] === 'update') {
        $controller->actualizar($data);
    } else {
        $controller->guardar($data);
    }
    header("Location: resgistro_clientes.php");
    exit;
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
        <a href="registro_propietarios.php"><i class="fas fa-user-tie"></i><span>PROPIETARIOS</span></a>
        <a href="#"><i class="fas fa-user-shield"></i><span>CODEUDORES</span></a>
        <a href="#"><i class="fas fa-building"></i><span>INMUEBLES</span></a>
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
          <div class="campo-grupo">
            <div class="campo">
              <label for="nombre">Nombre:</label>
              <input type="text" id="nombre" name="nombre" required value="<?= $editMode ? htmlspecialchars($editClient['nombre']) : '' ?>">
            </div>
            <div class="campo">
              <label for="apellido">Apellido:</label>
              <input type="text" id="apellido" name="apellido" required value="<?= $editMode ? htmlspecialchars($editClient['apellido']) : '' ?>">
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
              <input type="text" id="numero_doc" name="numero_doc" required value="<?= $editMode ? htmlspecialchars($editClient['documentIdentifier']) : '' ?>" <?= $editMode ? 'readonly' : '' ?>>
            </div>
          </div>

          <div class="campo-grupo">
            <div class="campo">
              <label for="telefono">Teléfono:</label>
              <input type="text" id="telefono" name="telefono" required value="<?= $editMode ? htmlspecialchars($editClient['phoneNumber']) : '' ?>">
            </div>
            <div class="campo">
              <label for="email">Correo electrónico:</label>
              <input type="email" id="email" name="email" required value="<?= $editMode ? htmlspecialchars($editClient['emailAddress']) : '' ?>">
            </div>
          </div>

          <div class="campo-grupo">
            <div class="campo">
              <label for="direccion">Dirección:</label>
              <input type="text" id="direccion" name="direccion" required value="<?= $editMode ? htmlspecialchars($editClient['address']) : '' ?>">
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
                      <a href="resgistro_clientes.php?action=edit&doc=<?= urlencode($c['documentIdentifier']) ?>" title="Editar" style="color:#7e57c2;">
                        <i class="fa-solid fa-pen-to-square"></i>
                      </a>
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

  <!-- FILTRO Y PAGINACIÓN -->
  <script>
  document.addEventListener("DOMContentLoaded", function() {
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
      const searchTerm = searchInput.value.toLowerCase().trim();
      const filteredRows = rows.filter(row =>
        row.textContent.toLowerCase().includes(searchTerm)
      );

      const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
      currentPage = Math.min(currentPage, totalPages) || 1;

      rows.forEach(row => row.style.display = "none");
      const start = (currentPage - 1) * rowsPerPage;
      const end = start + rowsPerPage;
      filteredRows.slice(start, end).forEach(row => {
        row.style.display = "";
      });

      renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
      paginationContainer.innerHTML = "";
      if (totalPages <= 1) return;

      const prev = document.createElement("button");
      prev.textContent = "← Anterior";
      prev.disabled = currentPage === 1;
      prev.onclick = () => { currentPage--; renderTable(); };
      paginationContainer.appendChild(prev);

      for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement("button");
        btn.textContent = i;
        btn.className = (i === currentPage ? "activo" : "");
        btn.onclick = () => { currentPage = i; renderTable(); };
        paginationContainer.appendChild(btn);
      }

      const next = document.createElement("button");
      next.textContent = "Siguiente →";
      next.disabled = currentPage === totalPages;
      next.onclick = () => { currentPage++; renderTable(); };
      paginationContainer.appendChild(next);
    }

    const style = document.createElement("style");
    style.textContent = `
      .paginacion button {
        margin: 0 3px;
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        background-color: #f44336;
        color: white;
        cursor: pointer;
        font-weight: 500;
        transition: background-color 0.3s;
      }
      .paginacion button:hover:not(:disabled) { background-color: #d32f2f; }
      .paginacion button:disabled { background-color: #ccc; cursor: not-allowed; }
      .paginacion button.activo { background-color: #b71c1c; }
    `;
    document.head.appendChild(style);

    searchInput.addEventListener("input", function() {
      currentPage = 1;
      renderTable();
    });

    renderTable();
  });
  </script>
</body>
</html>
