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
        $data['userType'] = $_POST['userType'];
        $data['username'] = $_POST['username'];
        $data['confirmPassword'] = $_POST['confirmPassword'];

        echo $this->model->crearUsuario($data);
    }

    public function validarNombre(){
        $data['name'] = $_POST['name'];

        echo $this->model->validarNombre($data);
    }

    public function editarUsuario()
    {
        $data['id'] = $_POST['id'];
        $data['userType'] = $_POST['userType'];
        $data['username'] = $_POST['username'];
        $data['confirmPassword'] = $_POST['confirmPassword'];

        echo $this->model->editarUsuario($data);
    }

    public function cambiarEstado()
    {
        $data['id'] = $_POST['id'];
        $data['estado'] = $_POST['estado'];
        echo $this->model->cambiarEstado($data);
    }
    
}
