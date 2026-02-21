<?php
// include-menu.php

// Incluir la definición del array del menú y las funciones
include('menu.php');
include('menu_functions.php');

// Obtener el rol del usuario desde la sesión (ajusta si tu variable se llama de otra forma)
$user_role = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'paciente';

// Obtener la página actual
$current_page = basename($_SERVER['REQUEST_URI'] ?? '');
if (strpos($current_page, '?') !== false) {
    $current_page = substr($current_page, 0, strpos($current_page, '?'));
}

// Generar el HTML del menú
$menu_html = generate_menu($menu, $user_role, $current_page);

// Determinar el link del logo según el rol
$logo_link = 'index.php';
if ($user_role === 'paciente') {
    $logo_link = 'paciente_dashboard.php';
}
?>

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="<?php echo $logo_link; ?>" class="app-brand-link">
            <span class="app-brand-logo demo">
                <span style="color: var(--bs-primary)">
                    <img src="assets/img/logos/logo-menu-left.png" alt="logo" />
                </span>
            </span>
            <span class="app-brand-text demo menu-text fw-semibold ms-2"></span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <!-- SVG del toggle -->
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <!-- SVG paths -->
            </svg>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <?php echo $menu_html; ?>
    </ul>
</aside>