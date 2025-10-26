<?php
require_once __DIR__ . '/../Model/conexions.php';
$conn = (new Conexion())->conectar();

$doc = $_GET['doc'] ?? null;
if (!$doc) die("Documento no especificado.");

// Obtener personId
$stmt = $conn->prepare("SELECT personId, fullName FROM person WHERE documentIdentifier = ?");
$stmt->bind_param("s", $doc);
$stmt->execute();
$res = $stmt->get_result();
$person = $res->fetch_assoc();
if (!$person) die("Propietario no encontrado.");

$msg = "";

// 🔹 Eliminar documento
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $filePath = $_GET['delete'];
    
    // Eliminar de BD
    $del = $conn->prepare("DELETE FROM file_record WHERE personId=? AND filePath=?");
    $del->bind_param("is", $person['personId'], $filePath);
    $del->execute();
    
    // Eliminar archivo físico
    $rutaAbsoluta = __DIR__ . '/' . ltrim($filePath, './');
    if (file_exists($rutaAbsoluta)) {
        unlink($rutaAbsoluta);
    }
    
    $msg = "✅ Archivo eliminado correctamente.";
}

// Obtener lista
$stmt = $conn->prepare("SELECT filePath, uploadDate FROM file_record WHERE personId=? ORDER BY uploadDate DESC");
$stmt->bind_param("i", $person['personId']);
$stmt->execute();
$files = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Documentos - <?= htmlspecialchars($person['fullName']) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: #f5f6fa;
  padding: 40px;
}
.container {
  background: #fff;
  padding: 30px;
  border-radius: 15px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  max-width: 800px;
  margin: auto;
}
h2 {
  color: #333;
  margin-bottom: 15px;
}
p {
  color: #666;
  font-size: 15px;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin: 20px 0;
}
th {
  background: #f44336;;
  color: white;
  padding: 12px;
  text-align: left;
  font-weight: 600;
}
td {
  padding: 10px 12px;
  border-bottom: 1px solid #e0e0e0;
}
tr:hover {
  background: #f5f5f5;
}
td a {
  margin-right: 10px;
  text-decoration: none;
  font-size: 16px;
  transition: transform 0.2s;
  display: inline-block;
}
td a:hover {
  transform: scale(1.2);
}
.msg {
  margin-top: 15px;
  padding: 12px;
  background: #d4edda;
  color: #155724;
  border-radius: 8px;
  border: 1px solid #c3e6cb;
}
.no-docs {
  text-align: center;
  padding: 40px;
  color: #999;
}
a.volver {
  display: inline-block;
  padding: 10px 20px;
  margin-top: 20px;
  background: #1976d2;
  color: white;
  text-decoration: none;
  border-radius: 10px;
  font-weight: 600;
  transition: background-color 0.3s;
}
a.volver:hover {
  background: #0d47a1;
}
</style>
<link rel="shortcut icon" href="../img/logo.svg" />
</head>
<body>
  <div class="container">
    <h2><i class="fa-solid fa-folder-open"></i> Documentos</h2>
    <p><b>Propietario:</b> <?= htmlspecialchars($person['fullName']) ?></p>

    <?php if ($msg): ?>
      <div class="msg"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <?php if ($files): ?>
      <table>
        <thead>
          <tr>
            <th><i class="fa-solid fa-file"></i> Archivo</th>
            <th><i class="fa-solid fa-calendar"></i> Fecha</th>
            <th><i class="fa-solid fa-wrench"></i> Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($files as $f): ?>
            <tr>
              <td><?= htmlspecialchars(basename($f['filePath'])) ?></td>
              <td><?= date('d/m/Y', strtotime($f['uploadDate'])) ?></td>
              <td>
                <a href="<?= htmlspecialchars($f['filePath']) ?>" target="_blank" title="Ver" style="color:#1565c0;">
                  <i class="fa-solid fa-eye"></i>
                </a>
                <a href="<?= htmlspecialchars($f['filePath']) ?>" download title="Descargar" style="color:#388e3c;">
                  <i class="fa-solid fa-download"></i>
                </a>
                <a href="ver_documentos.php?doc=<?= urlencode($doc) ?>&delete=<?= urlencode($f['filePath']) ?>" 
                   onclick="return confirm('¿Eliminar este archivo?');" 
                   title="Eliminar" 
                   style="color:#e53935;">
                  <i class="fa-solid fa-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="no-docs">
        <i class="fa-solid fa-inbox" style="font-size:48px; color:#ccc;"></i>
        <p>No hay documentos cargados</p>
      </div>
    <?php endif; ?>

    <a href="registro_propietarios.php" class="volver">
      <i class="fa-solid fa-arrow-left"></i> Volver
    </a>
  </div>
</body>
</html>