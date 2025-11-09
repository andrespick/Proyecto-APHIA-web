<?php
require_once __DIR__ . '/../Model/SystemAdminModel.php';

class SystemAdminController {
    private $model;

    public function __construct() {
        $this->model = new SystemAdminModel();
    }

    public function index() {
        return $this->model->obtenerTodos();
    }

    public function obtenerPorId($accountId) {
        return $this->model->obtenerPorId($accountId);
    }

    public function crear($data) {
        if ($this->model->existeUsername($data['userName'])) {
            return ['ok' => false, 'msg' => 'El nombre de usuario ya está registrado.'];
        }

        if ($this->model->existeDocumento($data['documentIdentifier'])) {
            return ['ok' => false, 'msg' => 'El documento ya está asociado a otro usuario.'];
        }

        $data['hashedPassword'] = trim($data['hashedPassword']);
        $data['state'] = $data['state'] ?? 'ACTIVE';

        $ok = $this->model->crear($data);
        return $ok ? ['ok' => true] : ['ok' => false, 'msg' => 'No fue posible crear el usuario.'];
    }

    public function actualizar($accountId, $data) {
        if ($this->model->existeUsername($data['userName'], $accountId)) {
            return ['ok' => false, 'msg' => 'El nombre de usuario ya está registrado.'];
        }

        if ($this->model->existeDocumento($data['documentIdentifier'], $accountId)) {
            return ['ok' => false, 'msg' => 'El documento ya está asociado a otro usuario.'];
        }

        $data['hashedPassword'] = trim($data['hashedPassword'] ?? '');
        $ok = $this->model->actualizar($accountId, $data);
        return $ok ? ['ok' => true] : ['ok' => false, 'msg' => 'No fue posible actualizar el usuario.'];
    }

    public function cambiarEstado($accountId, $nuevoEstado) {
        $ok = $this->model->cambiarEstado($accountId, $nuevoEstado);
        return $ok ? ['ok' => true] : ['ok' => false, 'msg' => 'No fue posible actualizar el estado.'];
    }
}
