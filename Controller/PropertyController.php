<?php
require_once __DIR__ . '/../Model/PropertyModel.php';

class PropertyController {
    private $model;

    public function __construct() {
        $this->model = new PropertyModel();
    }

    public function propietarios() {
        return $this->model->obtenerPropietarios();
    }

    public function index() {
        return $this->model->obtenerTodos();
    }

    public function crear($data) {
        return $this->model->crear($data);
    }

    public function actualizar($data) {
        return $this->model->actualizar($data);
    }

    public function eliminar($propertyId) {
        return $this->model->eliminar($propertyId);
    }

    public function obtener($propertyId) {
        return $this->model->obtenerPorId($propertyId);
    }

    // Documentos
    public function documentos($propertyId) {
        return $this->model->obtenerDocumentos($propertyId);
    }

    public function guardarDocumento($propertyId, $rutaRelativa) {
        return $this->model->guardarDocumento($propertyId, $rutaRelativa);
    }

    public function eliminarDocumento($propertyId, $filePath) {
        return $this->model->eliminarDocumento($propertyId, $filePath);
    }
}
