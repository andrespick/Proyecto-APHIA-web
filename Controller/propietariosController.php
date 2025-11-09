<?php
require_once __DIR__ . '/../Model/propietariosModel.php';
require_once __DIR__ . '/../Utils/InputValidator.php';

class PropietarioController {
    private $model;

    public function __construct() {
        $this->model = new PropietarioModel();
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
            return ['ok' => false, 'msg' => 'El propietario ya está registrado.', 'data' => $payload];
        }

        try {
            $this->model->guardar($payload);
            return ['ok' => true];
        } catch (Exception $e) {
            return ['ok' => false, 'msg' => 'No fue posible guardar el propietario.', 'data' => $payload];
        }
    }

    public function actualizar($data) {
        $validacion = $this->sanitizarYValidar($data, true);
        if (!$validacion['ok']) {
            return $validacion;
        }

        $payload = $validacion['data'];

        try {
            $this->model->actualizar($payload);
            return ['ok' => true];
        } catch (Exception $e) {
            return ['ok' => false, 'msg' => 'No fue posible actualizar el propietario.', 'data' => $payload];
        }
    }

    public function eliminar($documentIdentifier) {
        $documentIdentifier = InputValidator::sanitizeAlphaNum($documentIdentifier, 25);
        if ($documentIdentifier === '') {
            return ['ok' => false, 'msg' => 'Documento inválido.'];
        }

        $ok = $this->model->eliminar($documentIdentifier);
        return $ok ? ['ok' => true] : ['ok' => false, 'msg' => 'No fue posible eliminar el propietario.'];
    }

    public function obtenerPorDocumento($documentIdentifier) {
        $documentIdentifier = InputValidator::sanitizeAlphaNum($documentIdentifier, 25);
        if ($documentIdentifier === '') {
            return null;
        }
        return $this->model->obtenerPorDocumento($documentIdentifier);
    }

    public function subirDocumento($documentIdentifier, $archivo) {
        $documentIdentifier = InputValidator::sanitizeAlphaNum($documentIdentifier, 25);
        if ($documentIdentifier === '') {
            return false;
        }

        $prop = $this->model->obtenerPorDocumento($documentIdentifier);
        if (!$prop) return false;

        $personId = $prop['personId'];
        $nombreArchivo = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($archivo['name'] ?? ''));
        if ($nombreArchivo === '') {
            return false;
        }
        $rutaDestino = __DIR__ . '/../saveDoc/' . $nombreArchivo;
        $rutaRelativa = '../saveDoc/' . $nombreArchivo;

        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            return $this->model->guardarDocumento($personId, $rutaRelativa);
        }
        return false;
    }

    public function obtenerDocumentos($documentIdentifier) {
        $documentIdentifier = InputValidator::sanitizeAlphaNum($documentIdentifier, 25);
        if ($documentIdentifier === '') {
            return [];
        }
        $prop = $this->model->obtenerPorDocumento($documentIdentifier);
        if (!$prop) return [];
        return $this->model->obtenerDocumentos($prop['personId']);
    }

    private function sanitizarYValidar(array $data, bool $isUpdate = false): array
    {
        $allowedDocTypes = ['CC', 'CE', 'NIT', 'PAS'];
        $allowedAccountTypes = [
            'AHORROS' => 'Ahorros',
            'CORRIENTE' => 'Corriente',
            'DIGITAL' => 'Digital',
        ];

        $payload = [
            'nombre' => InputValidator::sanitizeText($data['nombre'] ?? '', 80),
            'apellido' => InputValidator::sanitizeText($data['apellido'] ?? '', 80),
            'tipo_doc' => strtoupper(InputValidator::sanitizeAlphaNum($data['tipo_doc'] ?? '', 5)),
            'numero_doc' => InputValidator::sanitizeAlphaNum($data['numero_doc'] ?? '', 25),
            'telefono' => InputValidator::sanitizeDigits($data['telefono'] ?? '', 15),
            'email' => InputValidator::sanitizeEmail($data['email'] ?? ''),
            'direccion' => InputValidator::sanitizeText($data['direccion'] ?? '', 150),
            'numero_cuenta' => InputValidator::sanitizeAlphaNum($data['numero_cuenta'] ?? '', 30),
            'tipo_cuenta' => strtoupper(InputValidator::sanitizeAlphaNum($data['tipo_cuenta'] ?? '', 15)),
            'entidad_bancaria' => InputValidator::sanitizeText($data['entidad_bancaria'] ?? '', 80),
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

        if ($payload['numero_cuenta'] !== '') {
            if (strlen($payload['numero_cuenta']) < 6) {
                $errores[] = 'El número de cuenta debe contener al menos 6 caracteres.';
            }

            if ($payload['tipo_cuenta'] === '' || !isset($allowedAccountTypes[$payload['tipo_cuenta']])) {
                $errores[] = 'Seleccione un tipo de cuenta válido.';
            }

            if ($payload['entidad_bancaria'] === '') {
                $errores[] = 'La entidad bancaria es obligatoria cuando se registra una cuenta.';
            }
        } else {
            $payload['tipo_cuenta'] = '';
            $payload['entidad_bancaria'] = '';
        }

        if (!empty($errores)) {
            return ['ok' => false, 'msg' => implode(' ', $errores), 'data' => $payload];
        }

        if ($payload['numero_cuenta'] !== '') {
            $payload['tipo_cuenta'] = $allowedAccountTypes[$payload['tipo_cuenta']];
        }

        return ['ok' => true, 'data' => $payload];
    }
}
?>
