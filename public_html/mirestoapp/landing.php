<?php
session_start();

$paciente_autenticado = isset($_SESSION['paciente_id']);
$paciente_id = $_SESSION['paciente_id'] ?? null;
$paciente_nombre = $_SESSION['paciente_nombre'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de gestión de turnos médicos. Agenda tus citas en línea de forma rápida y segura.">
    <meta name="keywords" content="turnos médicos, citas médicas, sistema de turnos, agenda médica">
    <meta name="author" content="TurnosMed">
    <title>TurnosMed | Sistema de Gestión de Turnos Médicos</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">



    <!-- jQuery (si lo necesitas para otros plugins) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Responsive DataTables JS -->
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>

    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet" />

    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/js/select2.min.js"></script>

    <!-- Flatpickr CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

    <!-- Toastr CSS (opcional) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">


    <style>
        :root {
            --primary: #e64389;
            --accent-gradient: linear-gradient(135deg, #e64389 0%, #e9bdd0 100%);
            --brand-gradient: linear-gradient(135deg, #e64389 0%, #e9bdd0 100%);
            --bg: #F8FAFC;
            --surface: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --radius-sm: 12px;
            --radius-md: 20px;
            --radius-lg: 32px;
            --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            --shadow-float: 0 20px 40px -10px rgba(46, 148, 159, 0.2);
        }

        * {
            box-sizing: border-box;
            outline: none;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            line-height: 1.6;
            overflow-x: hidden;
        }

        h1,
        h2,
        h3,
        h4,
        .font-display {
            font-family: 'Space Grotesk', sans-serif;
            letter-spacing: -0.02em;
            margin: 0;
        }

        a {
            text-decoration: none;
            color: inherit;
            transition: 0.2s;
        }

        .container {
            max-width: 1140px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .section-py {
            padding: 80px 0;
        }

        /* Navbar */
        .navbar {
            padding: 20px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(248, 250, 252, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.5);
        }

        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            font-family: 'Space Grotesk', sans-serif;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: var(--brand-gradient);
            color: white;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 1.2rem;
        }

        .nav-buttons {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: var(--accent-gradient);
            color: white;
            box-shadow: 0 10px 20px -5px rgba(46, 148, 159, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(46, 148, 159, 0.5);
        }

        .btn-ghost {
            background: white;
            border-color: var(--border);
            color: var(--text-main);
        }

        .btn-ghost:hover {
            border-color: #cbd5e1;
            background: #f1f5f9;
        }

        /* Hero */
        .hero {
            padding: 60px 0 80px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: white;
            border: 1px solid var(--border);
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
        }

        .hero-badge .dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
        }

        .hero-title {
            font-size: clamp(2.5rem, 5vw, 3.8rem);
            line-height: 1.1;
            font-weight: 700;
            margin-bottom: 24px;
            color: var(--text-main);
        }

        .hero-title span {
            background: var(--brand-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            font-size: 1.125rem;
            color: var(--text-muted);
            margin-bottom: 32px;
            max-width: 500px;
        }

        .hero-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .hero-visual {
            position: relative;
            background: white;
            border-radius: var(--radius-lg);
            padding: 20px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-xl);
            overflow: hidden;
            aspect-ratio: 1;
            display: grid;
            place-items: center;
        }

        .hero-visual img {
            border-radius: var(--radius-md);
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Features */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
        }

        .feature-card {
            background: white;
            padding: 32px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-float);
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, rgba(46, 148, 159, 0.1), rgba(32, 104, 125, 0.1));
            border-radius: var(--radius-sm);
            display: grid;
            place-items: center;
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 16px;
        }

        .feature-card h3 {
            font-size: 1.25rem;
            margin-bottom: 12px;
        }

        .feature-card p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        /* Médicos Section */
        .medicos-section {
            background: linear-gradient(135deg, rgba(46, 148, 159, 0.05), rgba(32, 104, 125, 0.05));
            border-radius: var(--radius-lg);
            padding: 60px 40px;
        }

        .section-title {
            font-size: 2.5rem;
            margin-bottom: 12px;
            text-align: center;
        }

        .section-subtitle {
            color: var(--text-muted);
            text-align: center;
            margin-bottom: 48px;
            font-size: 1.1rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .medicos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .medico-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 24px;
            text-align: center;
            border: 1px solid var(--border);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .medico-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-float);
        }

        .medico-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 0 auto 16px;
            object-fit: cover;
            border: 3px solid var(--primary);
            background: #f1f5f9;
        }

        .medico-name {
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 4px;
        }

        .medico-esp {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 16px;
        }

        .btn-citar {
            width: 100%;
            justify-content: center;
        }

        .medicos-footer {
            text-align: center;
        }

        /* Formulario Turno */
        .turno-section {
            background: white;
            border-radius: var(--radius-lg);
            padding: 60px 40px;
            border: 1px solid var(--border);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-main);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            transition: border 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(46, 148, 159, 0.1);
        }

        .form-group textarea {
            grid-column: 1 / -1;
            resize: vertical;
            min-height: 100px;
        }

        .form-buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 24px;
        }

        /* Footer */
        .footer {
            padding: 40px 0;
            border-top: 1px solid var(--border);
            text-align: center;
            color: var(--text-muted);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .nav-buttons {
                gap: 8px;
            }

            .btn {
                padding: 10px 16px;
                font-size: 0.85rem;
            }

            .section-py {
                padding: 60px 0;
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .hidden {
            display: none;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: var(--radius-lg);
            padding: 40px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: var(--shadow-float);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .modal-header h2 {
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-muted);
        }

        /* Búsqueda por criterios */
        .search-tabs {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 30px;
        }

        .tab-btn {
            padding: 12px;
            border: 2px solid var(--border);
            background: white;
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
            color: var(--text-muted);
        }

        .tab-btn.active {
            border-color: var(--primary);
            background: var(--accent-gradient);
            color: white;
        }

        /* Listado de médicos */
        .medicos-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-height: 400px;
            overflow-y: auto;
        }

        .medico-item {
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .medico-item:hover {
            border-color: var(--primary);
            background: #f8fafc;
        }

        .medico-item.selected {
            background: #f0f9fb;
            border-color: var(--primary);
        }

        .medico-item img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .medico-item-info {
            flex: 1;
        }

        .medico-item-name {
            font-weight: 600;
            color: var(--text-main);
        }

        .medico-item-esp {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        /* Slots horarios */
        .slots-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            max-height: 300px;
            overflow-y: auto;
            margin: 20px 0;
        }

        .slot-btn {
            padding: 10px;
            border: 1px solid var(--border);
            background: white;
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
        }

        .slot-btn:hover:not(:disabled) {
            border-color: var(--primary);
            background: #f0f9fb;
        }

        .slot-btn.selected {
            background: var(--accent-gradient);
            color: white;
            border-color: var(--primary);
        }

        .slot-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Resumen turno */
        .turno-resumen {
            background: #f0f9fb;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 20px;
            margin: 20px 0;
        }

        .resumen-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        .resumen-item-label {
            font-weight: 600;
            color: var(--text-main);
        }

        .resumen-item-value {
            color: var(--primary);
            font-weight: 600;
        }

        /* User badge navbar */
        .user-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: #f0f9fb;
            border-radius: 999px;
            font-size: 0.9rem;
        }

        /* Loading */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid var(--border);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Flatpickr Custom Theme */
        .flatpickr-calendar {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-float);
            font-family: 'Inter', sans-serif;
        }

        .flatpickr-months {
            background: var(--accent-gradient);
            border-radius: var(--radius-md) var(--radius-md) 0 0;
        }

        .flatpickr-current-month,
        .flatpickr-monthDropdown-months,
        .flatpickr-prev-month,
        .flatpickr-next-month {
            color: white !important;
            fill: white !important;
        }

        .flatpickr-day {
            color: var(--text-main);
            border-radius: var(--radius-sm);
            font-weight: 500;
        }

        .flatpickr-day:hover:not(.flatpickr-disabled) {
            background: #f0f9fb;
            border-color: var(--primary);
        }

        .flatpickr-day.selected,
        .flatpickr-day.selected:hover {
            background: var(--accent-gradient);
            color: white;
            border-color: var(--primary);
        }

        .flatpickr-day.today {
            border-color: var(--primary);
        }

        .flatpickr-day.flatpickr-disabled {
            color: #cbd5e1;
            cursor: not-allowed;
        }

        .flatpickr-weekday {
            color: var(--text-muted);
            font-weight: 600;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
    <script>
        // Función helper para mostrar mensajes (toastr o alert como fallback)
        function mostrarExito(mensaje) {
            if (typeof toastr !== 'undefined' && toastr.success) {
                toastr.success(mensaje);
            } else {
                alert('✓ ' + mensaje);
            }
        }

        function mostrarError(mensaje) {
            if (typeof toastr !== 'undefined' && toastr.error) {
                toastr.error(mensaje);
            } else {
                alert('✗ Error: ' + mensaje);
            }
        }

        // Configurar toastr si está disponible
        if (typeof toastr !== 'undefined') {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }
        }
    </script>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-inner">
                <a href="/" class="logo">
                    <div class="logo-icon"><i class="ri-calendar-check-line"></i></div>
                    <span>TurnosMed</span>
                </a>
                <div class="nav-buttons">
                    <?php if ($paciente_autenticado): ?>
                        <button onclick="abrirModalBusqueda()" class="btn btn-primary"><i class="ri-calendar-line"></i> Agendar Cita</button>
                        <div class="user-badge">
                            <i class="ri-user-line"></i>
                            <span><?php echo htmlspecialchars($paciente_nombre); ?></span>
                            <a href="app/logout.php" class="btn btn-ghost" style="padding: 4px 12px; margin: 0;">
                                Salir
                            </a>
                        </div>
                    <?php else: ?>
                        <button onclick="abrirModalBusqueda()" class="btn btn-ghost"><i class="ri-calendar-line"></i> Ver Disponibilidad</button>
                        <button onclick="abrirModalBusqueda()" class="btn btn-primary"><i class="ri-calendar-line"></i> Agendar Cita</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero section-py">
        <div class="container">
            <div class="hero-grid">
                <div>
                    <div class="hero-badge">
                        <span class="dot"></span>
                        Disponible 24/7
                    </div>
                    <h1 class="hero-title">
                        Agenda tus <span>citas médicas</span> sin complicaciones
                    </h1>
                    <p class="hero-desc">
                        Sistema moderno de gestión de turnos médicos. Conecta con los mejores profesionales de salud y reserva tu cita en segundos.
                    </p>
                    <div class="hero-buttons">
                        <button onclick="abrirModalBusqueda()" class="btn btn-primary"><i class="ri-calendar-line"></i> Agendar Ahora</button>
                        <a href="#features" class="btn btn-ghost"><i class="ri-information-line"></i> Más Info</a>
                    </div>
                </div>
                <div class="hero-visual">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 400'%3E%3Crect fill='%23f1f5f9' width='400' height='400'/%3E%3Ccircle cx='200' cy='150' r='50' fill='%232e949f' opacity='0.1'/%3E%3Crect x='80' y='250' width='240' height='100' rx='10' fill='%232e949f' opacity='0.05'/%3E%3Ctext x='200' y='210' text-anchor='middle' font-family='Arial' font-size='16' fill='%232e949f'%3EMis Turnos%3C/text%3E%3C/svg%3E" alt="Sistema de Turnos">
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="section-py" id="features">
        <div class="container">
            <h2 class="section-title" style="margin-bottom: 48px;">¿Por qué elegir TurnosMed?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="ri-speed-line"></i></div>
                    <h3>Rápido y Fácil</h3>
                    <p>Agenda tu cita en menos de 2 minutos sin necesidad de llamar.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="ri-shield-check-line"></i></div>
                    <h3>Seguro</h3>
                    <p>Tus datos médicos están protegidos con encriptación de nivel empresarial.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="ri-time-line"></i></div>
                    <h3>Disponibilidad Real</h3>
                    <p>Ve solo los turnos disponibles actualizados en tiempo real.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="ri-notification-line"></i></div>
                    <h3>Recordatorios</h3>
                    <p>Recibe notificaciones sobre tus citas próximas automáticamente.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="ri-user-heart-line"></i></div>
                    <h3>Profesionales Verificados</h3>
                    <p>Todos nuestros médicos están certificados y verificados.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="ri-smartphone-line"></i></div>
                    <h3>App Móvil</h3>
                    <p>Accede desde cualquier dispositivo en cualquier momento.</p>
                </div>
    </section>

    <!-- Modales -->
    <!-- Modal Login/Registro -->
    <div id="modalLogin" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Agendar Cita</h2>
                <button class="modal-close" onclick="cerrarModalLogin()">✕</button>
            </div>

            <div id="loginForm">
                <p style="color: var(--text-muted); margin-bottom: 20px;">Ingresa tu email o DNI para comenzar</p>
                <form onsubmit="loginPaciente(event)">
                    <div class="form-group">
                        <label>Email o DNI</label>
                        <input type="text" id="identificador" required placeholder="tu@email.com o 12345678">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 20px;">
                        <i class="ri-search-line"></i> Buscar
                    </button>
                </form>
                <div style="text-align: center; margin-top: 15px;">
                    <a href="app/login-forgot.php" target="_blank" style="color: var(--primary); font-size: 0.9rem; text-decoration: underline;">¿Olvidaste tu contraseña?</a>
                </div>
            </div>

            <div id="registroForm" class="hidden">
                <p style="color: var(--text-muted); margin-bottom: 20px;">Completa tus datos para registrarte</p>
                <form onsubmit="registrarPaciente(event)">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" id="reg_nombre" required placeholder="Tu nombre">
                    </div>
                    <div class="form-group">
                        <label>Apellido</label>
                        <input type="text" id="reg_apellido" required placeholder="Tu apellido">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="reg_email" required placeholder="tu@email.com">
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="tel" id="reg_telefono" placeholder="+54 9 XXX XXX XXXX">
                    </div>
                    <div class="form-group">
                        <label>DNI</label>
                        <input type="text" id="reg_documento" placeholder="12345678">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 20px;">
                        <i class="ri-check-line"></i> Registrarse
                    </button>
                </form>
                <div style="text-align: center; margin-top: 15px;">
                    <p style="color: var(--text-muted); font-size: 0.9rem;">¿Ya tienes cuenta? <a href="#" onclick="mostrarLogin(); return false;" style="color: var(--primary); text-decoration: underline;">Volver a login</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Búsqueda de Turnos -->
    <div id="modalBusqueda" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h2>Buscar Turno</h2>
                <button class="modal-close" onclick="cerrarModalBusqueda()">✕</button>
            </div>

            <div class="search-tabs">
                <button class="tab-btn active" onclick="cambiarTab('especialidad')">
                    <i class="ri-stethoscope-line"></i> Especialidad
                </button>
                <button class="tab-btn" onclick="cambiarTab('clinica')">
                    <i class="ri-building-line"></i> Clínica
                </button>
                <button class="tab-btn" onclick="cambiarTab('medico')">
                    <i class="ri-user-doctor-line"></i> Médico
                </button>
            </div>

            <div id="busquedaEspecialidad" class="tab-content">
                <div class="form-group">
                    <label>Selecciona Especialidad</label>
                    <select id="select-especialidad" class="select2" onchange="buscarMedicosPorEspecialidad()">
                        <option value="">-- Cargando --</option>
                    </select>
                </div>
                <div id="medicosPorEspecialidad" class="medicos-list"></div>
            </div>

            <div id="busquedaClinica" class="tab-content hidden">
                <div class="form-group">
                    <label>Selecciona Clínica</label>
                    <select id="select-clinica" onchange="buscarMedicosPorClinica()">
                        <option value="">-- Cargando --</option>
                    </select>
                </div>
                <div id="medicosPorClinica" class="medicos-list"></div>
            </div>

            <div id="busquedaMedico" class="tab-content hidden">
                <div class="form-group">
                    <label>Busca por Nombre</label>
                    <input type="text" id="input-busqueda-medico" placeholder="Dr. Pérez..." onkeyup="buscarMedicosPorNombre()">
                </div>
                <div id="medicosPorNombre" class="medicos-list"></div>
            </div>
        </div>
    </div>

    <!-- Modal Tratamientos -->
    <div id="modalTratamientos" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h2>Selecciona Tratamiento</h2>
                <button class="modal-close" onclick="cerrarModalTratamientos()">✕</button>
            </div>

            <p style="color: var(--text-muted); margin-bottom: 20px;">Elige el tratamiento que deseas realizar</p>

            <div id="tratamientosList" class="medicos-list"></div>

            <div style="display: flex; gap: 12px; margin-top: 20px;">
                <button onclick="volverAModalBusquedaDesdeTratamientos()" class="btn btn-ghost" style="flex: 1; justify-content: center;">
                    Volver
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Disponibilidad -->
    <div id="modalDisponibilidad" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h2>Selecciona Fecha y Hora</h2>
                <button class="modal-close" onclick="cerrarModalDisponibilidad()">✕</button>
            </div>

            <div class="form-group">
                <label>Fecha</label>
                <input type="text" id="select-fecha" placeholder="Selecciona una fecha" readonly>
            </div>

            <div id="slotsContainer" style="display: none;">
                <p style="color: var(--text-muted); margin-bottom: 15px;">Horarios disponibles</p>
                <div class="slots-grid" id="slotsGrid"></div>
            </div>

            <div class="turno-resumen" id="resumenTurno" style="display: none;">
                <div class="resumen-item">
                    <span class="resumen-item-label">Médico:</span>
                    <span class="resumen-item-value" id="resumen-medico"></span>
                </div>
                <div class="resumen-item">
                    <span class="resumen-item-label">Fecha:</span>
                    <span class="resumen-item-value" id="resumen-fecha"></span>
                </div>
                <div class="resumen-item">
                    <span class="resumen-item-label">Hora:</span>
                    <span class="resumen-item-value" id="resumen-hora"></span>
                </div>
            </div>

            <div class="form-group">
                <label>Observaciones (opcional)</label>
                <textarea id="observaciones-turno" placeholder="Cuéntanos qué te trae a la consulta..."></textarea>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 20px;">
                <button onclick="volverAModalBusqueda()" class="btn btn-ghost" style="flex: 1; justify-content: center;">
                    Volver
                </button>
                <button onclick="confirmarTurno()" class="btn btn-primary" style="flex: 1; justify-content: center;">
                    <i class="ri-calendar-check-line"></i> Confirmar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Datos Básicos (para usuario no autenticado) -->
    <div id="modalDatosBasicos" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h2>Confirma tu Identidad</h2>
                <button class="modal-close" onclick="cerrarModalDatosBasicos()">✕</button>
            </div>

            <!-- Paso 1: Login/Validación -->
            <div id="paso1Login" style="display: none;">
                <p style="color: var(--text-muted); margin-bottom: 20px;">¿Ya eres paciente? Ingresa tus datos</p>
                <form id="formValidarPaciente" onsubmit="validarPaciente(event)">
                    <div class="form-group">
                        <label>Email o DNI</label>
                        <input type="text" id="identificador_login" name="identificador" required placeholder="tu@email.com o 12345678">
                    </div>
                    <div class="form-group">
                        <label>Contraseña</label>
                        <input type="password" id="contrasena_login" name="contrasena" required placeholder="Tu contraseña">
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 20px;">
                        <button type="button" onclick="cerrarModalDatosBasicos()" class="btn btn-ghost" style="flex: 1; justify-content: center;">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">
                            <i class="ri-login-line"></i> Ingresar
                        </button>
                    </div>
                    <p style="text-align: center; color: var(--text-muted); margin-top: 15px; font-size: 0.9rem;">
                        ¿No tienes cuenta? <a href="#" onclick="mostrarPaso2Registro()" style="color: var(--primary); text-decoration: underline;">Regístrate aquí</a>
                    </p>
                    <p style="text-align: center; margin-top: 8px;">
                        <a href="app/login-forgot.php" target="_blank" style="color: var(--primary); font-size: 0.9rem; text-decoration: underline;">¿Olvidaste tu contraseña?</a>
                    </p>
                </form>
            </div>

            <!-- Paso 2: Registro -->
            <div id="paso2Registro" style="display: none;">
                <p style="color: var(--text-muted); margin-bottom: 20px;">Crea tu cuenta para agendar el turno</p>
                <form id="formRegistroPaciente" onsubmit="registrarYConfirmarTurno(event)">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" id="nombre_registro" name="nombre" required placeholder="Tu nombre">
                    </div>
                    <div class="form-group">
                        <label>Apellido</label>
                        <input type="text" id="apellido_registro" name="apellido" required placeholder="Tu apellido">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="email_registro" name="email" required placeholder="tu@email.com">
                    </div>
                    <div class="form-group">
                        <label>DNI</label>
                        <input type="text" id="dni_registro" name="documento" required placeholder="12345678">
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="tel" id="telefono_registro" name="telefono" placeholder="+54 9 XXXXX">
                    </div>
                    <div class="form-group">
                        <label>Contraseña</label>
                        <input type="password" id="contrasena_registro" name="contrasena" required placeholder="Tu contraseña">
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 20px;">
                        <button type="button" onclick="mostrarPaso1Login()" class="btn btn-ghost" style="flex: 1; justify-content: center;">
                            ← Volver
                        </button>
                        <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">
                            <i class="ri-user-add-line"></i> Registrarse
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer section-py">
        <div class="container">
            <p>&copy; 2026 TurnosMed. Todos los derechos reservados.</p>
            <p style="font-size: 0.9rem; margin-top: 12px;">Gestión de turnos médicos - Hecho con ❤️</p>
        </div>
    </footer>

    <script>
        // Variables globales
        let medicoSeleccionado = null;
        let clinicaSeleccionada = null;
        let tratamientoSeleccionado = null;
        let horarioSeleccionado = null;
        const pacienteAutenticado = <?php echo $paciente_autenticado ? 'true' : 'false'; ?>;

        document.addEventListener('DOMContentLoaded', () => {
            if (window.$ && $('#select-especialidad').length) {
                $('#select-especialidad').select2({
                    dropdownParent: $('#modalBusqueda'),
                    width: '100%',
                    placeholder: '-- Selecciona especialidad --'
                });
            }
        });

        // --- MODALES ---
        function abrirModalLogin() {
            if (pacienteAutenticado) {
                abrirModalBusqueda();
            } else {
                document.getElementById('modalLogin').classList.add('active');
            }
        }

        function cerrarModalLogin() {
            document.getElementById('modalLogin').classList.remove('active');
        }

        function mostrarLogin() {
            document.getElementById('loginForm').classList.remove('hidden');
            document.getElementById('registroForm').classList.add('hidden');
            // Limpiar los campos del formulario de registro
            document.getElementById('reg_nombre').value = '';
            document.getElementById('reg_apellido').value = '';
            document.getElementById('reg_email').value = '';
            document.getElementById('reg_telefono').value = '';
            document.getElementById('reg_documento').value = '';
            document.getElementById('identificador').value = '';
            document.getElementById('identificador').focus();
        }

        function abrirModalBusqueda() {
            console.log('Abriendo modal de búsqueda');
            document.getElementById('modalBusqueda').classList.add('active');
            cargarEspecialidades();
            cargarClinicas();
        }

        function cerrarModalBusqueda() {
            document.getElementById('modalBusqueda').classList.remove('active');
            // NO resetear medicoSeleccionado aquí porque se usa después
            // medicoSeleccionado = null;
            // clinicaSeleccionada = null;
        }

        let flatpickrInstance = null;

        function abrirModalDisponibilidad() {
            console.log('=== INICIANDO abrirModalDisponibilidad ===');
            console.log('medicoSeleccionado:', medicoSeleccionado);

            if (!medicoSeleccionado) {
                mostrarError('Selecciona un médico primero');
                return;
            }

            try {
                console.log('Agregando clase active al modal...');
                const modal = document.getElementById('modalDisponibilidad');
                if (!modal) {
                    console.error('Modal de disponibilidad NO encontrado');
                    return;
                }
                modal.classList.add('active');
                console.log('Modal abierto correctamente');

                const inputFecha = document.getElementById('select-fecha');
                if (!inputFecha) {
                    console.error('Input de fecha NO encontrado');
                    return;
                }

                // Cargar días disponibles y configurar Flatpickr
                cargarDiasDisponiblesYConfigurar();
            } catch (error) {
                console.error('Error en abrirModalDisponibilidad:', error);
                mostrarError('Error al abrir disponibilidad: ' + error.message);
            }
        }

        async function cargarDiasDisponiblesYConfigurar() {
            try {
                const url = `app/api_dias_disponibles.php?medico_id=${medicoSeleccionado.id}&clinica_id=${clinicaSeleccionada || 1}`;
                const res = await fetch(url);
                const data = await res.json();

                if (!data.success) {
                    mostrarError(data.error || 'Error al cargar días disponibles');
                    return;
                }

                const diasDisponibles = data.dias_disponibles || [];

                // Destruir instancia anterior si existe
                if (flatpickrInstance) {
                    flatpickrInstance.destroy();
                }

                // Configurar Flatpickr
                const mañana = new Date();
                mañana.setDate(mañana.getDate() + 1);

                flatpickrInstance = flatpickr('#select-fecha', {
                    locale: 'es',
                    minDate: mañana,
                    maxDate: new Date().fp_incr(60), // 60 días adelante
                    enable: diasDisponibles.length > 0 ? diasDisponibles : [mañana],
                    dateFormat: 'Y-m-d',
                    defaultDate: diasDisponibles.length > 0 ? diasDisponibles[0] : mañana,
                    onChange: function(selectedDates, dateStr, instance) {
                        console.log('Fecha seleccionada:', dateStr);
                        cargarDisponibilidad();
                    },
                    onReady: function(selectedDates, dateStr, instance) {
                        console.log('Flatpickr listo, fecha inicial:', dateStr);
                        // Cargar disponibilidad para la fecha por defecto
                        setTimeout(() => cargarDisponibilidad(), 300);
                    }
                });

            } catch (error) {
                console.error('Error al cargar días disponibles:', error);
                mostrarError('Error al cargar calendario');
            }
        }

        function cerrarModalDisponibilidad() {
            document.getElementById('modalDisponibilidad').classList.remove('active');
            horarioSeleccionado = null;
            // Destruir instancia de Flatpickr
            if (flatpickrInstance) {
                flatpickrInstance.destroy();
                flatpickrInstance = null;
            }
            // Resetear selección al cerrar disponibilidad
            medicoSeleccionado = null;
            clinicaSeleccionada = null;
        }

        function volverAModalBusqueda() {
            document.getElementById('modalDisponibilidad').classList.remove('active');
            document.getElementById('modalBusqueda').classList.add('active');
        }

        function cerrarModalTratamientos() {
            document.getElementById('modalTratamientos').classList.remove('active');
            tratamientoSeleccionado = null;
        }

        function volverAModalBusquedaDesdeTratamientos() {
            cerrarModalTratamientos();
            document.getElementById('modalBusqueda').classList.add('active');
        }

        async function cargarTratamientos() {
            if (!medicoSeleccionado) {
                mostrarError('No hay médico seleccionado');
                return;
            }

            try {
                const res = await fetch(`app/api_tratamientos.php?medico_id=${medicoSeleccionado.id}`);
                const data = await res.json();

                if (!data.success) {
                    mostrarError(data.error || 'Error al cargar tratamientos');
                    return;
                }

                const container = document.getElementById('tratamientosList');

                if (!data.tratamientos || data.tratamientos.length === 0) {
                    container.innerHTML = '<p style="color: var(--text-muted); text-align: center; padding: 20px;">Este médico no tiene tratamientos configurados. Procediendo sin tratamiento...</p>';
                    // Si no hay tratamientos, ir directo a disponibilidad
                    setTimeout(() => {
                        cerrarModalTratamientos();
                        abrirModalDisponibilidad();
                    }, 1500);
                    return;
                }

                container.innerHTML = data.tratamientos.map(t => {
                    const precioHtml = t.precio ? `<div style="color: var(--primary); font-weight: 600; margin-top: 4px;">$${t.precio}</div>` : '';
                    const duracionHtml = t.duracion ? `<div style="font-size: 0.85rem; color: var(--text-muted);">⏱ ${t.duracion} min</div>` : '';
                    const descripcionHtml = t.descripcion ? `<div style="font-size: 0.9rem; color: var(--text-muted); margin-top: 4px;">${t.descripcion}</div>` : '';

                    return `
                        <div class="medico-item" 
                             data-id="${t.id}"
                             data-nombre="${t.nombre.replace(/"/g, '&quot;')}"
                             data-duracion="${t.duracion || 30}"
                             onclick="seleccionarTratamiento(this)">
                            <div style="flex: 1;">
                                <div class="medico-item-name">${t.nombre}</div>
                                ${descripcionHtml}
                                ${duracionHtml}
                                ${precioHtml}
                            </div>
                        </div>
                    `;
                }).join('');

                document.getElementById('modalTratamientos').classList.add('active');
            } catch (err) {
                console.error('Error al cargar tratamientos:', err);
                mostrarError('Error al cargar tratamientos');
            }
        }

        function seleccionarTratamiento(elemento) {
            tratamientoSeleccionado = {
                id: elemento.dataset.id,
                nombre: elemento.dataset.nombre,
                duracion: parseInt(elemento.dataset.duracion) || 30
            };

            console.log('Tratamiento seleccionado:', tratamientoSeleccionado);

            document.querySelectorAll('#tratamientosList .medico-item').forEach(el => el.classList.remove('selected'));
            elemento.classList.add('selected');

            // Ir a disponibilidad
            cerrarModalTratamientos();
            setTimeout(() => {
                abrirModalDisponibilidad();
            }, 300);
        }

        // --- LOGIN/REGISTRO ---
        async function loginPaciente(e) {
            e.preventDefault();
            const identificador = document.getElementById('identificador').value;
            const btn = e.target.querySelector('button[type="submit"]');
            if (!btn) {
                console.error('Botón no encontrado');
                return;
            }
            btn.disabled = true;

            try {
                const formData = new FormData();
                formData.append('identificador', identificador);

                const res = await fetch('app/api_paciente_login.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await res.json();
                if (data.success) {
                    if (data.existe) {
                        mostrarExito('¡Bienvenido! Recargando...');
                        setTimeout(() => window.location.reload(), 800);
                    } else {
                        // Mostrar formulario de registro
                        document.getElementById('loginForm').classList.add('hidden');
                        document.getElementById('registroForm').classList.remove('hidden');

                        // Detectar si es email o DNI/número
                        if (identificador.includes('@')) {
                            document.getElementById('reg_email').value = identificador;
                        } else {
                            document.getElementById('reg_documento').value = identificador;
                        }
                    }
                } else {
                    mostrarError(data.error);
                }
            } catch (err) {
                console.error(err);
                mostrarError('Error en la búsqueda');
            } finally {
                btn.disabled = false;
            }
        }

        async function registrarPaciente(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('nombre', document.getElementById('reg_nombre').value);
            formData.append('apellido', document.getElementById('reg_apellido').value);
            formData.append('email', document.getElementById('reg_email').value);
            formData.append('telefono', document.getElementById('reg_telefono').value);
            formData.append('documento_numero', document.getElementById('reg_documento').value);

            try {
                const res = await fetch('app/api_paciente_registro.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await res.json();
                if (data.success) {
                    mostrarExito('Registrado exitosamente! Recargando...');
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    mostrarError(data.error);
                }
            } catch (err) {
                console.error(err);
                mostrarError('Error en el registro');
            }
        }

        // --- BÚSQUEDA ---
        async function cargarEspecialidades() {
            try {
                const res = await fetch('app/api_busqueda.php?tipo=especialidad');
                const data = await res.json();
                const select = document.getElementById('select-especialidad');
                select.innerHTML = '<option value="">-- Selecciona especialidad --</option>';
                data.items.forEach(e => {
                    select.innerHTML += `<option value="${e.id}">${e.nombre}</option>`;
                });
                if (window.$ && $(select).data('select2')) {
                    $(select).trigger('change.select2');
                }
            } catch (err) {
                console.error(err);
            }
        }

        async function cargarClinicas() {
            try {
                const res = await fetch('app/api_busqueda.php?tipo=clinica');
                const data = await res.json();
                const select = document.getElementById('select-clinica');
                select.innerHTML = '<option value="">-- Selecciona clínica --</option>';
                data.items.forEach(c => {
                    select.innerHTML += `<option value="${c.id}">${c.nombre}</option>`;
                });
            } catch (err) {
                console.error(err);
            }
        }

        async function buscarMedicosPorEspecialidad() {
            const especialidad = document.getElementById('select-especialidad').value;
            if (!especialidad) return;
            clinicaSeleccionada = null;

            try {
                const res = await fetch(`app/api_busqueda.php?tipo=medico_por_especialidad&valor=${especialidad}`);
                const data = await res.json();
                renderMedicos(data.items, 'medicosPorEspecialidad');
            } catch (err) {
                console.error(err);
            }
        }

        async function buscarMedicosPorClinica() {
            const clinica = document.getElementById('select-clinica').value;
            if (!clinica) return;
            clinicaSeleccionada = clinica;

            try {
                const res = await fetch(`app/api_busqueda.php?tipo=medico_por_clinica&valor=${clinica}`);
                const data = await res.json();
                renderMedicos(data.items, 'medicosPorClinica');
            } catch (err) {
                console.error(err);
            }
        }

        async function buscarMedicosPorNombre() {
            const nombre = document.getElementById('input-busqueda-medico').value;
            clinicaSeleccionada = null;

            if (!nombre) {
                await cargarMedicosTodos();
                return;
            }

            try {
                const res = await fetch(`app/api_busqueda.php?tipo=medico&valor=${encodeURIComponent(nombre)}`);
                const data = await res.json();
                renderMedicos(data.items, 'medicosPorNombre');
            } catch (err) {
                console.error(err);
            }
        }

        async function cargarMedicosTodos() {
            try {
                const res = await fetch('app/api_busqueda.php?tipo=medico');
                const data = await res.json();
                renderMedicos(data.items, 'medicosPorNombre');
            } catch (err) {
                console.error(err);
            }
        }

        function renderMedicos(medicos, containerId) {
            const container = document.getElementById(containerId);
            if (!medicos.length) {
                container.innerHTML = '<p style="color: var(--text-muted); text-align: center; padding: 20px;">No hay médicos disponibles</p>';
                return;
            }

            container.innerHTML = medicos.map(m => {
                const imagen = m.imagen_path ?
                    (m.imagen_path.startsWith('app/') ? m.imagen_path : `app/${m.imagen_path}`) :
                    'app/uploads/medicos/template.svg';
                const nombre = m.nombre + ' ' + m.apellido;
                const clinicaIds = m.clinica_ids || '';

                return `
                    <div class="medico-item" 
                         data-id="${m.id}" 
                         data-nombre="${nombre.replace(/"/g, '&quot;')}" 
                         data-imagen="${imagen}"
                         data-clinica-ids="${clinicaIds}"
                         onclick="seleccionarMedico(this)">
                        <img src="${imagen}" alt="${m.nombre}">
                        <div class="medico-item-info">
                            <div class="medico-item-name">Dr(a). ${nombre}</div>
                            <div class="medico-item-esp">${m.especialidades || 'Medicina General'}</div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function seleccionarMedico(elemento) {
            // Obtener datos del elemento
            medicoSeleccionado = {
                id: elemento.dataset.id,
                nombre: elemento.dataset.nombre,
                imagen: elemento.dataset.imagen
            };

            console.log('Medico seleccionado:', medicoSeleccionado);

            // Si viene de búsqueda por clínica, ya tenemos clinicaSeleccionada
            // Si no, usar la primera clínica del médico o 1 por defecto
            if (!clinicaSeleccionada) {
                const clinicaIds = (elemento.dataset.clinicaIds || '').split(',').filter(Boolean);
                clinicaSeleccionada = clinicaIds.length ? clinicaIds[0] : 1;
            }

            console.log('Clinica seleccionada:', clinicaSeleccionada);

            document.querySelectorAll('.medico-item').forEach(el => el.classList.remove('selected'));
            elemento.classList.add('selected');

            // Mostrar resumen
            document.getElementById('resumen-medico').textContent = medicoSeleccionado.nombre;

            // Cargar tratamientos del médico
            cerrarModalBusqueda();
            setTimeout(() => {
                cargarTratamientos();
            }, 400);
        }

        // --- DISPONIBILIDAD ---
        async function cargarDisponibilidad() {
            console.log('=== INICIANDO cargarDisponibilidad ===');
            const fecha = document.getElementById('select-fecha').value;
            console.log('Fecha seleccionada:', fecha);

            if (!fecha) {
                console.log('No hay fecha seleccionada');
                return;
            }

            console.log('Parámetros:', {
                medico_id: medicoSeleccionado.id,
                clinica_id: clinicaSeleccionada || 1,
                fecha: fecha
            });

            try {
                const url = `app/api_disponibilidad.php?medico_id=${medicoSeleccionado.id}&clinica_id=${clinicaSeleccionada || 1}&fecha=${fecha}`;
                console.log('Llamando a:', url);

                const res = await fetch(url);
                console.log('Respuesta recibida:', res.status);

                const data = await res.json();
                console.log('Datos recibidos:', data);

                const slotsGrid = document.getElementById('slotsGrid');
                if (!data.slots || data.slots.length === 0) {
                    slotsGrid.innerHTML = '<p style="grid-column: 1/-1; color: var(--text-muted);">Sin disponibilidad para este día</p>';
                    document.getElementById('slotsContainer').style.display = 'block';
                    return;
                }

                document.getElementById('slotsContainer').style.display = 'block';
                document.getElementById('resumenTurno').style.display = 'block';
                document.getElementById('resumen-fecha').textContent = new Date(fecha).toLocaleDateString('es-AR');

                slotsGrid.innerHTML = data.slots.map(slot => `
                    <button type="button" class="slot-btn" 
                            onclick="${slot.disponible ? `seleccionarHora('${slot.hora}')` : 'mostrarError("Este horario ya está ocupado")'}"
                            ${!slot.disponible ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''}>
                        ${slot.hora} ${!slot.disponible ? '❌' : ''}
                    </button>
                `).join('');

                console.log('Slots renderizados:', data.slots.length);
            } catch (err) {
                console.error('Error en cargarDisponibilidad:', err);
                mostrarError('Error al cargar disponibilidad: ' + err.message);
            }
        }

        function seleccionarHora(hora) {
            horarioSeleccionado = hora;
            document.querySelectorAll('.slot-btn').forEach(el => el.classList.remove('selected'));
            event.target.classList.add('selected');
            document.getElementById('resumen-hora').textContent = hora;
        }

        // --- CONFIRMAR TURNO ---
        async function confirmarTurno() {
            console.log('=== INICIANDO confirmarTurno ===');
            console.log('horarioSeleccionado:', horarioSeleccionado);
            console.log('medicoSeleccionado:', medicoSeleccionado);
            console.log('clinicaSeleccionada:', clinicaSeleccionada);

            if (!horarioSeleccionado) {
                mostrarError('Selecciona un horario');
                return;
            }

            // Si no está autenticado, pedir datos básicos primero
            if (!pacienteAutenticado) {
                console.log('Usuario no autenticado - mostrando modal de datos');
                abrirModalDatosBasicos();
                return;
            }

            // Si está autenticado, continuar con la confirmación
            await confirmarTurnoInterno();
        }

        async function confirmarTurnoInterno() {
            const fecha = document.getElementById('select-fecha').value;
            console.log('Datos a enviar:', {
                medico_id: medicoSeleccionado.id,
                clinica_id: clinicaSeleccionada || 1,
                tratamiento_id: tratamientoSeleccionado?.id || null,
                fecha: fecha,
                hora: horarioSeleccionado,
                observaciones: document.getElementById('observaciones-turno').value
            });

            const formData = new FormData();
            formData.append('medico_id', medicoSeleccionado.id);
            formData.append('clinica_id', clinicaSeleccionada || 1);
            if (tratamientoSeleccionado) {
                formData.append('tratamiento_id', tratamientoSeleccionado.id);
            }
            formData.append('fecha', fecha);
            formData.append('hora', horarioSeleccionado);
            formData.append('observaciones', document.getElementById('observaciones-turno').value);

            const btn = document.querySelector('#modalDisponibilidad .btn-primary');
            if (!btn) {
                console.error('Botón de confirmación no encontrado');
                mostrarError('Error: botón no encontrado');
                return;
            }
            btn.disabled = true;

            try {
                console.log('Enviando fetch a app/api_crear_turno.php');
                const res = await fetch('app/api_crear_turno.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                console.log('Respuesta recibida:', res.status);
                const data = await res.json();
                console.log('Datos de respuesta:', data);

                if (data.success) {
                    mostrarExito(data.mensaje);
                    cerrarModalDisponibilidad();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    mostrarError(data.error || 'Error desconocido');
                }
            } catch (err) {
                console.error('Error en catch:', err);
                mostrarError('Error al confirmar turno: ' + err.message);
            } finally {
                btn.disabled = false;
            }
        }

        // --- MODAL DATOS BÁSICOS ---
        function abrirModalDatosBasicos() {
            mostrarPaso1Login();
            document.getElementById('modalDatosBasicos').classList.add('active');
        }

        function cerrarModalDatosBasicos() {
            document.getElementById('modalDatosBasicos').classList.remove('active');
        }

        function mostrarPaso1Login() {
            document.getElementById('paso1Login').style.display = 'block';
            document.getElementById('paso2Registro').style.display = 'none';
        }

        function mostrarPaso2Registro() {
            document.getElementById('paso1Login').style.display = 'none';
            document.getElementById('paso2Registro').style.display = 'block';
        }

        async function validarPaciente(e) {
            e.preventDefault();
            const identificador = document.getElementById('identificador_login').value.trim();
            const contrasena = document.getElementById('contrasena_login').value;

            if (!identificador || !contrasena) {
                mostrarError('Ingrese email/DNI y contraseña');
                return;
            }

            try {
                const formData = new FormData();
                formData.append('identificador', identificador);
                formData.append('contrasena', contrasena);

                const res = await fetch('app/api_paciente_login.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await res.json();

                if (data.success && data.existe) {
                    // Credenciales válidas
                    window._pacienteAutenticado = true;
                    window._pacienteId = data.paciente_id;
                    cerrarModalDatosBasicos();
                    await confirmarTurnoInterno();
                } else if (data.success && !data.existe) {
                    // Usuario no existe - mostrar formulario de registro
                    document.getElementById('email_registro').value = identificador.includes('@') ? identificador : '';
                    document.getElementById('dni_registro').value = !identificador.includes('@') ? identificador : '';
                    mostrarPaso2Registro();
                } else {
                    mostrarError(data.error || 'Credenciales inválidas');
                }
            } catch (err) {
                console.error('Error:', err);
                mostrarError('Error: ' + err.message);
            }
        }

        async function registrarYConfirmarTurno(e) {
            e.preventDefault();

            const email = document.getElementById('email_registro').value.trim();
            const dni = document.getElementById('dni_registro').value.trim();
            const nombre = document.getElementById('nombre_registro').value.trim();
            const apellido = document.getElementById('apellido_registro').value.trim();
            const telefono = document.getElementById('telefono_registro').value.trim();
            const contrasena = document.getElementById('contrasena_registro').value;

            if (!email || !dni || !nombre || !apellido || !contrasena) {
                mostrarError('Por favor complete todos los campos');
                return;
            }

            if (contrasena.length < 6) {
                mostrarError('La contraseña debe tener al menos 6 caracteres');
                return;
            }

            try {
                const formData = new FormData();
                formData.append('email', email);
                formData.append('documento_numero', dni);
                formData.append('nombre', nombre);
                formData.append('apellido', apellido);
                formData.append('telefono', telefono);
                formData.append('contrasena', contrasena);

                const res = await fetch('app/api_paciente_registro.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await res.json();

                if (data.success) {
                    window._pacienteAutenticado = true;
                    window._pacienteId = data.paciente_id;
                    mostrarExito('Usuario registrado. ' + (data.mensaje || ''));
                    cerrarModalDatosBasicos();
                    await confirmarTurnoInterno();
                } else {
                    mostrarError(data.error || 'Error en el registro');
                }
            } catch (err) {
                console.error('Error:', err);
                mostrarError('Error: ' + err.message);
            }
        }

        // --- TABS ---
        function cambiarTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));

            event.target.closest('.tab-btn').classList.add('active');
            document.getElementById(`busqueda${tab.charAt(0).toUpperCase() + tab.slice(1)}`).classList.remove('hidden');

            if (tab === 'medico') {
                cargarMedicosTodos();
            }
        }
    </script>
</body>

</html>
<!-- Footer -->
<footer class="footer section-py">
    <div class="container">
        <p>&copy; 2026 TurnosMed. Todos los derechos reservados.</p>
        <p style="font-size: 0.9rem; margin-top: 12px;">Gestión de turnos médicos - Hecho con ❤️</p>
    </div>
</footer>

<script>
    // Script limpio - el formulario form-turno no existe en esta página
</script>
</body>

</html>