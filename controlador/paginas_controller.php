<?php
class paginas_controller
{
    function __construct() {
        // Verificar si la sesión está activa
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    function home()
    {
        // Si la sesión está activa, redirigir al inicio
        if (isset($_SESSION['id'])) {
            header('Location: index.php?c=paginas&m=inicio');
            exit();
        }
        
        include_once('vistas/header.php');
        include_once('vistas/login.php');
    }

    function irRegistro()
    {
        if (isset($_SESSION['id'])) {
            header('Location: index.php?c=paginas&m=home');
            exit();
        }
        include_once('vistas/header.php');
        include_once('vistas/registro.php');
    }

    function registro()
    {
        // Si la sesión no esta activa, mandar a login
        if (!isset($_SESSION['id'])) {
            header('Location: index.php?c=paginas&m=home');
            exit();
        }
        include_once('vistas/loged/header_loged.php');
        include_once('vistas/registro.php');
    }

    function inicio()
    {
        // Verificar si la sesión no está activa, redirigir al login
        if (!isset($_SESSION['id'])) {
            header('Location: index.php?c=paginas&m=home');
            exit();
        }

        include_once('vistas/loged/header_loged.php');
        include_once('vistas/loged/dashboard.php');
    }
    function usuarios()
    {
        // Verificar si la sesión no está activa, redirigir al login
        if (!isset($_SESSION['id'])) {
            header('Location: index.php?c=paginas&m=home');
            exit();
        }

        include_once('vistas/loged/users_table.php');
    }

    function logout()
    {
        // Destruir la sesión y redirigir al login
        session_destroy();
        header('Location: index.php?c=paginas&m=home');
        exit();
    }
}
?>
