<?php

class vacaciones__model
{
    private $db;

    public function __construct()
    {
        $this->db = Conectar::conexion();
        mysqli_set_charset($this->db, 'utf8');
    }


    public function consultarTotalDias($employee_number, $preev_year)
    {
        mysqli_select_db($this->db, "hr_system");
    
        // Cambiar el año si preev_year es true
        $anio = date('Y');
        if ($preev_year === true || $preev_year === "true" || $preev_year == 1) {
            $anio = date('Y', strtotime('+1 year'));
        }
    
        $sql = "SELECT SUM(days) AS total_dias 
                FROM vacation_requests 
                WHERE state = 2 
                AND employee_number = " . intval($employee_number) . " 
                AND year = " . intval($anio);
    
        $result = mysqli_query($this->db, $sql);
    
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            return $row['total_dias'] ?? 0; // Si es NULL, devuelve 0
        } else {
            return 0; // Si hay error en la consulta, devuelve 0
        }
    }
    

    public function cambiarEstado($data)
    {
        mysqli_select_db($this->db, "hr_system");

        if (!isset($_SESSION)) {
            session_start();
        }

        $set = "state = '" . $data['estado'] . "'"; 
        $condicion = "request_vacation_id = " . $data['id'];

        $sqlUpdate = "UPDATE vacation_requests SET " . $set . " WHERE " . $condicion . "";

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
                'cambio' => TRUE,
                'datos' => $data,
                'id_updated' => $_SESSION['usuario']
            );
        }

        return json_encode($respuesta);
    }

    public function cambiarEstadoSI($data)
    {
        mysqli_select_db($this->db, "hr_system");

        if (!isset($_SESSION)) {
            session_start();
        }

        $set = "state = '" . $data['estado'] . "'"; 
        $condicion = "request_id = " . $data['id'];

        $sqlUpdate = "UPDATE requests SET " . $set . " WHERE " . $condicion . "";

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
                'cambio' => TRUE,
                'datos' => $data,
                'id_updated' => $_SESSION['usuario']
            );
        }

        return json_encode($respuesta);
    }
    
    public function consultarSolicitudes($employee_number,$preev_year)
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
                    DATE_FORMAT(vr.request_date, '%d/%m/%Y') AS request_date,
                    vr.days,
                    DATE_FORMAT(vr.start_date, '%d/%m/%Y') AS start_date,
                    DATE_FORMAT(vr.finish_date, '%d/%m/%Y') AS finish_date,
                    DATE_FORMAT(vr.back_date, '%d/%m/%Y') AS back_date,
                    vr.work_shift,
                    vr.year
                FROM vacation_requests vr
                INNER JOIN employees e ON vr.employee_number = e.employee_number_id";

        // Agregar condición si se recibe un employee id

        $sql .= " WHERE vr.employee_number = " . intval($employee_number) . " ";
        

        $sql .= " ORDER BY vr.request_date DESC, vr.state ASC ";

        

        $result = mysqli_query($this->db, $sql);

        if ($result) {
            $requests = array();
            $consultarTotalDias = $this->consultarTotalDias($employee_number,$preev_year);
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

    public function consultarSolicitudesSI($employee_number, $type_request)
    {
        mysqli_select_db($this->db, "hr_system");

        $sql = "SELECT 
                    r.request_id,
                    r.employee_number,
                    r.type_request,
                    e.name AS employee_name,
                    CASE
                        WHEN r.state = 0 THEN 'CREADA'
                        WHEN r.state = 1 THEN 'PROCESO'
                        WHEN r.state = 2 THEN 'APROVADA'
                        WHEN r.state = 3 THEN 'RECHAZADA'
                        ELSE 'Desconocido'
                    END AS estado,
                    DATE_FORMAT(r.required_date, '%d/%m/%Y %H:%i:%s') AS required_date,
                    DATE_FORMAT(r.request_date, '%d/%m/%Y %H:%i:%s') AS request_date
                FROM requests r
                INNER JOIN employees e ON r.employee_number = e.employee_number_id ";

        // Agregar condición si se recibe un employee id

        $sql .= " WHERE r.employee_number = " . intval($employee_number) . " ";
        $sql .= " AND r.type_request = " . intval($type_request) . " ";

        $sql .= " ORDER BY r.state ASC";
        

        $result = mysqli_query($this->db, $sql);

        if ($result) {
            $requests = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $requests[] = $row;
            }
            $respuesta = array(
                'error' => false,
                'msg' => 'Se encontraron las solicitudes de ingreso - salida',
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
        if($data['preev_year'] == true){
            $anioSiguiente = date('Y', strtotime('+1 year'));
            $sql = "INSERT INTO vacation_requests (employee_number,year) VALUES ('$employee_number',  $anioSiguiente)";
        }else{
            $sql = "INSERT INTO vacation_requests (employee_number) VALUES ('$employee_number')";
        }

        
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

    public function crearSolicitudSI($data)
    {
        mysqli_select_db($this->db, "hr_system");
    
        if (!isset($_SESSION)) {
            session_start();
        }
    
        $employee_number = mysqli_real_escape_string($this->db, $data['employee_number']);
        $type_request = mysqli_real_escape_string($this->db, $data['type_request']);
    
        $sql = "INSERT INTO requests (employee_number, type_request) VALUES ('$employee_number', '$type_request')";
        
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
        $set .= "back_date = '" . $data['labelDateL'] . "', ";
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

    public function editarSolicitudSI($data)
    {
        mysqli_select_db($this->db, "hr_system");

        if (!isset($_SESSION)) {
            session_start();
        }

        // Se arma la cadena SET con todos los campos a actualizar
        $set = "";
        $set .= "required_date = '" . $data['labelDateRequired'] . "', ";
        $set .= "state = '" . $data['state'] . "'";

        // Se utiliza employee_number_id (el id único del empleado) para identificar el registro a actualizar
        $condicion = "request_id = " . $data['request_id'];

        $sqlUpdate = "UPDATE requests SET " . $set . " WHERE " . $condicion;
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