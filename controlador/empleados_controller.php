<?php

require_once('modelo/empleados_model.php');

class empleados_controller
{
    private $model;

    public function __construct()
    {
        //$this->model = new empleados_model();
        $this->model = new empleados__model();
    }

    public function consultarDepartamentos()
    {
        echo $this->model->consultarDepartamentos();
    }

    public function consultarEmpleados()
    {
        echo $this->model->consultarEmpleados();
    }

    public function consultarDatosExtra()
    {
        $index = $_POST['index'];
        echo $this->model->consultarDatosExtra($index);
    }


    public function crearEmpleado()
    {
        $data['employee_number_id'] = $_POST['employee_number_id'];
        $data['puesto'] = $_POST['puesto'];
        $data['username'] = $_POST['username'];
        $data['genero'] = $_POST['genero'];

        echo $this->model->crearEmpleado($data);
    }

    public function validarNombre(){
        $data['name'] = $_POST['name'];

        echo $this->model->validarNombre($data);
    }

    public function editarEmpleado()
    {
        $data = array();
        // Usa el nombre correcto del input para el id
        $data['employee_number_id'] = $_POST['id'];
        // Usa el nombre correcto de los inputs
        $data['username'] = $_POST['labelName'];
        $data['genero'] = $_POST['labelGenre'];
        $data['role'] = $_POST['labelRole'];
    
        echo $this->model->editarEmpleado($data);
    }
    
    public function cambiarEstado()
    {
        $data['employee_number_id'] = $_POST['employee_number_id'];
        $data['estado'] = $_POST['estado'];
        
        echo $this->model->cambiarEstado($data);
    }
    
}
