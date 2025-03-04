<?php

require_once('modelo/usuarios_model.php');

class usuarios_controller
{
    private $model;

    public function __construct()
    {
        $this->model = new usuarios_model();
    }

    public function consultarUsuarios()
    {
        echo $this->model->consultarUsuarios();
    }

    public function crearUsuario()
    {
        $nombre = $_POST['firstname'];
        $apellido = $_POST['lastname'];
        $telefono = $_POST['phone'];
        $email = $_POST['email'];

        echo $this->model->crearUsuario($nombre, $apellido, $telefono, $email);
    }

    public function actualizarUsuario()
    {
        $id = $_GET['id'];
        $nombre = $_POST['firstname'];
        $apellido = $_POST['lastname'];
        $telefono = $_POST['phone'];
        $email = $_POST['email'];

        echo $this->model->actualizarUsuario($id, $nombre, $apellido, $telefono, $email);
    }

    public function eliminarUsuario()
    {
        $id = $_POST['id'];
        echo $this->model->eliminarUsuario($id);
    }
}

// Manejo de las peticiones
$controller = new usuarios_controller();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    if ($_GET['action'] === 'consultar') {
        $controller->consultarUsuarios();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    if ($_GET['action'] === 'crear') {
        $controller->crearUsuario();
    } elseif ($_GET['action'] === 'actualizar') {
        $controller->actualizarUsuario();
    } elseif ($_GET['action'] === 'eliminar') {
        $controller->eliminarUsuario();
    }
}
