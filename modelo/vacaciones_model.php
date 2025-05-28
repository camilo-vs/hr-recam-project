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

        // Obtener hire_date del empleado
        $queryFecha = "SELECT hire_date FROM employees WHERE employee_number_id = " . intval($employee_number);
        $resFecha = mysqli_query($this->db, $queryFecha);

        $anio = date('Y');
        $anio_i = null;

        if ($resFecha && $rowFecha = mysqli_fetch_assoc($resFecha)) {
            $hireDate = $rowFecha['hire_date']; // formato YYYY-MM-DD
            $hireMonthDay = date('m-d', strtotime($hireDate));
            $currentMonthDay = date('m-d');
            $anio_actual = date('Y');

            if ($preev_year === true || $preev_year === "true" || $preev_year == 1) {
                // Periodo adelantado basado en hire_date comparando dia y mes
                if ($currentMonthDay < $hireMonthDay) {
                    // No ha llegado el aniversario → periodo adelantado anterior
                    $anio_i = $anio_actual;       // ej: 2025
                    $anio = $anio_actual + 1;     // ej: 2026
                } else {
                    // Ya pasó aniversario → periodo adelantado siguiente
                    $anio_i = $anio_actual + 1;   // ej: 2026
                    $anio = $anio_actual + 2;     // ej: 2027
                }
            } else {
                // lógica normal cuando no es preev_year
                if ($currentMonthDay < $hireMonthDay) {
                    $anio_i = $anio_actual - 1;   // ej: 2024
                    $anio = $anio_actual;         // ej: 2025
                } else {
                    $anio_i = $anio_actual;       // ej: 2025
                    $anio = $anio_actual + 1;     // ej: 2026
                }
            }
        }

        // Construcción de la consulta
        $sql = "SELECT SUM(days) AS total_dias 
            FROM vacation_requests 
            WHERE state = 2 
            AND employee_number = " . intval($employee_number) . " 
            AND year = " . intval($anio);

        // Agrega la condición para year_i si fue definida
        if (!is_null($anio_i)) {
            $sql .= " AND year_i = " . intval($anio_i);
        }

        $result = mysqli_query($this->db, $sql);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            return $row['total_dias'] ?? 0;
        } else {
            return 0;
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

    public function consultarSolicitudes($employee_number, $preev_year)
    {
        mysqli_select_db($this->db, "hr_system");
        $sql = "SELECT 
                vr.request_vacation_id,
                vr.employee_number,
                e.name AS employee_name,
                e.hire_date,
                CASE
                    WHEN vr.state = 0 THEN 'CREADA'
                    WHEN vr.state = 1 THEN 'PROCESO'
                    WHEN vr.state = 2 THEN 'APROBADA'
                    WHEN vr.state = 3 THEN 'RECHAZADA'
                    ELSE 'Desconocido'
                END AS estado,
                DATE_FORMAT(vr.request_date, '%d/%m/%Y') AS request_date,
                vr.days,
                DATE_FORMAT(vr.start_date, '%d/%m/%Y') AS start_date,
                DATE_FORMAT(vr.finish_date, '%d/%m/%Y') AS finish_date,
                DATE_FORMAT(vr.back_date, '%d/%m/%Y') AS back_date,
                vr.work_shift,
                vr.year,
                vr.year_i,
                c.url AS url_doc,

                CASE
                    WHEN CURRENT_DATE BETWEEN 
                        STR_TO_DATE(CONCAT(vr.year_i, '-', MONTH(e.hire_date), '-', DAY(e.hire_date)), '%Y-%m-%d')
                        AND DATE_SUB(STR_TO_DATE(CONCAT(vr.year, '-', MONTH(e.hire_date), '-', DAY(e.hire_date)), '%Y-%m-%d'), INTERVAL 1 DAY)
                    THEN 'ACTUAL'

                    WHEN CURRENT_DATE < STR_TO_DATE(CONCAT(vr.year_i, '-', MONTH(e.hire_date), '-', DAY(e.hire_date)), '%Y-%m-%d')
                    THEN 'ADELANTADO'

                    WHEN CURRENT_DATE > DATE_SUB(STR_TO_DATE(CONCAT(vr.year, '-', MONTH(e.hire_date), '-', DAY(e.hire_date)), '%Y-%m-%d'), INTERVAL 1 DAY)
                        AND vr.year_i >= YEAR(e.hire_date)
                    THEN 'ANTERIOR'

                    ELSE 'FUERA_DE_PERIODO_VALIDO'
                END AS tipo_periodo

            FROM vacation_requests vr
            INNER JOIN employees e ON vr.employee_number = e.employee_number_id
            LEFT JOIN archivo_contancia c ON c.request_vacation_id = vr.request_vacation_id";

        // Agregar condición si se recibe un employee id

        $sql .= " WHERE vr.employee_number = " . intval($employee_number) . " ";


        $sql .= " ORDER BY vr.state ASC, vr.request_date DESC ";


        $result = mysqli_query($this->db, $sql);

        if ($result) {
            $requests = array();
            $consultarTotalDias = $this->consultarTotalDias($employee_number, $preev_year);
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
                        WHEN r.state = 2 THEN 'APROBADA'
                        WHEN r.state = 3 THEN 'RECHAZADA'
                        ELSE 'Desconocido'
                    END AS estado,
                    DATE_FORMAT(r.required_date, '%d/%m/%Y %H:%i:%s') AS required_date,
                    DATE_FORMAT(r.request_date, '%d/%m/%Y %H:%i:%s') AS request_date,
                    c.url as url_doc
                FROM requests r
                INNER JOIN employees e ON r.employee_number = e.employee_number_id 
                LEFT JOIN archivo_contancia c ON c.request_id = r.request_id
                ";

        // Agregar condición si se recibe un employee id

        $sql .= " WHERE r.employee_number = " . intval($employee_number) . " ";

        $sql .= " AND r.type_request = " . intval($type_request) . " ";

        $sql .= " GROUP BY r.request_id, r.employee_number, r.type_request, e.name, r.state, r.required_date, r.request_date ";
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
        $hire_date = isset($data['hire_date']) ? $data['hire_date'] : null;

        $year = date('Y');
        $year_i = "NULL"; // valor por defecto

        // Verificar si ya cumplió un año desde hire_date
        $cumple_un_anio = false;
        $hire_date_iso = null;

        if ($hire_date) {
            $fecha_parts = explode('/', $hire_date); // dd/mm/yyyy
            if (count($fecha_parts) === 3) {
                $hire_date_iso = $fecha_parts[2] . '-' . $fecha_parts[1] . '-' . $fecha_parts[0];
                $hire_timestamp = strtotime($hire_date_iso);
                $unAnioDespues = strtotime('+1 year', $hire_timestamp);
                $ahora = time();

                if ($ahora >= $unAnioDespues) {
                    $cumple_un_anio = true;
                }
            }
        }

        if ($data['preev_year'] == 'true') {
            $hireMonthDay = date('m-d', strtotime($hire_date_iso));
            $currentMonthDay = date('m-d');

            if ($currentMonthDay >= $hireMonthDay) {
                $year_i = $year + 1;
                $year = $year + 2;
            } else {
                $year_i = $year;
                $year = $year + 1;
            }
        } else {
            // Si no es preev_year, pero aún no cumple un año, asignar periodo adelantado (ej. 2025-2026)
            if (!$cumple_un_anio) {
                $year_i = $year + 1;
                $year = $year + 2;
            } else {
                // Cumplió un año → periodo normal basado en hire_date
                if ($hire_date_iso) {
                    $hireMonthDay = date('m-d', strtotime($hire_date_iso));
                    $currentMonthDay = date('m-d');

                    if ($currentMonthDay >= $hireMonthDay) {
                        $year_i = $year;
                        $year = $year + 1;
                    } else {
                        $year_i = $year - 1;
                        $year = $year;
                    }
                } else {
                    $year_i = $year;
                    $year = $year + 1;
                }
            }
        }

        // Consulta insert (NULL sin comillas)
        $sql = "INSERT INTO vacation_requests (employee_number, year, year_i)
                VALUES ('$employee_number', $year, " . ($year_i === "NULL" ? "NULL" : intval($year_i)) . ")";

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
                'year' => $year,
                'year_i' => $year_i,
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

    public function subirConstancia($data)
    {
        mysqli_select_db($this->db, "hr_system");

        if (isset($data['archivo']) && isset($data['opcion'])) {
            $opcion = intval($_POST['opcion']);
            $constancia = '';
            switch ($opcion) {
                case 0:
                    $constancia = 'Ingresos';
                    break;
                case 1:
                    $constancia = 'Salida';
                    break;
                default:
                    $constancia = 'Vacaciones';
                    break;
            }

            $archivo = $data['archivo'];

            // Validar que sea un PDF
            $tipo = mime_content_type($archivo['tmp_name']);
            if ($tipo !== 'application/pdf') {
                http_response_code(400);
                echo json_encode(['error' => true, 'msg' => 'Solo se permiten archivos PDF']);
                exit;
            }

            // Crear carpeta de destino según opción
            $carpetaDestino = "Constancias/$constancia/";
            if (!is_dir($carpetaDestino)) {
                mkdir($carpetaDestino, 0777, true);
            }

            $nombreFinal = "" . $constancia . "_" . $data['id'] . ".pdf";
            $rutaFinal = $carpetaDestino . $nombreFinal;

            // Revisar si ya existe un archivo para este ID y tipo
            $checkQuery = "";
            $whereCondition = "";
            switch ($opcion) {
                case 2:
                    $whereCondition = "request_vacation_id = " . intval($data['id']);
                    break;
                default:
                    $whereCondition = "request_id = " . intval($data['id']);
                    break;
            }

            $checkQuery = "SELECT id FROM archivo_contancia WHERE $whereCondition AND Tipo = '$constancia'";
            $checkResult = mysqli_query($this->db, $checkQuery);
            $registroExiste = ($checkResult && mysqli_num_rows($checkResult) > 0);

            // Subir archivo (sobreescribir si ya existe)
            $subido = move_uploaded_file($archivo['tmp_name'], $rutaFinal);

            if ($registroExiste) {
                // Actualizar registro existente
                $row = mysqli_fetch_assoc($checkResult);
                $updateSql = "UPDATE archivo_contancia SET url = '$rutaFinal', fecha_subida = NOW() WHERE id = " . intval($row['id']);
                $result = mysqli_query($this->db, $updateSql);

                $respuesta = [
                    'error' => !$result,
                    'msg' => $result ? 'Archivo actualizado correctamente' : 'Error al actualizar: ' . mysqli_error($this->db),
                    'url' => $rutaFinal,
                    'sql' => $updateSql,
                    'actualizado' => true,
                    'subido' => $subido
                ];
            } else {
                // Insertar nuevo registro
                $values = "'" . $rutaFinal . "',";
                switch ($opcion) {
                    case 2:
                        $values .= "null," . intval($data['id']) . ",";
                        break;
                    default:
                        $values .= intval($data['id']) . ",null,";
                        break;
                }
                $values .= "'" . $constancia . "'";

                $insertSql = "INSERT INTO archivo_contancia (url, request_id, request_vacation_id, Tipo) VALUES ($values)";
                $result = mysqli_query($this->db, $insertSql);

                $respuesta = [
                    'error' => !$result,
                    'msg' => $result ? 'Archivo insertado correctamente' : 'Error al insertar: ' . mysqli_error($this->db),
                    'url' => $rutaFinal,
                    'sql' => $insertSql,
                    'insertado' => true,
                    'subido' => $subido
                ];
            }

            return json_encode($respuesta);
        } else {
            return json_encode([
                'error' => true,
                'msg' => 'Error al recibir el documento',
                'resultado' => ''
            ]);
        }
    }

}