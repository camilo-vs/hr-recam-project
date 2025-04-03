<?php

class empleados__model
{
    private $db;

    public function __construct()
    {
        $this->db = Conectar::conexion();
        mysqli_set_charset($this->db, 'utf8');
    }

    public function consultarDepartamentos()
    {
        mysqli_select_db($this->db, "hr_system");

        $sql = "SELECT role_id, name FROM roles;";
        $result = mysqli_query($this->db, $sql);

        if ($result) {
            $users = array();

            while ($row = mysqli_fetch_assoc($result)) {
                $users[] = $row;
            }

            $respuesta = array(
                'error' => false,
                'msg' => 'Consulta correcta',
                'sql' => $sql,
                'departamentos' => $users
            );
        } else {
            $respuesta = array(
                'error' => true,
                'msg' => 'Error en la consulta: ' . mysqli_error($this->db),
                'sql' => $sql,
                'departamentos' => []
            );
        }
        return json_encode($respuesta);
    }

    public function consultarDatosExtra($index)
    {
        mysqli_select_db($this->db, "hr_system");

        // Consulta única con JOINs:
        $sql = "SELECT 
                    r.role_id,
                    r.name AS roleName,
                    d.name AS departmentName,
                    IFNULL(s.name, 'No asignado') AS supervisorName
                FROM roles r
                INNER JOIN departments d ON r.department = d.department_id
                LEFT JOIN employees s ON d.supervisor_number = s.employee_number_id
                WHERE r.role_id = $index";
    
        $result = mysqli_query($this->db, $sql);
    
        if ($result) {
            if (mysqli_num_rows($result) == 0) {
                $respuesta = array(
                    'error' => true,
                    'sql'   => $sql
                );
            } else {
                $data = mysqli_fetch_assoc($result);
                $respuesta = array(
                    'error'           => false,
                    'msg'             => 'Consulta correcta',
                    'departmentName'  => $data['departmentName'],
                    'supervisorName'  => $data['supervisorName'],
                    'roleName'        => $data['roleName'],
                    'sql'             => $sql
                );
            }
        } else {
            $respuesta = array(
                'error' => true,
                'msg'   => 'Error en la consulta: ' . mysqli_error($this->db),
                'sql'   => $sql
            );
        }
        return json_encode($respuesta);
    }
    

