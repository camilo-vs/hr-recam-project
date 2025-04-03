<?php

require_once('modelo/vacaciones_model.php');

class vacaciones_controller
{
    private $model;

    public function __construct()
    {
        $this->model = new vacaciones__model();
    }

    public function consultarSolicitudes()
    {
        // Capturamos el employee id (puede venir por GET o POST)
        $employee_number = isset($_REQUEST['employee_number']) ? $_REQUEST['employee_number'] : '';
        echo $this->model->consultarSolicitudes($employee_number);
    }

    public function consultarSolicitudesSI()
    {
        // Capturamos el employee id (puede venir por GET o POST)
        $employee_number = isset($_REQUEST['employee_number']) ? $_REQUEST['employee_number'] : '';
        $type_request = isset($_REQUEST['type_request']) ? $_REQUEST['type_request'] : '';
        echo $this->model->consultarSolicitudesSI($employee_number, $type_request);
    }


    public function crearSolicitud()
    {
        $data['employee_number'] = $_POST['employee_number'];

        echo $this->model->crearSolicitud($data);
    }

    public function cambiarEstado()
    {
        $data['id'] = $_POST['id'];
        $data['estado'] = $_POST['estado'];
        echo $this->model->cambiarEstado($data);
    }

    public function editarSolicitud()
    {
        $data = array();
        // Usa el nombre correcto del input para el id
        $data['request_vacation_id'] = $_POST['id'];
        // Usa el nombre correcto de los inputs
        $data['labelDateR'] = $_POST['labelDateR'];
        $data['labelDays']    = $_POST['labelDays'];
        $data['labelDateL']   = $_POST['labelDateL'];
        $data['labelDateC']   = $_POST['labelDateC'];
        $data['labelDateF']   = $_POST['labelDateF'];
        $data['state']        = '1';
        $data['labelTurno']   = $_POST['labelTurno'];
    
        echo $this->model->editarSolicitud($data);
    }

}
