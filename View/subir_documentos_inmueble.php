<?php
require_once __DIR__ . '/../Controller/PropertyController.php';

$controller = new PropertyController();
$propertyId = isset($_POST['prop']) ? (int)$_POST['prop'] : (isset($_GET['prop']) ? (int)$_GET['prop'] : 0);
$property = $propertyId ? $controller->obtener($propertyId) : null;

if (!$property) {
    http_response_code(404);
    die('Inmueble no encontrado.');
}

$msg = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
    $archivo = $_FILES['archivo'];
    $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        $msg = 'Error al subir el archivo. Intente nuevamente.';
        $msgType = 'error';
    } elseif ($archivo['size'] > $maxSize) {
        $msg = 'El archivo supera el tamaño permitido (5MB).';
        $msgType = 'error';
    } else {
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions, true)) {
            $msg = 'Formato no permitido.';
            $msgType = 'error';
        } else {
            $uploadDir = __DIR__ . '/../saveDoc';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            try {
                $uniqueName = sprintf(
                    'prop_%d_%s_%s.%s',
                    $propertyId,
                    date('YmdHis'),
                    bin2hex(random_bytes(3)),
                    $extension
                );
            } catch (Exception $e) {
                $uniqueName = sprintf(
                    'prop_%d_%s.%s',
                    $propertyId,
                    uniqid(),
                    $extension
                );
            }

            $destination = $uploadDir . '/' . $uniqueName;
            $relativePath = '../saveDoc/' . $uniqueName;

            if (move_uploaded_file($archivo['tmp_name'], $destination)) {
                $controller->guardarDocumento($propertyId, $relativePath);
                $msg = 'Archivo cargado correctamente.';
                $msgType = 'success';
            } else {
                $msg = 'No se pudo mover el archivo al directorio de almacenamiento.';
                $msgType = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Subir documentos del inmueble</title>
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
  max-width: 640px;
  margin: auto;
}
h2 {
  color: #333;
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
}
p {
  color: #555;
  margin: 4px 0;
}
form {
  margin-top: 25px;
}
.upload-area {
  border: 2px dashed #ccc;
  border-radius: 12px;
  padding: 30px;
  text-align: center;
  background: #fafafa;
  margin-bottom: 20px;
  transition: all .3s ease;
}
.upload-area:hover {
  border-color: #388e3c;
  background: #f0f8f0;
}
.upload-area i {
  font-size: 48px;
  color: #388e3c;
  margin-bottom: 10px;
}
input[type="file"] {
  width: 100%;
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-family: 'Poppins', sans-serif;
  cursor: pointer;
}
input[type="file"]::file-selector-button {
  background: #388e3c;
  border: none;
  color: #fff;
  padding: 8px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}
.button-group {
  display: flex;
  gap: 12px;
  justify-content: center;
  margin-top: 25px;
  flex-wrap: wrap;
}
button,
.volver {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  border: none;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  text-decoration: none;
  transition: transform .2s, box-shadow .2s;
}
button {
  background: #388e3c;
  color: #fff;
}
button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(56,142,60,0.3);
}
.volver {
  background: #1976d2;
  color: #fff;
}
.volver:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(25,118,210,0.3);
}
.msg {
  margin-top: 20px;
  padding: 12px 16px;
  border-radius: 8px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 10px;
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
</style>
</head>
<body>
  <div class="container">
    <h2><i class="fa-solid fa-cloud-arrow-up"></i> Documentos del inmueble</h2>
    <p><strong>ID:</strong> <?= (int)$property['propertyId'] ?></p>
    <p><strong>Dirección:</strong> <?= htmlspecialchars($property['address']) ?></p>
    <p><strong>Ciudad:</strong> <?= htmlspecialchars($property['city']) ?></p>
    <p><strong>Propietario:</strong> <?= htmlspecialchars($property['ownerName']) ?></p>

    <?php if ($msg): ?>
      <div class="msg <?= $msgType ?>">
        <i class="fa-solid fa-<?= $msgType === 'success' ? 'check-circle' : 'triangle-exclamation' ?>"></i>
        <?= htmlspecialchars($msg) ?>
      </div>
    <?php endif; ?>

    <form action="subir_documentos_inmueble.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="prop" value="<?= (int)$propertyId ?>">
      <div class="upload-area">
        <i class="fa-solid fa-file-circle-plus"></i>
        <p>Seleccione el archivo (PDF, DOC, XLS, JPG, PNG - máx. 5MB)</p>
      </div>
      <input type="file" name="archivo" required accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">

      <div class="button-group">
        <button type="submit"><i class="fa-solid fa-upload"></i> Subir archivo</button>
        <a class="volver" href="registro_inmuebles.php?action=edit&id=<?= (int)$propertyId ?>">
          <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
      </div>
    </form>
  </div>
</body>
</html>