    public function consultarEmpleados()
    {
        mysqli_select_db($this->db, "hr_system");

        $sql = "SELECT
                    employee.employee_number_id,
                    employee.name,
                    CASE
                        WHEN employee.active = 1 THEN 'ACTIVO'
                        WHEN employee.active = 0 THEN 'BAJA'
                        ELSE 'Desconocido'
                    END AS estado,
                    CASE
                        WHEN employee.genre = 1 THEN 'Masculino'
                        WHEN employee.genre = 0 THEN 'Femenino'
                        ELSE 'Desconocido'
                    END AS genero,
                    employee.genre AS genre_wname,
                    role.role_id AS role_wname,
                    role.name AS role_name,
                    DATE_FORMAT(employee.hire_date, '%d/%m/%Y %H:%i:%s') AS hire_date,
                    sup.name AS supervisor,
                    dept.name AS department,
                    DATE_FORMAT(employee.update_date, '%d/%m/%Y %H:%i:%s') AS update_date
                FROM employees employee
                INNER JOIN roles role ON employee.role = role.role_id
                INNER JOIN departments dept ON role.department = dept.department_id
                LEFT JOIN employees sup ON dept.supervisor_number = sup.employee_number_id
                ORDER BY employee.hire_date DESC";
    
        $result = mysqli_query($this->db, $sql);

        if ($result) {
            $users = array();

            while ($row = mysqli_fetch_assoc($result)) {
                $users[] = $row;
            }

            $respuesta = array(
                'error' => false,
                'msg' => 'Se encontraron los usuarios',
                'sql' => $sql,
                'registros' => $users
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

    public function crearEmpleado($data)
    {
        mysqli_select_db($this->db, "hr_system");

        $consultarExistencia = $this->consultarExistencia($data['employee_number_id']);
        if ($consultarExistencia['error']) {
            $respuesta = array(
                'error' => true,
                'msg' => $consultarExistencia['msg'],
                'sql' => $consultarExistencia['sql'],
                'resultado' => ''
            );
        } else {
            if ($consultarExistencia['resultado'] > 0) {
                $respuesta = array(
                    'error' => false,
                    'msg' => 'El numero de empleado ya se encuentra registrado',
                    'sql' => '',
                    'resultado' => '',
                    'creado' => false
                );
            } else {

                if (!isset($_SESSION)) {
                    session_start();
                }

                $values = "";
                $values .= "" . $data['employee_number_id'] . ",";
                $values .= "" . $data['puesto'] . ",";
                $values .= "'" . $data['username'] . "',";
                $values .= "" . $data['genero'] . ",";
                $values .= "" . $_SESSION['id'] . "";

                $sql = "INSERT INTO employees (
                        employee_number_id,
                        role,
                        name,
                        genre,
                        created_by) 
                        VALUES ($values)";

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
            }

            return json_encode($respuesta);
        }

    }

    public function editarEmpleado($data)
    {
        mysqli_select_db($this->db, "hr_system");

        if (!isset($_SESSION)) {
            session_start();
        }

        // Se arma la cadena SET con todos los campos a actualizar
        $set = "";
        $set .= "name = '" . $data['username'] . "', ";
        $set .= "genre = '" . $data['genero'] . "', ";
        $set .= "role = '" . $data['role'] . "', ";
        $set .= "updated_by = " . $_SESSION['id'] . ", ";
        $set .= "update_date = NOW()";

        // Se utiliza employee_number_id (el id único del empleado) para identificar el registro a actualizar
        $condicion = "employee_number_id = " . $data['employee_number_id'];

        $sqlUpdate = "UPDATE employees SET " . $set . " WHERE " . $condicion;
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


    public function cambiarEstado($data)
    {

        mysqli_select_db($this->db, "hr_system");

        if (!isset($_SESSION)) {
            session_start();
        }

        $set = '';
        $set .= "active = '" . $data['estado'] . "',";
        $set .= "updated_by = " . $_SESSION['id'] . ",";
        $set .= "update_date = NOW(),";
        $set = substr($set, 0, -1);
        $condicion = "employee_number_id = " . $data['employee_number_id'] . "";

        $sqlUpdate = "UPDATE employees SET " . $set . " WHERE " . $condicion . "";

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
    public function consultarExistencia($employee_number_id)
    {
        mysqli_select_db($this->db, "hr_system");
        $condicion = '';
        $condicion .= " AND employee_number_id LIKE '" . $employee_number_id . "'";

        $sql = "SELECT count(*) total FROM employees where 1=1 " . $condicion;
 
        $result = mysqli_query($this->db, $sql);

        if (!$result) {
            $respuesta = array(
                'error' => true,
                'msg' => 'Error en la consulta: ' . mysqli_error($this->db),
                'sql' => $sql,
                'resultado' => ''
            );
        } else {
            $row = mysqli_fetch_assoc($result);

            $respuesta = array(
                'error' => false,
                'msg' => 'Todo Bien',
                'sql' => $sql,
                'resultado' => $row['total']
            );
        }
        return $respuesta;
    }

    public function validarNombre($data)
    {
        mysqli_select_db($this->db, "hr_system");
        $condicion = '';
        $condicion .= " AND employee_number_id LIKE '" . $data['employee_number_id'] . "'";

        $sql = "SELECT count(*) total FROM employees where 1=1 " . $condicion;

        $result = mysqli_query($this->db, $sql);

        if (!$result) {
            $respuesta = array(
                'error' => true,
                'msg' => 'Error en la consulta: ' . mysqli_error($this->db),
                'sql' => $sql,
                'registros' => ''
            );
        } else {
            $row = mysqli_fetch_assoc($result);

            $respuesta = array(
                'error' => false,
                'msg' => 'Todo Bien',
                'sql' => $sql,
                'registros' => $row
            );
        }
        return json_encode($respuesta);
    }
}