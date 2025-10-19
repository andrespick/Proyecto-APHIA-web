<?php
require_once __DIR__ . '/../Model/clientesModel.php';

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
        return $this->model->buscarPorDocumento($doc);
    }

    // Guardar nuevo cliente
    public function guardar($postData) {
        // Espera array con keys: nombre, apellido, tipo_doc, numero_doc, telefono, email, direccion, genero (opcional)
        // Evitar duplicados
        $existe = $this->model->buscarPorDocumento($postData['numero_doc']);
        if ($existe) {
            return ['ok' => false, 'msg' => 'El cliente ya está registrado'];
        }

        $ok = $this->model->crear($postData);
        return $ok ? ['ok' => true] : ['ok' => false, 'msg' => 'Error al insertar'];
    }

    // Actualizar cliente
    public function actualizar($postData) {
        // postData debe contener numero_doc (clave) y demás campos
        $ok = $this->model->actualizar($postData);
        return $ok ? ['ok' => true] : ['ok' => false, 'msg' => 'Error al actualizar'];
    }

    // Eliminar cliente
    public function eliminar($numero_doc) {
        return $this->model->eliminar($numero_doc);
    }
}

// Nota: no manejamos acciones automáticas aquí; la vista llamará métodos del controlador.
?>
