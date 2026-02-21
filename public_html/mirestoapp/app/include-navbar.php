<?php
// include-navbar.php

// Iniciar la sesión si aún no está iniciada
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Asignar variables locales para el navbar
$rol       = $_SESSION['rol']      ?? 'invitado';
$nombre    = $_SESSION['nombre']   ?? '';
$apellido  = $_SESSION['apellido'] ?? '';
$id_usuario = $_SESSION['id']       ?? 0;

// Si tu sistema maneja clubs y lo necesitas, se loedearía así (sólo como ejemplo):
$id_club   = isset($_SESSION['id_club']) ? $_SESSION['id_club'] : 0;

?>
<nav
  class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
  id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
      <i class="ri-menu-fill ri-22px"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse" style="display:none;">
    <!-- Search u otros elementos que quieras poner -->
    <div class="navbar-nav align-items-center">
      <div class="nav-item navbar-search-wrapper mb-0" style="display: none !important;">
        <a class="nav-item nav-link search-toggler fw-normal px-0" href="javascript:void(0);">
          <i class="ri-search-line ri-22px scaleX-n1-rtl me-3"></i>
          <span class="d-none d-md-inline-block text-muted">Accesos directos</span>
        </a>
      </div>
    </div>
    <!-- /Search -->

    <ul class="navbar-nav flex-row align-items-center ms-auto">

      <!-- Sistema de Notificaciones -->
      <?php include('include-notifications.php'); ?>

      <!-- Ejemplo: si el rol es 'administrador' mostramos "quick links" -->
      <?php if ($rol === 'administrador'): ?>
        <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-1 me-xl-0">
          <a
            class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
            href="javascript:void(0);"
            data-bs-toggle="dropdown"
            data-bs-auto-close="outside"
            aria-expanded="false">
            <i class="ri-star-smile-line ri-22px"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-end py-0">
            <div class="dropdown-menu-header border-bottom py-50">
              <div class="dropdown-header d-flex align-items-center py-2">
                <h6 class="mb-0 me-auto">Atajos</h6>
                <a
                  href="javascript:void(0)"
                  class="btn btn-text-secondary rounded-pill btn-icon dropdown-shortcuts-add text-heading"
                  data-bs-toggle="tooltip"
                  data-bs-placement="top"
                  title="Add shortcuts"><i class="ri-add-line ri-24px"></i></a>
              </div>
            </div>
            <div class="dropdown-shortcuts-list scrollable-container">
              <div class="row row-bordered overflow-visible g-0">
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                    <i class="ri-user-line ri-26px text-heading"></i>
                  </span>
                  <a href="usuario-listado.php" class="stretched-link">Usuarios</a>
                  <small class="mb-0">Listado</small>
                </div>
                <!-- Agrega más atajos si quieres... -->
              </div>
            </div>
          </div>
        </li>
      <?php endif; ?>
      <!-- / Quick links -->

      <!-- Notificaciones -->
      <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-4 me-xl-1" style="display:none;">
        <a
          class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
          href="javascript:void(0);"
          data-bs-toggle="dropdown"
          data-bs-auto-close="outside"
          aria-expanded="false">
          <i class="ri-notification-2-line ri-22px"></i>
          <span
            class="position-absolute top-0 start-50 translate-middle-y badge badge-dot bg-danger mt-2 border"></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end py-0">
          <!-- contenido de notificaciones -->
        </ul>
      </li>
      <!-- /Notificaciones -->

      <!-- User -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            <!-- Si usas un logo de club, ajusta la lógica de $id_club o quítala -->
            <?php
            $logoPathHead = "club/logo/{$id_club}.jpg";
            if (!file_exists($logoPathHead)) {
              $logoPathHead = "assets/img/logos/logo-listas.jpg";
            }
            ?>
            <img src="<?php echo htmlspecialchars($logoPathHead); ?>"
              alt="logo"
              style="max-width: 100px; margin-top: 10px;"
              class="rounded-circle">
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="pages-account-settings-account.html">
              <div class="d-flex">
                <div class="flex-shrink-0 me-2">
                  <div class="avatar avatar-online">
                    <img src="assets/img/avatars/1.png" alt class="rounded-circle" />
                  </div>
                </div>
                <div class="flex-grow-1">
                  <span class="fw-medium d-block small">
                    <?php echo htmlspecialchars($nombre . ' ' . $apellido); ?>
                  </span>
                  <small class="text-muted">
                    <?php echo htmlspecialchars($rol); ?>
                  </small>
                </div>
              </div>
            </a>
          </li>
          <li>
            <div class="dropdown-divider"></div>
          </li>
          <li>
            <div class="d-grid px-4 pt-2 pb-1">
              <a class="btn btn-sm btn-danger d-flex" href="logout.php">
                <small class="align-middle">Salir</small>
                <i class="ri-logout-box-r-line ms-2 ri-16px"></i>
              </a>
            </div>
          </li>
        </ul>
      </li>
      <!--/ User -->
    </ul>
  </div>

  <!-- Search Small Screens -->
  <div class="navbar-search-wrapper search-input-wrapper d-none">
    <input
      type="text"
      class="form-control search-input container-xxl border-0"
      placeholder="Buscar..."
      aria-label="Buscar..." />
    <i class="ri-close-fill search-toggler cursor-pointer"></i>
  </div>
</nav>