<?php
require_once('base_de_datos/conexion.php');
require_once('controlador/paginas_controller.php');
require_once('controlador/login_controller.php');
require_once('controlador/registro_controller.php');
require_once('controlador/usuarios_controller.php');

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
        default:
            $controller = new paginas_controller();
            $controller->error404();
            exit;
    }

    // Identificar el método solicitado
    $metodo = $_REQUEST['m'];
    if (method_exists($controller, $metodo)) {
        $controller->$metodo();
    } else {
        // Llamar al error 404 si el método no existe
        $controller = new paginas_controller();
        $controller->error404();
    }
} else {
    // Cargar la página principal por defecto
    $controller = new paginas_controller();
    $controller->home();
}
