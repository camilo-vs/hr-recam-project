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
    
    public function consultarEmpleadosSolicitud()
    {
        echo $this->model->consultarEmpleadosSolicitud();
    }
    
    public function consultarDatosExtra()
    {
        $index = $_POST['index'];
        echo $this->model->consultarDatosExtra($index);
    }


    public function crearEmpleado()
    {
        
        $data['new_hire_date'] = $_POST['new_hire_date'];
        $data['employee_number_id'] = $_POST['employee_number_id'];
        $data['puesto'] = $_POST['puesto'];
        $data['username'] = $_POST['username'];
        $data['genero'] = $_POST['genero'];

        $data['new_nss'] = $_POST['new_nss'];
        $data['new_curp'] = $_POST['new_curp'];
        $data['new_rfc'] = $_POST['new_rfc'];
        $data['new_birth_date'] = $_POST['new_birth_date'];
        $data['new_phone'] = $_POST['new_phone'];
        $data['new_address'] = $_POST['new_address'];
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
        $data['editTYPE'] = $_POST['labelType'];
        $data['employee_number_change'] = $_POST['labelEmployeeNumberId'];
        // Usa el nombre correcto de los inputs
        $data['username'] = $_POST['labelName'];
        $data['genero'] = $_POST['labelGenre'];
        $data['role'] = $_POST['labelRole'];
        
        $data['nss'] = $_POST['nss'];
        $data['curp'] = $_POST['curp'];
        $data['rfc'] = $_POST['rfc'];
        $data['birth_date'] = $_POST['birth_date'];
        $data['phone'] = $_POST['phone'];
        $data['address'] = $_POST['address'];
    
        if (!empty($_FILES['imagen']['tmp_name'])) {
            $data['imagen'] = $_FILES['imagen'];
        }
        echo $this->model->editarEmpleado($data);
    }
    
    public function cambiarEstado()
    {
        $data['employee_number_id'] = $_POST['employee_number_id'];
        $data['estado'] = $_POST['estado'];
        
        echo $this->model->cambiarEstado($data);
    }
    
    public function generarReporte()
    {
        $data['empresa'] = $_REQUEST['empresa']?? '';
        $data['genero'] = $_REQUEST['genero']?? '';
        $data['puesto'] = $_REQUEST['puesto']?? '';
        $data['fechaInicio'] = $_REQUEST['fechaInicio']?? '';
        $data['fechaFin'] = $_REQUEST['fechaFin']?? '';
        $data['estado'] = $_REQUEST['estado']?? '';
        
        echo $this->model->generarReporte($data);
    }
}
