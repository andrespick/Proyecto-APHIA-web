<?php
require_once __DIR__ . '/../Model/codeudorModel.php';

class CodeudorController {
    private $model;

    public function __construct() {
        $this->model = new CodeudorModel();
    }

    public function index() {
        return $this->model->obtenerTodos();
    }

    public function guardar($data) {
        return $this->model->crear($data);
    }

    public function actualizar($data) {
        return $this->model->actualizar($data);
    }

    public function eliminar($documento) {
        return $this->model->eliminar($documento);
    }

    public function obtenerPorDocumento($documento) {
        return $this->model->obtenerPorDocumento($documento);
    }
}
