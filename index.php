<?php
require_once('base_de_datos/conexion.php');
require_once('controlador/paginas_controller.php');
require_once('controlador/login_controller.php');
require_once('controlador/registro_controller.php');
require_once('controlador/usuarios_controller.php');
require_once('controlador/empleados_controller.php');
require_once('controlador/vacaciones_controller.php');
require_once('controlador/pdf_controller.php');

if (!empty($_REQUEST['m']) && !empty($_REQUEST['c'])) {
    // Inicializa la variable del controlador
    $controller = null;

    // Identificar el controlador solicitado
    switch ($_REQUEST['c']) {

        case 'paginas':
            $controller = new paginas_controller();
            break;
        case 'login':
            $controller = new login_controller();
            break;
        case 'registro':
            $controller = new registro_controller();
            break;
        case 'usuarios':
            $controller = new usuarios_controller();
            break;
        case 'empleados':
            $controller = new empleados_controller();
        break;
        case 'vacaciones':
            $controller = new vacaciones_controller();
        break;
        case 'pdf':
            $controller = new pdf_controller();
        break;
        default:
        header("HTTP/1.0 404 Not Found");
        echo "Error 404: Página no encontrada";
        exit;
    }

    // Identificar el método solicitado
    $metodo = $_REQUEST['m'];
    if (method_exists($controller, $metodo)) {
        $controller->$metodo();
    } else {
        // Llamar al error 404 si el método no existe
        header("HTTP/1.0 404 Not Found");
        echo "Error 404: Página no encontrada";
    }
} else {
    // Cargar la página principal por defecto
    $controller = new paginas_controller();
    $controller->home();
}
