<?php
require_once __DIR__ . '/../Model/PropertyModel.php';

class AdvisorPropertyController {
    private $model;

    public function __construct() {
        $this->model = new PropertyModel();
    }

    public function listar() {
        return $this->model->obtenerTodos();
    }
}
