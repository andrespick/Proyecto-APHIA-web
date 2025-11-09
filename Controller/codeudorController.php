<?php
require_once __DIR__ . '/../Model/codeudorModel.php';
require_once __DIR__ . '/../Utils/InputValidator.php';

class CodeudorController {
    private $model;

    public function __construct() {
        $this->model = new CodeudorModel();
    }

    public function index() {
        return $this->model->obtenerTodos();
    }

    public function guardar($data) {
        $validacion = $this->sanitizarYValidar($data);
        if (!$validacion['ok']) {
            return $validacion;
        }

        $payload = $validacion['data'];

        if ($this->model->obtenerPorDocumento($payload['numero_doc'])) {
            return ['ok' => false, 'msg' => 'El codeudor ya está registrado.', 'data' => $payload];
        }

        $ok = $this->model->crear($payload);
        return $ok ? ['ok' => true] : ['ok' => false, 'msg' => 'No fue posible guardar el codeudor.', 'data' => $payload];
    }

    public function actualizar($data) {
        $validacion = $this->sanitizarYValidar($data, true);
        if (!$validacion['ok']) {
            return $validacion;
        }

        $payload = $validacion['data'];
        $ok = $this->model->actualizar($payload);
        return $ok ? ['ok' => true] : ['ok' => false, 'msg' => 'No fue posible actualizar el codeudor.', 'data' => $payload];
    }

    public function eliminar($documento) {
        $documento = InputValidator::sanitizeAlphaNum($documento, 25);
        if ($documento === '') {
            return ['ok' => false, 'msg' => 'Documento inválido.'];
        }

        $ok = $this->model->eliminar($documento);
        return $ok ? ['ok' => true] : ['ok' => false, 'msg' => 'No fue posible eliminar el codeudor.'];
    }

    public function obtenerPorDocumento($documento) {
        $documento = InputValidator::sanitizeAlphaNum($documento, 25);
        if ($documento === '') {
            return null;
        }
        return $this->model->obtenerPorDocumento($documento);
    }

    private function sanitizarYValidar(array $data, bool $isUpdate = false): array
    {
        $allowedDocTypes = ['CC', 'CE', 'TI', 'PAS', 'NIT'];

        $payload = [
            'nombre' => InputValidator::sanitizeText($data['nombre'] ?? '', 80),
            'apellido' => InputValidator::sanitizeText($data['apellido'] ?? '', 80),
            'tipo_doc' => strtoupper(InputValidator::sanitizeAlphaNum($data['tipo_doc'] ?? '', 5)),
            'numero_doc' => InputValidator::sanitizeAlphaNum($data['numero_doc'] ?? '', 25),
            'telefono' => InputValidator::sanitizeDigits($data['telefono'] ?? '', 15),
            'email' => InputValidator::sanitizeEmail($data['email'] ?? ''),
            'direccion' => InputValidator::sanitizeText($data['direccion'] ?? '', 120),
        ];

        $errores = [];

        if ($payload['nombre'] === '' || $payload['apellido'] === '') {
            $errores[] = 'Nombre y apellido son obligatorios.';
        }

        if ($payload['tipo_doc'] === '' || !InputValidator::isInArray($payload['tipo_doc'], $allowedDocTypes)) {
            $errores[] = 'Seleccione un tipo de documento válido.';
        }

        if ($payload['numero_doc'] === '') {
            $errores[] = 'El número de documento es obligatorio.';
        }

        if ($payload['telefono'] === '' || strlen($payload['telefono']) < 7) {
            $errores[] = 'El teléfono debe contener al menos 7 dígitos.';
        }

        if ($payload['email'] === '' || !InputValidator::isValidEmail($payload['email'])) {
            $errores[] = 'Ingrese un correo electrónico válido.';
        }

        if ($payload['direccion'] === '') {
            $errores[] = 'La dirección es obligatoria.';
        }

        if (!empty($errores)) {
            return ['ok' => false, 'msg' => implode(' ', $errores), 'data' => $payload];
        }

        return ['ok' => true, 'data' => $payload];
    }
}
