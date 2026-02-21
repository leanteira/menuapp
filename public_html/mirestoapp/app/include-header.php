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
            background: linear-gradient(180deg, #f8f9fb 0%, #f1f3f8 100%);
        }

        #layout-menu .menu-link,
        #layout-menu .menu-text,
        #layout-menu .menu-icon {
            color: #344054 !important;
        }

        #layout-menu .menu-item.active>.menu-link,
        #layout-menu .menu-item.open>.menu-link {
            color: #fff !important;
        }
    </style>
</head>
