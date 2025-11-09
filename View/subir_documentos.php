<?php
require_once __DIR__ . '/../Model/conexions.php';
$conn = (new Conexion())->conectar();

$doc = $_POST['doc'] ?? ($_GET['doc'] ?? null);
if (!$doc) die("Documento no especificado.");

$allowedReturns = [
    'registro_propietarios.php',
    'registro_codeudor.php',
    'resgistro_clientes.php'
];
$returnParam = $_POST['return'] ?? ($_GET['return'] ?? '');
$returnPage = basename($returnParam);
if (!in_array($returnPage, $allowedReturns, true)) {
    $refererPath = parse_url($_SERVER['HTTP_REFERER'] ?? '', PHP_URL_PATH) ?: '';
    $refererBase = basename($refererPath);
    $returnPage = in_array($refererBase, $allowedReturns, true) ? $refererBase : 'registro_propietarios.php';
}

// Obtener personId
$stmt = $conn->prepare("SELECT personId, fullName FROM person WHERE documentIdentifier = ?");
$stmt->bind_param("s", $doc);
$stmt->execute();
$res = $stmt->get_result();
$person = $res->fetch_assoc();
if (!$person) die("Propietario no encontrado.");

$msg = "";
$msgType = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
    $archivo = $_FILES['archivo'];
    $nombre = basename($archivo['name']);
    $ruta = __DIR__ . '/../saveDoc/' . $nombre;
    $rutaRelativa = '../saveDoc/' . $nombre;

    if (move_uploaded_file($archivo['tmp_name'], $ruta)) {
        $insert = $conn->prepare("INSERT INTO file_record (personId, filePath, uploadDate) VALUES (?, ?, NOW())");
        $insert->bind_param("is", $person['personId'], $rutaRelativa);
        $insert->execute();
        $msg = "Archivo subido correctamente";
        $msgType = "success";
    } else {
        $msg = "Error al subir el archivo";
        $msgType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Subir Documentos - <?= htmlspecialchars($person['fullName']) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="shortcut icon" href="img/logo.svg" />
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: #f5f6fa;
  padding: 40px;
  margin: 0;
}
.container {
  background: #fff;
  padding: 30px;
  border-radius: 15px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  max-width: 600px;
  margin: auto;
}
h2 {
  color: #333;
  margin-bottom: 10px;
  display: flex;
  align-items: center;
  gap: 10px;
}
h2 i {
  color: #388e3c;
}
p {
  color: #666;
  font-size: 15px;
  margin-bottom: 25px;
}
.upload-area {
  border: 2px dashed #ccc;
  border-radius: 10px;
  padding: 30px;
  text-align: center;
  background: #fafafa;
  margin: 20px 0;
  transition: all 0.3s;
}
.upload-area:hover {
  border-color: #388e3c;
  background: #f0f8f0;
}
.upload-area i {
  font-size: 48px;
  color: #388e3c;
  margin-bottom: 15px;
}
input[type="file"] {
  width: 100%;
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 8px;
  margin: 15px 0;
  cursor: pointer;
  font-family: 'Poppins', sans-serif;
}
input[type="file"]::file-selector-button {
  background: #388e3c;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  margin-right: 10px;
  transition: background 0.3s;
}
input[type="file"]::file-selector-button:hover {
  background: #2e7d32;
}
.button-group {
  display: flex;
  gap: 10px;
  justify-content: center;
  margin-top: 25px;
}
button, a.volver {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  border: none;
  border-radius: 10px;
  text-decoration: none;
  font-weight: 600;
  font-size: 15px;
  cursor: pointer;
  transition: all 0.3s;
  font-family: 'Poppins', sans-serif;
}
button {
  background: #388e3c;
  color: #fff;
}
button:hover {
  background: #2e7d32;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(56, 142, 60, 0.3);
}
a.volver {
  background: #1976d2;
  color: white;
}
a.volver:hover {
  background: #0d47a1;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(25, 118, 210, 0.3);
}
.msg {
  margin-top: 20px;
  padding: 12px 16px;
  border-radius: 8px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 10px;
  animation: slideIn 0.3s ease;
}
.msg.success {
  background: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}
.msg.error {
  background: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}
.msg i {
  font-size: 20px;
}
@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
</head>
<body>
  <div class="container">
    <h2><i class="fa-solid fa-cloud-arrow-up"></i> Subir Documento</h2>
    <p><b>Persona:</b> <?= htmlspecialchars($person['fullName']) ?></p>

    <?php if ($msg): ?>
      <div class="msg <?= $msgType ?>">
        <i class="fa-solid fa-<?= $msgType === 'success' ? 'check-circle' : 'times-circle' ?>"></i>
        <strong><?= htmlspecialchars($msg) ?></strong>
      </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" action="subir_documentos.php">
      <input type="hidden" name="doc" value="<?= htmlspecialchars($doc) ?>">
      <input type="hidden" name="return" value="<?= htmlspecialchars($returnPage) ?>">
      <div class="upload-area">
        <i class="fa-solid fa-file-arrow-up"></i>
        <p style="margin:0; color:#666;">Seleccione el archivo que desea subir</p>
      </div>
      
      <input type="file" name="archivo" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.txt">
      
      <div class="button-group">
        <button type="submit">
          <i class="fa-solid fa-upload"></i> 
          Subir Archivo
        </button>
        <a href="<?= htmlspecialchars($returnPage) ?>" class="volver">
          <i class="fa-solid fa-arrow-left"></i> 
          Volver
        </a>
      </div>
    </form>
  </div>
</body>
</html>
