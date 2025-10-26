<?php
require_once __DIR__ . '/../Model/propietariosModel.php';

class PropietarioController {
    private $model;

    public function __construct() {
        $this->model = new PropietarioModel();
    }

    public function index() {
        return $this->model->obtenerTodos();
    }

    public function guardar($data) {
        return $this->model->guardar($data);
    }

    public function actualizar($data) {
        return $this->model->actualizar($data);
    }

    public function eliminar($documentIdentifier) {
        return $this->model->eliminar($documentIdentifier);
    }

    public function obtenerPorDocumento($documentIdentifier) {
        return $this->model->obtenerPorDocumento($documentIdentifier);
    }

    public function subirDocumento($documentIdentifier, $archivo) {
        $prop = $this->model->obtenerPorDocumento($documentIdentifier);
        if (!$prop) return false;

        $personId = $prop['personId'];
        $nombreArchivo = basename($archivo['name']);
        $rutaDestino = __DIR__ . '/../saveDoc/' . $nombreArchivo;
        $rutaRelativa = '../saveDoc/' . $nombreArchivo;

        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            return $this->model->guardarDocumento($personId, $rutaRelativa);
        }
        return false;
    }

    public function obtenerDocumentos($documentIdentifier) {
        $prop = $this->model->obtenerPorDocumento($documentIdentifier);
        if (!$prop) return [];
        return $this->model->obtenerDocumentos($prop['personId']);
    }
}
?>
