<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Gestor de recursos humanos RECAM CORTELASER">
  <meta name="author" content="Juan Camilo - Nataniel">
  <title>Gestor Recursos Humanos</title>
  <link rel="icon" type="image/x-icon" href="assets/img/logo_ico.ico">
  <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/navbars/">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
  <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">

  <link rel="stylesheet" type="text/css" href="assets/easyui/themes/default/easyui.css">
  <link rel="stylesheet" type="text/css" href="assets/easyui/themes/icon.css">
  <link rel="stylesheet" type="text/css" href="assets/easyui/themes/color.css">
  <script type="text/javascript" src="assets/easyui/js/jquery.min.js"></script>
  <script type="text/javascript" src="assets/easyui/js/jquery.easyui.min.js"></script>
  <script type="text/javascript" src="assets/easyui/filter/datagrid-filter.js"></script>
  <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

  <style>
    .bd-placeholder-img {
      font-size: 1.125rem;
      text-anchor: middle;
      -webkit-user-select: none;
      -moz-user-select: none;
      user-select: none;
    }

    @media (min-width: 768px) {
      .bd-placeholder-img-lg {
        font-size: 3.5rem;
      }
    }

    .b-example-divider {
      width: 100%;
      height: 3rem;
      background-color: rgba(0, 0, 0, .1);
      border: solid rgba(0, 0, 0, .15);
      border-width: 1px 0;
      box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
    }

    .b-example-vr {
      flex-shrink: 0;
      width: 1.5rem;
      height: 100vh;
    }

    .bi {
      vertical-align: -.125em;
      fill: currentColor;
    }

    .nav-scroller {
      position: relative;
      z-index: 2;
      height: 2.75rem;
      overflow-y: hidden;
    }

    .nav-scroller .nav {
      display: flex;
      flex-wrap: nowrap;
      padding-bottom: 1rem;
      margin-top: -1px;
      overflow-x: auto;
      text-align: center;
      white-space: nowrap;
      -webkit-overflow-scrolling: touch;
    }

    .btn-bd-primary {
      --bd-violet-bg: #712cf9;
      --bd-violet-rgb: 112.520718, 44.062154, 249.437846;

      --bs-btn-font-weight: 600;
      --bs-btn-color: var(--bs-white);
      --bs-btn-bg: var(--bd-violet-bg);
      --bs-btn-border-color: var(--bd-violet-bg);
      --bs-btn-hover-color: var(--bs-white);
      --bs-btn-hover-bg: #6528e0;
      --bs-btn-hover-border-color: #6528e0;
      --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
      --bs-btn-active-color: var(--bs-btn-hover-color);
      --bs-btn-active-bg: #5a23c8;
      --bs-btn-active-border-color: #5a23c8;
    }

    .bd-mode-toggle {
      z-index: 1500;
    }

    .bd-mode-toggle .dropdown-menu .active .bi {
      display: block !important;
    }

    .navbar-brand img {
      max-height: 40px;
      width: auto;
    }

    .navbar-nav {
      flex-direction: row;
    }

    .navbar-toggler-icon {
      background-color: white;
    }

    .navbar-dark .navbar-nav .nav-link {
      color: white !important;
    }

    .navbar {
      background-color: black !important;
    }
  </style>
</head>

<body>
<nav class="navbar navbar-expand-md navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php?c=paginas&m=inicio">
      <img src="assets/img/logos/recam_logo_white.png" alt="Logo" style="max-height: 40px;">
    </a>

    <!-- Botón solo visible en móviles -->
    <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuLateral" aria-controls="menuLateral">
      <i class="bi bi-list"></i>
    </button>

    <!-- Menú horizontal desde md en adelante -->
    <div class="collapse navbar-collapse d-none d-md-flex" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="index.php?c=paginas&m=inicio">Inicio</a>
        </li>

        <?php if ($_SESSION['user_type'] <= 3): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Gestionar Registros</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="index.php?c=paginas&m=gestion_empleados">Empleados</a></li>
            <li><a class="dropdown-item" href="index.php?c=paginas&m=gestion_cambios">Cambios de Empresa</a></li>
            <?php if ($_SESSION['user_type'] == 1): ?>
              <li><a class="dropdown-item" href="index.php?c=paginas&m=gestion_usuarios">Usuarios</a></li>
            <?php endif; ?>
          </ul>
        </li>
        <?php endif; ?>
        <li class="nav-item">
          <a class="nav-link" href="index.php?c=paginas&m=solicitud_vacaciones">Generar Constancias</a>
        </li>
      </ul>

      <a class="nav-link text-white" href="index.php?c=paginas&m=logout">Cerrar sesión</a>
    </div>

    <!-- Menú lateral solo en móviles -->
    <div class="offcanvas offcanvas-end text-bg-dark d-md-none" tabindex="-1" id="menuLateral" aria-labelledby="menuLateralLabel" style="width: 50%;">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="menuLateralLabel">Menú</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
      </div>
      <div class="offcanvas-body d-flex flex-column">
        <a class="nav-link text-white mb-3" href="index.php?c=paginas&m=inicio">Inicio</a>

        <?php if ($_SESSION['user_type'] <= 3): ?>
          <a class="nav-link text-white mb-3" href="index.php?c=paginas&m=gestion_empleados">Empleados</a>
          <a class="nav-link text-white mb-3" href="index.php?c=paginas&m=gestion_cambios">Cambios de Empresa</a>
        <?php endif; ?>

        <?php if ($_SESSION['user_type'] == 1 || $_SESSION['user_type'] == 2): ?>
          <a class="nav-link text-white mb-3" href="index.php?c=paginas&m=gestion_usuarios">Usuarios</a>
          <a class="nav-link text-white mb-3" href="index.php?c=paginas&m=solicitud_vacaciones">Generar Constancias</a>
        <?php endif; ?>

        <hr class="text-white">
        <a class="nav-link text-white" href="index.php?c=paginas&m=logout">Cerrar sesión</a>
      </div>
    </div>
  </div>
</nav>
