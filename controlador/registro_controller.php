<?php

require_once('modelo/registro_model.php');

class registro_controller
{
    function __construct()
    {
        $this->model = new registro_model();
    }

    function registro()
    {
        $data['nombre'] = $_REQUEST['nombre'];
        $data['contraseña'] = $_REQUEST['contraseña'];
        $resultado = $this->model->registro($data);
    }
}
