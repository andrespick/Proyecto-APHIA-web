<?php
class Conexion {
    private $server = "localhost";
    private $user = "root";
    private $pass = "";
    private $db = "aphia_p";
    private $conexion;

    public function __construct() {
        $this->conexion = new mysqli($this->server, $this->user, $this->pass, $this->db);

        if ($this->conexion->connect_errno) {
            die("Conexión fallida: " . $this->conexion->connect_error);
        }
    }

    public function conectar() {
        return $this->conexion;
    }
}
?>
