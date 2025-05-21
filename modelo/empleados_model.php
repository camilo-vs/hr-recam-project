<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
                    'sql' => $sql
                );
            } else {
                $data = mysqli_fetch_assoc($result);
                $respuesta = array(
                    'error' => false,
                    'msg' => 'Consulta correcta',
                    'departmentName' => $data['departmentName'],
                    'supervisorName' => $data['supervisorName'],
                    'roleName' => $data['roleName'],
                    'sql' => $sql
                );
            }
        } else {
            $respuesta = array(
                'error' => true,
                'msg' => 'Error en la consulta: ' . mysqli_error($this->db),
                'sql' => $sql
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
                DATE_FORMAT(employee.hire_date, '%d/%m/%Y') AS hire_date,
                sup.name AS supervisor,
                dept.name AS department,
                DATE_FORMAT(employee.update_date, '%d/%m/%Y %H:%i:%s') AS update_date,
                employee.nss,
                employee.rfc,
                employee.curp,
                employee.phone,
                employee.address,
                employee.birth_date,
                employee.type as id_type,
                employee.url_img,
                CASE
                    WHEN employee.type = 1 THEN 'RECAM'
                    WHEN employee.type = 0 THEN 'GPI'
                    ELSE 'Desconocido'
                END AS type,
                DATE_FORMAT(emp_last.change_date, '%d/%m/%Y %H:%i:%s') AS change_date,
                UPPER(users.name) AS change_type
            FROM employees employee
            INNER JOIN roles role ON employee.role = role.role_id
            INNER JOIN departments dept ON role.department = dept.department_id
            LEFT JOIN employees sup ON dept.supervisor_number = sup.employee_number_id

            -- 🔍 Solo el cambio más reciente por empleado
            LEFT JOIN (
                SELECT esc.*
                FROM employee_status_changes esc
                INNER JOIN (
                    SELECT employee_id, MAX(change_date) AS max_date
                    FROM employee_status_changes
                    GROUP BY employee_id
                ) latest ON esc.employee_id = latest.employee_id AND esc.change_date = latest.max_date
            ) emp_last ON emp_last.employee_id = employee.employee_number_id

            LEFT JOIN users ON users.id = emp_last.changed_by_user_id

            ORDER BY employee.employee_number_id ASC";

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

    public function consultarEmpleadosSolicitud()
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
                    DATE_FORMAT(employee.hire_date, '%d/%m/%Y') AS hire_date,
                    sup.name AS supervisor,
                    dept.name AS department,
                    DATE_FORMAT(employee.update_date, '%d/%m/%Y %H:%i:%s') AS update_date,
                    employee.nss,
                    employee.rfc,
                    employee.curp,
                    employee.phone,
                    employee.address,
                    employee.birth_date,
                    employee.type as id_type,
                     CASE
                        WHEN employee.type = 1 THEN 'RECAM'
                        WHEN employee.type = 0 THEN 'GPI'
                        ELSE 'Desconocido'
                    END AS type
                FROM employees employee
                INNER JOIN roles role ON employee.role = role.role_id
                INNER JOIN departments dept ON role.department = dept.department_id
                LEFT JOIN employees sup ON dept.supervisor_number = sup.employee_number_id
                WHERE employee.active = 1
                ORDER BY employee.employee_number_id ASC";

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
                $values .= "'" . mysqli_real_escape_string($this->db, $data['new_hire_date']) . "',";
                $values .= intval($data['employee_number_id']) . ",";                  // numérico
                $values .= intval($data['puesto']) . ",";                              // numérico
                $values .= "'" . mysqli_real_escape_string($this->db, $data['username']) . "',";
                $values .= intval($data['genero']) . ",";                              // numérico
                $values .= "'" . mysqli_real_escape_string($this->db, $data['new_nss']) . "',";
                $values .= "'" . mysqli_real_escape_string($this->db, $data['new_curp']) . "',";
                $values .= "'" . mysqli_real_escape_string($this->db, $data['new_rfc']) . "',";
                $values .= "'" . mysqli_real_escape_string($this->db, $data['new_birth_date']) . "',";
                $values .= "'" . mysqli_real_escape_string($this->db, $data['new_phone']) . "',";
                $values .= "'" . mysqli_real_escape_string($this->db, $data['new_address']) . "',";
                $values .= intval($_SESSION['id']);

                $sql = "INSERT INTO employees (
                        hire_date,
                        employee_number_id,
                        role,
                        name,
                        genre,
                        nss,
                        curp,
                        rfc,
                        birth_date,
                        phone,
                        address,
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

        $set = "";
        $set .= "employee_number_id = '" . $data['employee_number_change'] . "', ";
        $set .= ($data['editTYPE'] != '') ? "type = '" . $data['editTYPE'] . "', " : "type = null, ";
        $set .= "name = '" . $data['username'] . "', ";
        $set .= "genre = '" . $data['genero'] . "', ";
        $set .= "role = '" . $data['role'] . "', ";
        $set .= ($data['nss'] != '') ? "nss = '" . $data['nss'] . "', " : "nss = null, ";
        $set .= ($data['curp'] != '') ? "curp = '" . $data['curp'] . "', " : "curp = curp, ";
        $set .= ($data['rfc'] != '') ? "rfc = '" . $data['rfc'] . "', " : "rfc = null, ";
        $set .= ($data['birth_date'] != '') ? "birth_date = '" . $data['birth_date'] . "', " : "birth_date = null, ";
        $set .= ($data['phone'] != '') ? "phone = '" . $data['phone'] . "', " : "phone = null, ";
        $set .= ($data['address'] != '') ? "address = '" . $data['address'] . "', " : "address = null, ";

        if (isset($data['imagen']) && is_array($data['imagen']) && $data['imagen']['tmp_name'] != '') {
            $extension = pathinfo($data['imagen']['name'], PATHINFO_EXTENSION);
            $nombreArchivo = 'empleado_' . $data['employee_number_change'] . '.' . $extension;
            $rutaDestino = 'assets/img/empleados/' . $nombreArchivo;

            if (file_exists($rutaDestino)) {
                unlink($rutaDestino);
            }

            if (move_uploaded_file($data['imagen']['tmp_name'], $rutaDestino)) {
                $set .= "url_img = '" . $rutaDestino . "', ";
            }
        }

        $set .= "updated_by = " . $_SESSION['id'] . ", ";
        $set .= "update_date = NOW()";

        $condicion = "employee_number_id = '" . $data['employee_number_id'] . "'";
        $sqlUpdate = "UPDATE employees SET $set WHERE $condicion";

        $resultUpdate = mysqli_query($this->db, $sqlUpdate);

        if (!$resultUpdate) {
            $respuesta = [
                'error' => true,
                'msg' => 'Error al actualizar: ' . mysqli_error($this->db),
                'sql' => $sqlUpdate,
            ];
        } else {
            $respuesta = [
                'error' => false,
                'msg' => 'Todo Bien',
                'sql' => $sqlUpdate,
                'cambio' => true,
                'datos' => $data,
                'url' => $rutaDestino,
                'created_by' => $_SESSION['usuario']
            ];
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
            $registro_cambio = $this->registrarCambio($data, $_SESSION['id']);
            $respuesta = array(
                'error' => false,
                'msg' => 'Todo Bien',
                'sql' => $sqlUpdate,
                'respuesta' => $resultUpdate,
                'cambio' => TRUE,
                'datos' => $data,
                'insertRegistros' => $registro_cambio,
                'id_updated' => $_SESSION['usuario']
            );
        }


        return json_encode($respuesta);
    }

    public function registrarCambio($data, $id)
    {
        $cambio = '';
        if ($data['estado'] == 1) {
            $cambio = 'alta';
        } else if ($data['estado'] == 0) {
            $cambio = 'baja';
        }
        $values = "";
        $values .= '' . $data['employee_number_id'] . ',';
        $values .= '"' . $cambio . '",';
        $values .= 'NOW(),';
        $values .= '' . $id . '';

        $sql = "INSERT INTO employee_status_changes (
                employee_id,
                change_type,
                change_date,
                changed_by_user_id) 
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
            $respuesta = array(
                'error' => false,
                'msg' => 'Todo Bien',
                'sql' => $sql,
                'resultado' => $result,
                'creado' => true,
            );
        }
        return $respuesta;
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

    public function generarReporte($data)
    {
        mysqli_select_db($this->db, "hr_system");

        $condiciones = " WHERE 1 = 1 ";

        if ($data['empresa'] != '') {
            $empresa = mysqli_real_escape_string($this->db, $data['empresa']);
            $condiciones .= " AND employee.type = '$empresa'";
        }

        if (!empty($data['genero'])) {
            $genero = mysqli_real_escape_string($this->db, $data['genero']);
            $condiciones .= " AND employee.genre = '$genero'";
        }

        if (!empty($data['puesto'])) {
            $puesto = mysqli_real_escape_string($this->db, $data['puesto']);
            $condiciones .= " AND employee.role = '$puesto'";
        }

        if (!empty($data['fechaInicio']) && !empty($data['fechaFin'])) {
            $fechaInicio = mysqli_real_escape_string($this->db, $data['fechaInicio']);
            $fechaFin = mysqli_real_escape_string($this->db, $data['fechaFin']);

            // Convertir de dd/mm/yyyy a yyyy-mm-dd
            $fechaInicio = DateTime::createFromFormat('d/m/Y', $fechaInicio)->format('Y-m-d');
            $fechaFin = DateTime::createFromFormat('d/m/Y', $fechaFin)->format('Y-m-d');
            $condiciones .= " AND employee.hire_date BETWEEN '$fechaInicio' AND '$fechaFin'";
        }

        if ($data['estado'] != '') {
            $estado = mysqli_real_escape_string($this->db, $data['estado']);
            $condiciones .= " AND employee.active = '$estado'";
        }


        $sql = "SELECT
                    employee.employee_number_id as NUMERO_DE_EMPLEADO,
                        CASE
                        WHEN employee.type = 1 THEN 'RECAM'
                        WHEN employee.type = 0 THEN 'GPI'
                        ELSE 'Desconocido'
                    END AS EMPRESA,
                    employee.name as NOMBRE_DEL_EMPLEADO,
                    CASE
                        WHEN employee.active = 1 THEN 'ACTIVO'
                        WHEN employee.active = 0 THEN 'BAJA'
                        ELSE 'Desconocido'
                    END AS ESTADO,
                    CASE
                        WHEN employee.genre = 1 THEN 'Masculino'
                        WHEN employee.genre = 0 THEN 'Femenino'
                        ELSE 'Desconocido'
                    END AS GENERO,
                    role.name AS PUESTO_DEL_EMPLEADO,
                    DATE_FORMAT(employee.hire_date, '%d/%m/%Y') AS FECHA_DE_ALTA,
                    sup.name AS SUPERVISOR,
                    dept.name AS DEPARTAMENTO,
                    employee.nss AS NSS,
                    employee.rfc AS RFC,
                    employee.curp AS CURP,
                    employee.phone AS TELEFONO,
                    employee.address AS DIRECCION,
                    DATE_FORMAT(employee.birth_date, '%d/%m/%Y') AS FECHA_DE_NACIMIENTO
                
                FROM employees employee
                INNER JOIN roles role ON employee.role = role.role_id
                INNER JOIN departments dept ON role.department = dept.department_id
                LEFT JOIN employees sup ON dept.supervisor_number = sup.employee_number_id
                $condiciones
                ORDER BY employee.hire_date DESC";

        $result = mysqli_query($this->db, $sql);

        if ($result) {
            $users = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $users[] = $row;
            }

            return json_encode([
                'error' => false,
                'msg' => 'Se encontraron los usuarios',
                'sql' => $sql,
                'registros' => $users
            ]);
        } else {
            return json_encode([
                'error' => true,
                'msg' => 'Error en la consulta: ' . mysqli_error($this->db),
                'sql' => $sql,
                'registros' => []
            ]);
        }
    }
}