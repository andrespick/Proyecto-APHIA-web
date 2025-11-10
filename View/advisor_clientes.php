<?php
require_once __DIR__ . '/../Controller/AdvisorClienteController.php';
$controller = new AdvisorClienteController();
$clientes = $controller->listar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Clientes - Asesor</title>
  <link rel="stylesheet" href="Styles/style_administrator_dashboard.css">
  <link rel="stylesheet" href="Styles/registro_cliente.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="shortcut icon" href="img/logo.svg" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="container">
    <aside class="sidebar">
      <div class="user"><i class="fa-solid fa-user-tie"></i><span>Asesor</span></div>
      <nav class="menu">
        <a href="#" class="activo"><i class="fas fa-users"></i><span>CLIENTES</span></a>
        <a href="advisor_propietarios.php"><i class="fas fa-user-tie"></i><span>PROPIETARIOS</span></a>
        <a href="advisor_codeudores.php"><i class="fas fa-user-shield"></i><span>CODEUDORES</span></a>
        <a href="advisor_inmuebles.php"><i class="fas fa-building"></i><span>INMUEBLES</span></a>
      </nav>
    </aside>

    <main class="main-content">
      <div class="contenedor">
        <div class="header">
          <div>Consulta de Clientes</div>
          <div class="icono"><i class="fa-solid fa-users"></i></div>
        </div>

        <div class="listado">
          <h2>Lista de Clientes</h2>
          <div class="buscador">
            <input type="text" id="buscador-clientes" placeholder="Buscar cliente...">
            <i class="fa-solid fa-magnifying-glass"></i>
          </div>

          <table>
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Documento</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Dirección</th>
                <th>Documentos</th>
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
                    <td><?= htmlspecialchars($c['address']) ?></td>
                    <td class="acciones">
                      <form method="POST" action="ver_documentos.php" style="display:inline;">
                        <input type="hidden" name="doc" value="<?= htmlspecialchars($c['documentIdentifier']) ?>">
                        <input type="hidden" name="return" value="advisor_clientes.php">
                        <button type="submit" title="Ver documentos" style="background:none;border:none;color:#1565c0;cursor:pointer;">
                          <i class="fa-solid fa-file-lines"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6">No hay clientes registrados</td>
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
      const searchInput = document.getElementById('buscador-clientes');
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
