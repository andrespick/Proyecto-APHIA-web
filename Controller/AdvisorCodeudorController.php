<?php
require_once __DIR__ . '/../Model/codeudorModel.php';

class AdvisorCodeudorController {
    private $model;

    public function __construct() {
        $this->model = new CodeudorModel();
    }

    public function listar() {
        return $this->model->obtenerTodos();
    }
}
