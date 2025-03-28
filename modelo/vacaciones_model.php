<?php

class vacaciones__model
{
    private $db;

    public function __construct()
    {
        $this->db = Conectar::conexion();
        mysqli_set_charset($this->db, 'utf8');
    }


    public function consultarTotalDias($employee_number)
    {
        mysqli_select_db($this->db, "hr_system");

        $sql = "SELECT SUM(days) AS total_dias FROM vacation_requests WHERE state = 2 AND employee_number = " . intval($employee_number);
        $result = mysqli_query($this->db, $sql);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            return $row['total_dias'] ?? 0; // Si es NULL, devuelve 0
        } else {
            return 0; // Si hay error en la consulta, devuelve 0
        }
    }

    
    public function consultarSolicitudes($employee_number)
    {
        mysqli_select_db($this->db, "hr_system");

        $sql = "SELECT 
                    vr.request_vacation_id,
                    vr.employee_number,
                    e.name AS employee_name,
                    CASE
                        WHEN vr.state = 0 THEN 'CREADA'
                        WHEN vr.state = 1 THEN 'PROCESO'
                        WHEN vr.state = 2 THEN 'APROVADA'
                        WHEN vr.state = 3 THEN 'RECHAZADA'
                        ELSE 'Desconocido'
                    END AS estado,
                    DATE_FORMAT(vr.request_date, '%d/%m/%Y %H:%i:%s') AS request_date,
                    vr.days,
                    DATE_FORMAT(vr.start_date, '%d/%m/%Y') AS start_date,
                    DATE_FORMAT(vr.finish_date, '%d/%m/%Y') AS finish_date,
                    vr.work_shift
                FROM vacation_requests vr
                INNER JOIN employees e ON vr.employee_number = e.employee_number_id ";

        // Agregar condición si se recibe un employee id

            $sql .= "WHERE vr.employee_number = " . intval($employee_number) . " ";
        

        $sql .= "ORDER BY vr.request_date DESC";
        

        $result = mysqli_query($this->db, $sql);

        if ($result) {
            $requests = array();
            $consultarTotalDias = $this->consultarTotalDias($employee_number);
            while ($row = mysqli_fetch_assoc($result)) {
                $requests[] = $row;
            }
            $respuesta = array(
                'count' => $consultarTotalDias,
                'error' => false,
                'msg' => 'Se encontraron las solicitudes de vacaciones',
                'sql' => $sql,
                'registros' => $requests
            );
        } else {
            $respuesta = array(
                'error' => true,
                'msg' => 'Error en la consulta: ' . mysqli_error($this->db),
                'sql' => $sql,
                'registros' => []
            );
        }
        return json_encode($respuesta);
    }


    public function crearSolicitud($data)
    {
        mysqli_select_db($this->db, "hr_system");

        if (!isset($_SESSION)) {
            session_start();
        }

        $employee_number = mysqli_real_escape_string($this->db, $data['employee_number']);
        $sql = "INSERT INTO vacation_requests (employee_number) VALUES ('$employee_number')";
        
        $result = mysqli_query($this->db, $sql);

        if (!$result) {

            $respuesta = array(
                'error' => true,
                'msg' => 'Error al insertar: ' . mysqli_error($this->db),
                'sql' => $sql,
                'resultado' => ''
            );
        } else {
            $lastInsertId = mysqli_insert_id($this->db);
            $respuesta = array(
                'error' => false,
                'msg' => 'Todo Bien',
                'sql' => $sql,
                'resultado' => $result,
                'id' => $lastInsertId,
                'datos' => $data,
                'created_by' => $_SESSION['usuario'],
                'creado' => true,
            );
        }

        return json_encode($respuesta);
    }

    public function editarSolicitud($data)
    {
        mysqli_select_db($this->db, "hr_system");

        if (!isset($_SESSION)) {
            session_start();
        }

        // Se arma la cadena SET con todos los campos a actualizar
        $set = "";
        $set .= "request_date = '" . $data['labelDateR'] . "', ";
        $set .= "days = '" . $data['labelDays'] . "', ";
        $set .= "start_date = '" . $data['labelDateC'] . "', ";
        $set .= "finish_date = '" . $data['labelDateF'] . "', ";
        $set .= "state = '" . $data['state'] . "', ";
        $set .= "work_shift = '" . $data['labelTurno'] . "'";

        // Se utiliza employee_number_id (el id único del empleado) para identificar el registro a actualizar
        $condicion = "request_vacation_id = " . $data['request_vacation_id'];

        $sqlUpdate = "UPDATE vacation_requests SET " . $set . " WHERE " . $condicion;
        $resultUpdate = mysqli_query($this->db, $sqlUpdate);

        if (!$resultUpdate) {
            $respuesta = array(
                'error' => true,
                'msg' => 'Error al actualizar: ' . mysqli_error($this->db),
                'sql' => $sqlUpdate,
                'respuesta' => $resultUpdate
            );
        } else {
            $respuesta = array(
                'error' => false,
                'msg' => 'Todo Bien',
                'sql' => $sqlUpdate,
                'respuesta' => $resultUpdate,
                'cambio' => true,
                'datos' => $data,
                'id_updated' => $_SESSION['usuario']
            );
        }

        return json_encode($respuesta);
    }

}