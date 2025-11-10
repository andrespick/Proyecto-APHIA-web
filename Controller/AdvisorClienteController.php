<?php
require_once __DIR__ . '/../Model/clientesModel.php';

class AdvisorClienteController {
    private $model;

    public function __construct() {
        $this->model = new ClienteModel();
    }

    public function listar() {
        return $this->model->obtenerTodos();
    }
}
