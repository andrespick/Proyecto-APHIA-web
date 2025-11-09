<?php
require_once __DIR__ . '/../Model/propietariosModel.php';

class AdvisorPropietarioController {
    private $model;

    public function __construct() {
        $this->model = new PropietarioModel();
    }

    public function listar() {
        return $this->model->obtenerTodos();
    }
}
