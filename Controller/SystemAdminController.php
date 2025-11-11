<?php
require_once __DIR__ . '/../Model/SystemAdminModel.php';
require_once __DIR__ . '/../Utils/InputValidator.php';

class SystemAdminController {
    private $model;

    public function __construct() {
        $this->model = new SystemAdminModel();
    }

    public function obtenerCategoriasDisponibles() {
        return $this->model->obtenerCategoriasDisponibles();
    }

    public function index() {
        return $this->model->obtenerTodos();
    }

    public function obtenerPorId($accountId) {
        $accountId = (int)$accountId;
        return $accountId > 0 ? $this->model->obtenerPorId($accountId) : null;
    }

    public function crear($data) {
        $sanitized = $this->sanitizeAccountData($data, true);
        if (!$sanitized['ok']) {
            return $sanitized;
        }

        $payload = $sanitized['data'];

        if ($this->model->existeUsername($payload['userName'])) {
            return ['ok' => false, 'msg' => 'El nombre de usuario ya está registrado.', 'data' => $payload];
        }

        if ($this->model->existeDocumento($payload['documentIdentifier'])) {
            return ['ok' => false, 'msg' => 'El documento ya está asociado a otro usuario.', 'data' => $payload];
        }

        $ok = $this->model->crear($payload);
        return $ok ? ['ok' => true] : ['ok' => false, 'msg' => 'No fue posible crear el usuario.'];
    }

    public function actualizar($accountId, $data) {
        $accountId = (int)$accountId;
        if ($accountId <= 0) {
            return ['ok' => false, 'msg' => 'Identificador de cuenta inválido.'];
        }

        $sanitized = $this->sanitizeAccountData($data, false);
        if (!$sanitized['ok']) {
            return $sanitized;
        }

        $payload = $sanitized['data'];

        if ($this->model->existeUsername($payload['userName'], $accountId)) {
            return ['ok' => false, 'msg' => 'El nombre de usuario ya está registrado.', 'data' => $payload];
        }

        if ($this->model->existeDocumento($payload['documentIdentifier'], $accountId)) {
            return ['ok' => false, 'msg' => 'El documento ya está asociado a otro usuario.', 'data' => $payload];
        }

        $ok = $this->model->actualizar($accountId, $payload);
        return $ok ? ['ok' => true] : ['ok' => false, 'msg' => 'No fue posible actualizar el usuario.'];
    }

    public function cambiarEstado($accountId, $nuevoEstado) {
        $accountId = (int)$accountId;
        $nuevoEstado = strtoupper(InputValidator::sanitizeAlphaNum($nuevoEstado, 8));
        $estadoPermitido = in_array($nuevoEstado, ['ACTIVE', 'INACTIVE'], true);
        if ($accountId <= 0 || !$estadoPermitido) {
            return ['ok' => false, 'msg' => 'Datos inválidos para actualizar el estado.'];
        }

        $ok = $this->model->cambiarEstado($accountId, $nuevoEstado);
        return $ok ? ['ok' => true] : ['ok' => false, 'msg' => 'No fue posible actualizar el estado.'];
    }

    private function sanitizeAccountData(array $data, bool $requirePassword): array
    {
        $allowedDocTypes = ['CC', 'CE', 'NIT', 'PAS', 'ID'];

        $payload = [
            'userName' => InputValidator::sanitizeText($data['userName'] ?? '', 80),
            'documentCategory' => strtoupper(InputValidator::sanitizeAlphaNum($data['documentCategory'] ?? '', 10)),
            'documentIdentifier' => InputValidator::sanitizeAlphaNum($data['documentIdentifier'] ?? '', 25),
            'emailAddress' => InputValidator::sanitizeEmail($data['emailAddress'] ?? ''),
            'hashedPassword' => InputValidator::sanitizePassword($data['hashedPassword'] ?? ''),
            'state' => 'ACTIVE',
            'userCategory' => InputValidator::sanitizeText($data['userCategory'] ?? '', 60),
        ];

        if (isset($data['state'])) {
            $payload['state'] = strtoupper(InputValidator::sanitizeAlphaNum($data['state'], 8));
        }

        $errors = [];

        if ($payload['userName'] === '') {
            $errors[] = 'El nombre de usuario es obligatorio.';
        }

        if ($payload['documentCategory'] === '' || !InputValidator::isInArray($payload['documentCategory'], $allowedDocTypes)) {
            $errors[] = 'Seleccione un tipo de documento válido.';
        }

        if ($payload['documentIdentifier'] === '') {
            $errors[] = 'Ingrese un número de documento válido.';
        }

        if ($payload['emailAddress'] === '' || !InputValidator::isValidEmail($payload['emailAddress'])) {
            $errors[] = 'Ingrese un correo electrónico válido.';
        }

        if ($payload['userCategory'] === '' || !$this->model->esCategoriaValida($payload['userCategory'])) {
            $errors[] = 'La categoria seleccionada no es valida.';
        }

        $plainPassword = $payload['hashedPassword'];

        if ($requirePassword && $plainPassword === '') {
            $errors[] = 'La contraseña es obligatoria.';
        }

        if (!$requirePassword && $plainPassword === '') {
            unset($payload['hashedPassword']);
        } elseif ($plainPassword !== '') {
            $payload['hashedPassword'] = password_hash($plainPassword, PASSWORD_DEFAULT);
        }

        if (!isset($payload['state']) || !in_array($payload['state'], ['ACTIVE', 'INACTIVE'], true)) {
            $payload['state'] = 'ACTIVE';
        }

        if (!empty($errors)) {
            return ['ok' => false, 'msg' => implode(' ', $errors), 'data' => $payload];
        }

        return ['ok' => true, 'data' => $payload];
    }
}
