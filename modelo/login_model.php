<?php

class login_model
{
    private $db;

    public function __construct()
    {
        // Asume que Conectar::conexion() está configurado adecuadamente
        $this->db = Conectar::conexion();
        mysqli_set_charset($this->db, 'utf8');
    }

    public function logear($data)
    {
        // Validar si el usuario está registrado
        $UsuarioAlta = $this->validarCuenta($data);
    
        if ($UsuarioAlta['numero_registros'] == 0) {
            $respuesta = array(
                'error' => true,
                'msg' => 'El usuario no está registrado',
                'numero_registros' => $UsuarioAlta['numero_registros'],
                'sql' => $UsuarioAlta['sql'],
                'activo' => false
            );
        } else {
            mysqli_select_db($this->db, "hr_system");
            // Preparar y ejecutar la consulta para obtener el hash de la contraseña y otros datos
            $sql = "SELECT id, name, user_type, password FROM users WHERE name = ?";
            $stmt = mysqli_prepare($this->db, $sql);
    
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 's', $data['usuario']);  // Cambié 'name' por 'usuario'
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
    
                // Verificar la contraseña
                if ($row && password_verify($data['password'], $row['password'])) {
                    // La contraseña es correcta, iniciar sesión
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }
    
                    $_SESSION['id'] = $row['id'];
                    $_SESSION['usuario'] = $row['name'];  // Cambié 'usuario' por 'name'
                    $_SESSION['user_type'] = $row['user_type'];
                    $respuesta = array(
                        'error' => false,
                        'msg' => 'Bienvenido',
                        'sql' => $sql,
                        'registros' => $row,
                        'numero_registros' => 1,
                        'activo' => true
                    );
                } else {
                    // Contraseña incorrecta
                    $respuesta = array(
                        'error' => true,
                        'msg' => 'La contraseña o el usuario están mal',
                        'sql' => $sql,
                        'registros' => $row,
                        'numero_registros' => 0,
                        'activo' => false
                    );
                }
    
                mysqli_stmt_close($stmt);
            } else {
                $respuesta = array(
                    'error' => true,
                    'msg' => 'Error en la consulta: ' . mysqli_error($this->db),
                    'sql' => $sql,
                    'registros' => '',
                    'numero_registros' => 0,
                    'activo' => false
                );
            }
        }
    
        echo json_encode($respuesta);
    }
    
    public function validarCuenta($data)
    {
        // Validación del nombre de usuario: asegurar que esté correctamente formateado (puedes agregar más validaciones si es necesario)
        if (empty($data['usuario'])) {
            return array(
                'error' => true,
                'msg' => 'El nombre de usuario no puede estar vacío',
                'sql' => '',
                'registros' => '',
                'numero_registros' => 0
            );
        }
        $usuario = trim($data['usuario']);  // Usar 'usuario' en lugar de 'name' aquí también
        mysqli_select_db($this->db, "hr_system");
        // Preparar la consulta SQL para verificar si el usuario existe
        $sql = "SELECT COUNT(id) as total FROM users WHERE name = ?"; 
        $stmt = mysqli_prepare($this->db, $sql);
    
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $usuario);
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