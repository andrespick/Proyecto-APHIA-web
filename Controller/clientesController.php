<?php
require_once __DIR__ . '/../Model/clientesModel.php';
require_once __DIR__ . '/../Utils/InputValidator.php';

class ClienteController {
    private $model;

    public function __construct() {
        $this->model = new ClienteModel();
    }

    // Mostrar lista de clientes
    public function index() {
        return $this->model->obtenerTodos();
    }

    // Obtener un cliente por documento
    public function obtenerPorDocumento($doc) {
        $doc = InputValidator::sanitizeAlphaNum($doc, 25);
        if ($doc === '') {
            return null;
        }
        return $this->model->buscarPorDocumento($doc);
    }

    // Guardar nuevo cliente
    public function guardar($postData) {
        $validacion = $this->sanitizarYValidar($postData);
        if (!$validacion['ok']) {
            return $validacion;
        }

        $data = $validacion['data'];

        if ($this->model->buscarPorDocumento($data['numero_doc'])) {
            return ['ok' => false, 'msg' => 'El cliente ya está registrado.', 'data' => $data];
        }

        $ok = $this->model->crear($data);
        return $ok ? ['ok' => true] : ['ok' => false, 'msg' => 'No fue posible guardar el cliente.', 'data' => $data];
    }

    // Actualizar cliente
    public function actualizar($postData) {
        $validacion = $this->sanitizarYValidar($postData, true);
        if (!$validacion['ok']) {
            return $validacion;
        }

        $data = $validacion['data'];

        $ok = $this->model->actualizar($data);
        return $ok ? ['ok' => true] : ['ok' => false, 'msg' => 'No fue posible actualizar el cliente.', 'data' => $data];
    }

    // Eliminar cliente
    public function eliminar($numero_doc) {
        $numero_doc = InputValidator::sanitizeAlphaNum($numero_doc, 25);
        if ($numero_doc === '') {
            return ['ok' => false, 'msg' => 'Documento inválido.'];
        }

        $ok = $this->model->eliminar($numero_doc);
        return $ok ? ['ok' => true] : ['ok' => false, 'msg' => 'No fue posible eliminar el cliente.'];
    }

    private function sanitizarYValidar(array $postData, bool $isUpdate = false): array
    {
        $allowedDocTypes = ['CC', 'CE', 'TI', 'PAS', 'NIT'];
        $allowedGender = ['M', 'F'];

        $data = [
            'nombre' => InputValidator::sanitizeText($postData['nombre'] ?? '', 80),
            'apellido' => InputValidator::sanitizeText($postData['apellido'] ?? '', 80),
            'tipo_doc' => strtoupper(InputValidator::sanitizeAlphaNum($postData['tipo_doc'] ?? '', 5)),
            'numero_doc' => InputValidator::sanitizeAlphaNum($postData['numero_doc'] ?? '', 25),
            'telefono' => InputValidator::sanitizeDigits($postData['telefono'] ?? '', 15),
            'email' => InputValidator::sanitizeEmail($postData['email'] ?? ''),
            'direccion' => InputValidator::sanitizeText($postData['direccion'] ?? '', 120),
            'genero' => strtoupper(InputValidator::sanitizeAlphaNum($postData['genero'] ?? '', 1)),
        ];

        $errores = [];

        if ($data['nombre'] === '' || $data['apellido'] === '') {
            $errores[] = 'Nombre y apellido son obligatorios.';
        }

        if ($data['tipo_doc'] === '' || !InputValidator::isInArray($data['tipo_doc'], $allowedDocTypes)) {
            $errores[] = 'Seleccione un tipo de documento válido.';
        }

        if ($data['numero_doc'] === '') {
            $errores[] = 'El número de documento es obligatorio.';
        }

        if ($data['telefono'] === '' || strlen($data['telefono']) < 7) {
            $errores[] = 'El teléfono debe contener al menos 7 dígitos.';
        }

        if ($data['email'] === '' || !InputValidator::isValidEmail($data['email'])) {
            $errores[] = 'Ingrese un correo electrónico válido.';
        }

        if ($data['direccion'] === '') {
            $errores[] = 'La dirección es obligatoria.';
        }

        if ($data['genero'] !== '' && !InputValidator::isInArray($data['genero'], $allowedGender)) {
            $errores[] = 'El género seleccionado no es válido.';
        }

        if (!empty($errores)) {
            return ['ok' => false, 'msg' => implode(' ', $errores), 'data' => $data];
        }

        return ['ok' => true, 'data' => $data];
    }
}

// Nota: no manejamos acciones automáticas aquí; la vista llamará métodos del controlador.
?>
