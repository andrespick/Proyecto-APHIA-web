<?php
require_once __DIR__ . '/../Model/PropertyModel.php';
require_once __DIR__ . '/../Utils/InputValidator.php';

class PropertyController {
    private $model;

    public function __construct() {
        $this->model = new PropertyModel();
    }

    public function propietarios() {
        return $this->model->obtenerPropietarios();
    }

    public function index() {
        return $this->model->obtenerTodos();
    }

    public function crear($data) {
        $validacion = $this->sanitizarYValidar($data);
        if (!$validacion['ok']) {
            return $validacion;
        }

        $payload = $validacion['data'];
        $ok = $this->model->crear($payload);
        return $ok ? ['ok' => true] : ['ok' => false, 'msg' => 'No fue posible guardar el inmueble.', 'data' => $payload];
    }

    public function actualizar($data) {
        $validacion = $this->sanitizarYValidar($data, true);
        if (!$validacion['ok']) {
            return $validacion;
        }

        $payload = $validacion['data'];
        $ok = $this->model->actualizar($payload);
        return $ok ? ['ok' => true] : ['ok' => false, 'msg' => 'No fue posible actualizar el inmueble.', 'data' => $payload];
    }

    public function eliminar($propertyId) {
        $propertyId = (int)$propertyId;
        if ($propertyId <= 0) {
            return ['ok' => false, 'msg' => 'Identificador de inmueble inválido.'];
        }

        $ok = $this->model->eliminar($propertyId);
        return $ok ? ['ok' => true] : ['ok' => false, 'msg' => 'No fue posible eliminar el inmueble.'];
    }

    public function obtener($propertyId) {
        $propertyId = (int)$propertyId;
        return $propertyId > 0 ? $this->model->obtenerPorId($propertyId) : null;
    }

    // Documentos
    public function documentos($propertyId) {
        $propertyId = (int)$propertyId;
        return $propertyId > 0 ? $this->model->obtenerDocumentos($propertyId) : [];
    }

    public function guardarDocumento($propertyId, $rutaRelativa) {
        $propertyId = (int)$propertyId;
        $rutaRelativa = InputValidator::ensureNullableText($rutaRelativa, 255);
        if ($propertyId <= 0 || $rutaRelativa === '') {
            return false;
        }
        return $this->model->guardarDocumento($propertyId, $rutaRelativa);
    }

    public function eliminarDocumento($propertyId, $filePath) {
        $propertyId = (int)$propertyId;
        $filePath = InputValidator::ensureNullableText($filePath, 255);
        if ($propertyId <= 0 || $filePath === '') {
            return false;
        }
        return $this->model->eliminarDocumento($propertyId, $filePath);
    }

    private function sanitizarYValidar(array $data, bool $isUpdate = false): array
    {
        $allowedOccupancy = ['DISPONIBLE', 'OCUPADO', 'MANTENIMIENTO', ''];
        $allowedPropertyTypes = ['APARTAMENTO', 'CASA', 'OFICINA', 'LOCAL', 'BODEGA', 'OTRO'];

        $payload = [
            'propertyId' => isset($data['propertyId']) ? (int)$data['propertyId'] : null,
            'address' => InputValidator::sanitizeText($data['address'] ?? '', 150),
            'city' => InputValidator::sanitizeText($data['city'] ?? '', 80),
            'registrationIdentifier' => InputValidator::sanitizeText($data['registrationIdentifier'] ?? '', 50),
            'rentalValue' => InputValidator::sanitizeFloatString($data['rentalValue'] ?? ''),
            'utilityContractIdentifiers' => InputValidator::ensureNullableText($data['utilityContractIdentifiers'] ?? '', 150),
            'occupancyState' => strtoupper(InputValidator::sanitizeText($data['occupancyState'] ?? '', 30)),
            'propertyType' => strtoupper(InputValidator::sanitizeText($data['propertyType'] ?? '', 30)),
            'ownerId' => isset($data['ownerId']) ? (int)$data['ownerId'] : 0,
        ];

        $errors = [];

        if ($isUpdate && ($payload['propertyId'] === null || $payload['propertyId'] <= 0)) {
            $errors[] = 'Identificador de inmueble inválido.';
        }

        if ($payload['address'] === '') {
            $errors[] = 'La dirección es obligatoria.';
        }

        if ($payload['city'] === '') {
            $errors[] = 'La ciudad es obligatoria.';
        }

        if ($payload['propertyType'] === '' || !in_array($payload['propertyType'], $allowedPropertyTypes, true)) {
            $errors[] = 'Seleccione un tipo de inmueble válido.';
        }

        if ($payload['occupancyState'] !== '' && !in_array($payload['occupancyState'], $allowedOccupancy, true)) {
            $errors[] = 'Seleccione un estado de ocupación válido.';
        }

        if ($payload['ownerId'] <= 0) {
            $errors[] = 'Seleccione un propietario válido.';
        }

        if ($payload['rentalValue'] !== '' && !is_numeric($payload['rentalValue'])) {
            $errors[] = 'El valor del canon debe ser numérico.';
        }

        if (!empty($errors)) {
            return ['ok' => false, 'msg' => implode(' ', $errors), 'data' => $payload];
        }

        if ($payload['rentalValue'] === '') {
            $payload['rentalValue'] = '';
        }

        if ($payload['occupancyState'] === '') {
            $payload['occupancyState'] = 'DISPONIBLE';
        }

        $payload['propertyType'] = ucfirst(strtolower($payload['propertyType']));
        $payload['occupancyState'] = ucfirst(strtolower($payload['occupancyState']));

        return ['ok' => true, 'data' => $payload];
    }
}
