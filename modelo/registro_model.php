<?php

class registro_model
{
    private $db;

    public function __construct()
    {
        // Asume que Conectar::conexion() está configurado adecuadamente
        $this->db = Conectar::conexion();
        mysqli_set_charset($this->db, 'utf8');
    }

    public function registro($data)
    {
        // Verificar si el nombre de usuario ya está registrado
        $usuarioExistente = $this->validarCuenta($data); // Puedes reutilizar la misma función que usas en el login

        if ($usuarioExistente['numero_registros'] > 0) {
            // Si el usuario ya existe, retornar un mensaje de error
            $respuesta = array(
                'error' => true,
                'msg' => 'El nombre de usuario ya está registrado',
                'numero_registros' => $usuarioExistente['numero_registros'],
                'sql' => $usuarioExistente['sql'],
                'activo' => false
            );
        } else {
            // Si el usuario no existe, proceder con el registro
            mysqli_select_db($this->db, "hr_system");

$contraseñaHash = password_hash($data['contraseña'], PASSWORD_BCRYPT);

$name = $data['nombre'];
$active = 1; 
$user_type = 1;
$created_by = 1; 
$updated_by = 1;


$values = "'" . mysqli_real_escape_string($this->db, $name) . "', " . 
          (int)$active . ", '" . mysqli_real_escape_string($this->db, $contraseñaHash) . "', '" . 
          mysqli_real_escape_string($this->db, $user_type) . "', " . 
          (int)$created_by . ", " . (int)$updated_by;


$sql = "INSERT INTO users (name, active, password, user_type_id, created_by, updated_by) VALUES ($values)";

// Ejecutar la consulta preparada
$stmt = mysqli_prepare($this->db, $sql);

if ($stmt) {
    // Ejecutar la consulta
    $success = mysqli_stmt_execute($stmt);

    if ($success) {
        // Registro exitoso
        $respuesta = array(
            'error' => false,
            'msg' => 'Usuario registrado exitosamente',
            'sql' => $sql,
            'numero_registros' => 1,
            'activo' => true
        );
    } else {
        // Error al registrar el usuario
        $respuesta = array(
            'error' => true,
            'msg' => 'Hubo un error al registrar el usuario.',
            'sql' => $sql,
            'numero_registros' => 0,
            'activo' => false
        );
    }

    // Cerrar la declaración
    mysqli_stmt_close($stmt);
            } else {
                // Error en la consulta
                $respuesta = array(
                    'error' => true,
                    'msg' => 'Error en la consulta: ' . mysqli_error($this->db),
                    'sql' => $sql,
                    'numero_registros' => 0,
                    'activo' => false
                );
            }
        }
    
        // Devolver la respuesta en formato JSON
        echo json_encode($respuesta);
    }
    public function validarCuenta($data)
    {
        // Validación del nombre de usuario: asegurar que esté correctamente formateado (puedes agregar más validaciones si es necesario)
        if (empty($data['nombre'])) {
            return array(
                'error' => true,
                'msg' => 'El nombre de usuario no puede estar vacío',
                'sql' => '',
                'registros' => '',
                'numero_registros' => 0
            );
        }
        $usuario = trim($data['nombre']);
        mysqli_select_db($this->db, "hr_system");
        // Preparar la consulta SQL para verificar si el usuario existe
        $sql = "SELECT COUNT(id) as total FROM users WHERE name = ?"; 
 
        $stmt = mysqli_prepare($this->db, $sql);

        if ($stmt) {
         
            mysqli_stmt_bind_param($stmt, 's',  $usuario);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            $numero_registros = $row['total'];

            mysqli_stmt_close($stmt);

            return array(
                'error' => false,
                'msg' => 'Todo Bien',
                'sql' => $sql,
                'registros' => $row,
                'numero_registros' => $numero_registros
            );
        } else {
            return array(
                'error' => true,
                'msg' => 'Error en la consulta: ' . mysqli_error($this->db),
                'sql' => $sql,
                'registros' => '',
                'numero_registros' => 0
            );
        }
    }    
}
