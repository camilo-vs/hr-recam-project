<?php

require_once('modelo/login_model.php');

class login_controller
{

    function __construct()
    {
        $this->model = new login_model();
    }

    function logear()
    {
        $data['usuario'] = $_REQUEST['usuario'];
        $data['password'] = $_REQUEST['password'];
        $resultado = $this->model->logear($data);
    }
}
