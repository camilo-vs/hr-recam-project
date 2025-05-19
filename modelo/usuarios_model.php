<?php

class usuarios_model
{
    private $db;

    public function __construct()
    {
        $this->db = Conectar::conexion();
        mysqli_set_charset($this->db, 'utf8');
    }

    public function consultarUsuarios()
    {
        mysqli_select_db($this->db, "hr_system");

        $sql = "SELECT
                user.id,
                user.name,
                CASE
                    WHEN user.active = 1 THEN 'ACTIVO'
                    WHEN user.active = 2 THEN 'BAJA'
                    ELSE 'Desconocido'
                END AS estado,
                user_type.name AS user_type,
                DATE_FORMAT(user.creation_date, '%d/%m/%Y %H:%i:%s') AS creation_date,
                (SELECT name FROM users WHERE id = user.created_by) created_by,
                 DATE_FORMAT(user.update_date, '%d/%m/%Y %H:%i:%s') AS update_date,
                (SELECT name FROM users WHERE id = user.updated_by) updated_by
                FROM users user inner join user_types user_type on user.user_type = user_type.user_type_id
                ORDER BY user.creation_date DESC";
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

    public function crearUsuario($data)
    {
        mysqli_select_db($this->db, "hr_system");

        $consultarExistencia = $this->consultarExistencia($data['username']);
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
                    'msg' => 'El nombre del usuario ya se encuentra registrado',
                    'sql' => '',
                    'resultado' => '',
                    'creado' => false
                );
            } else {

                if (!isset($_SESSION)) {
                    session_start();
                }

                $contraseñaHash = password_hash($data['confirmPassword'], PASSWORD_BCRYPT);

                $values = "";
                $values .= "'" . $data['username'] . "',";
                $values .= "'" . $contraseñaHash . "',";
                $values .= "" . $data['userType'] . ",";
                $values .= "" . $_SESSION['id'] . ",";
                $values .= "1,";
                $values = rtrim($values, ',');


                $sql = "INSERT INTO users (
                        name, 
                        password,
                        user_type,
                        created_by,
                        active) 
                        VALUES (" . $values . ")";

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
                        'creado' => true
                    );
                }
            }

            return json_encode($respuesta);
        }

    }

    public function editarUsuario($data)
    {
        mysqli_select_db($this->db, "hr_system");

        $consultarExistencia = $this->consultarExistencia($data['username']);
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
                    'msg' => 'El nombre del usuario ya se encuentra registrado',
                    'sql' => '',
                    'resultado' => '',
                    'creado' => false
                );
            } {
                if (!isset($_SESSION)) {
                    session_start();
                }

                $set = '';
                $set .= "name = '" . $data['username'] . "',";
                $set .= "user_type = " . $data['userType'] . ",";
                if (isset($data['confirmPassword']) && $data['confirmPassword'] != null) {
                    $contraseñaHash = password_hash($data['confirmPassword'], PASSWORD_BCRYPT);
                    $set .= "password = '" . $contraseñaHash . "',";
                }
                $set .= "updated_by = " . $_SESSION['id'] . ",";
                $set .= "update_date = NOW(),";
                $set = substr($set, 0, -1);
                $condicion = "id = " . $data['id'] . "";

                $sqlUpdate = "UPDATE users SET " . $set . " WHERE " . $condicion . "";

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
            }
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
        $condicion = "id = " . $data['id'] . "";

        $sqlUpdate = "UPDATE users SET " . $set . " WHERE " . $condicion . "";

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
    
    public function consultarExistencia($nombre)
    {
        mysqli_select_db($this->db, "hr_system");
        $condicion = '';
        $condicion .= " AND name LIKE '" . $nombre . "'";

        $sql = "SELECT count(*) total FROM users where 1=1 " . $condicion;

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
        $condicion .= " AND name LIKE '" . $data['name'] . "'";

        $sql = "SELECT count(*) total FROM users where 1=1 " . $condicion;

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