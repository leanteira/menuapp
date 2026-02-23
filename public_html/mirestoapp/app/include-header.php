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
        .layout-menu-fixed .layout-menu,
        .layout-menu-fixed-offcanvas .layout-menu {
            background: linear-gradient(180deg, #2c4db8 0%, #2243aa 42%, #17358f 100%) !important;
            border-right: 1px solid #1d3f9f;
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
            background: linear-gradient(135deg, #7ea3ff 0%, #5f84ff 55%, #4a6df3 100%) !important;
            box-shadow: 0 10px 22px rgba(8, 24, 71, 0.42);
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