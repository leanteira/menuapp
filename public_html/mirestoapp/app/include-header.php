<?php

require_once __DIR__ . '/mr_auth.php';

mr_require_auth();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$conn = mr_db();
$currentUser = mr_user();

$rol = $_SESSION['rol'] ?? ($currentUser['rol'] ?? 'operador');
$nombre = $_SESSION['nombre'] ?? ($currentUser['nombre'] ?? '');
$apellido = $_SESSION['apellido'] ?? '';
$id_usuario = $_SESSION['id'] ?? ($currentUser['id'] ?? 0);

$_SESSION['rol'] = $rol;
$_SESSION['nombre'] = $nombre;
$_SESSION['apellido'] = $apellido;
$_SESSION['id'] = $id_usuario;
$_SESSION['email'] = $_SESSION['email'] ?? ($currentUser['email'] ?? '');

?>
<!doctype html>
<html
    lang="es"
    class="light-style layout-navbar-fixed layout-menu-fixed layout-compact"
    dir="ltr"
    data-theme="theme-semi-dark"
    data-assets-path="assets/"
    data-template="vertical-menu-template-no-customizer"
    data-style="light">

<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>MiRestoApp - Panel</title>

    <meta name="csrf_token" content="<?php echo $_SESSION['csrf_token']; ?>" />
    <meta name="description" content="" />

    <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.png" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="assets/vendor/fonts/remixicon/remixicon.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="assets/vendor/fonts/flag-icons.css?v=<?php echo time(); ?>" />

    <link rel="stylesheet" href="assets/vendor/libs/node-waves/node-waves.css?v=<?php echo time(); ?>" />

    <link rel="stylesheet" href="assets/vendor/css/rtl/core.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="assets/vendor/css/rtl/theme-semi-dark.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="assets/css/demo.css?v=<?php echo time(); ?>" />

    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="assets/vendor/libs/typeahead-js/typeahead.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="assets/vendor/libs/apex-charts/apex-charts.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="assets/vendor/libs/swiper/swiper.css?v=<?php echo time(); ?>" />

    <link rel="stylesheet" href="assets/vendor/libs/bs-stepper/bs-stepper.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="assets/vendor/libs/bootstrap-select/bootstrap-select.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="assets/vendor/libs/select2/select2.css?v=<?php echo time(); ?>" />

    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">

    <link rel="stylesheet" href="assets/vendor/css/pages/cards-statistics.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="assets/vendor/css/pages/cards-analytics.css?v=<?php echo time(); ?>" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="assets/vendor/js/helpers.js"></script>
    <script src="assets/js/config.js"></script>

    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <link rel="stylesheet" href="assets/vendor/libs/flatpickr/flatpickr.css?v=<?php echo time(); ?>">

    <style>
        :root {
            --mr-green-1: #2f8a3b;
            --mr-green-2: #246d2f;
            --mr-green-3: #4dae5c;
            --mr-bg-1: #f3f7ee;
            --mr-bg-2: #e8f5e4;
            --mr-line: #dce8d9;
        }

        body,
        .content-wrapper {
            background: linear-gradient(180deg, var(--mr-bg-1) 0%, #f7faf4 52%, var(--mr-bg-2) 100%) !important;
        }

        .card {
            border-color: var(--mr-line) !important;
            box-shadow: 0 8px 20px rgba(39, 79, 39, 0.08) !important;
        }

        .bg-navbar-theme,
        #layout-navbar {
            background: #ffffff !important;
            border-bottom: 1px solid var(--mr-line) !important;
            box-shadow: 0 6px 16px rgba(39, 79, 39, 0.06) !important;
        }

        .btn-primary,
        .btn.btn-primary {
            border-color: var(--mr-green-1) !important;
            background: linear-gradient(135deg, var(--mr-green-1) 0%, #3f9c4c 58%, var(--mr-green-2) 100%) !important;
            box-shadow: 0 8px 16px rgba(47, 138, 59, 0.2) !important;
        }

        .btn-primary:hover,
        .btn-primary:focus,
        .btn.btn-primary:hover,
        .btn.btn-primary:focus {
            border-color: var(--mr-green-2) !important;
            filter: brightness(1.02);
        }

        .btn-outline-primary,
        .btn.btn-outline-primary {
            color: var(--mr-green-1) !important;
            border-color: #91c89a !important;
            background: #f9fdf8 !important;
        }

        .btn-outline-primary:hover,
        .btn-outline-primary:focus,
        .btn.btn-outline-primary:hover,
        .btn.btn-outline-primary:focus {
            color: #fff !important;
            border-color: var(--mr-green-1) !important;
            background: var(--mr-green-1) !important;
        }

        .text-primary {
            color: var(--mr-green-1) !important;
        }

        .bg-primary {
            background-color: var(--mr-green-1) !important;
        }

        .bg-label-primary {
            background-color: #e7f5e8 !important;
            color: #2c6f33 !important;
        }

        .table thead th {
            background: #ecf5e8 !important;
            color: #315a31 !important;
        }

        .form-control:focus,
        .form-select:focus,
        textarea.form-control:focus {
            border-color: #8fc68a !important;
            box-shadow: 0 0 0 .2rem rgba(47, 138, 59, .18) !important;
        }

        a,
        .nav-link,
        .dropdown-item {
            --bs-link-color-rgb: 47, 138, 59;
        }

        .layout-menu-fixed .layout-menu,
        .layout-menu-fixed-offcanvas .layout-menu {
            background: linear-gradient(180deg, #2f8a3b 0%, #2a7f36 42%, #236b2f 100%) !important;
            border-right: 1px solid #2c6f33;
        }

        #layout-menu .app-brand {
            background: rgba(255, 255, 255, 0.08);
            border-bottom: 1px solid rgba(255, 255, 255, 0.16);
        }

        #layout-menu .menu-link,
        #layout-menu .menu-text,
        #layout-menu .menu-icon {
            color: rgba(242, 247, 255, 0.9) !important;
        }

        #layout-menu .menu-item>.menu-link {
            border-radius: 10px;
            margin: 2px 10px;
            transition: all .2s ease;
        }

        #layout-menu .menu-item>.menu-link:hover {
            background: rgba(255, 255, 255, 0.14) !important;
            color: #ffffff !important;
        }

        #layout-menu .menu-sub .menu-link {
            color: rgba(230, 239, 255, 0.9) !important;
        }

        #layout-menu .menu-sub .menu-link:hover {
            background: rgba(255, 255, 255, 0.12) !important;
            color: #ffffff !important;
        }

        #layout-menu .menu-item.active>.menu-link,
        #layout-menu .menu-item.open>.menu-link {
            color: #fff !important;
            background: linear-gradient(135deg, #66b874 0%, #4ea75c 55%, #3f924d 100%) !important;
            box-shadow: 0 10px 22px rgba(24, 71, 30, 0.32);
        }

        #layout-menu .menu-item.active>.menu-link .menu-icon,
        #layout-menu .menu-item.open>.menu-link .menu-icon {
            color: #fff !important;
        }

        #layout-menu .menu-inner>.menu-item.open>.menu-sub {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            margin: 4px 10px 8px;
            padding: 6px;
        }
    </style>
</head>