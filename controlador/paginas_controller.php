<?php
class paginas_controller
{
    function __construct() {
        // Verificar si la sesión está activa
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    function login()
    {
        // Si la sesión está activa, redirigir al inicio
        if (isset($_SESSION['usuario'])) {
            header('Location: index.php?c=paginas&m=inicio');
            exit();
        }

        include_once('vistas/header.php');
        include_once('vistas/login.php');
    }

    function registro()
    {
        // Si la sesión está activa, redirigir al inicio
        if (isset($_SESSION['usuario'])) {
            header('Location: index.php?c=paginas&m=inicio');
            exit();
        }

        include_once('vistas/header.php');
        include_once('vistas/registro.php');
    }

    function inicio()
    {
        // Verificar si la sesión no está activa, redirigir al login
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?c=paginas&m=login');
            exit();
        }

        include_once('vistas/logeado/header_inicio.php');
        include_once('vistas/logeado/index.php');
        include_once('vistas/logeado/footer.php');
    }

    function gestion_usuarios()
    {
        // Verificar si la sesión no está activa, redirigir al login
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?c=paginas&m=login');
            exit();
        }

        include_once('vistas/logeado/header_inicio.php');
        include_once('vistas/logeado/gestion_usuarios.php');
        include_once('vistas/logeado/footer.php');
    }

    function solicitud_vacaciones()
    {
        // Verificar si la sesión no está activa, redirigir al login
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?c=paginas&m=login');
            exit();
        }

        include_once('vistas/logeado/header_inicio.php');
        include_once('vistas/logeado/solicitud_vacaciones.php');
        include_once('vistas/logeado/footer.php');
    }

    function gestion_empleados()
    {
        // Verificar si la sesión no está activa, redirigir al login
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?c=paginas&m=login');
            exit();
        }

        include_once('vistas/logeado/header_inicio.php');
        include_once('vistas/logeado/gestion_empleados.php');
        include_once('vistas/logeado/footer.php');
    }

    function logout()
    {
        // Destruir la sesión y redirigir al login
        session_destroy();
        header('Location: index.php?c=paginas&m=login');
        exit();
    }
}
?>