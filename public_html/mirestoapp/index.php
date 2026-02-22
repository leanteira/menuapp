<?php
session_start();

$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : 'demo-resto';
$clienteNombre = $_SESSION['paciente_nombre'] ?? 'Mi cuenta';
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MiRestoApp · Pedido online</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --brand: #ea1d6f;
            --brand-dark: #ca165e;
            --brand-soft: #ffe2ef;
            --bg: #f3f2f8;
            --ink: #241b3e;
            --muted: #6f6788;
            --line: #e7e4f2;
            --card: #fff;
            --good: #00a650;
            --sun: #ffde59;
            --blue: #2f89ff;
        }

        body {
            background: radial-gradient(circle at top right, #ffe5f2 0, transparent 35%), var(--bg);
            color: var(--ink);
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            padding-right: 340px;
        }

        .container-main {
            max-width: 1120px;
        }

        .topbar {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(35, 18, 63, .06);
            padding: .7rem .9rem;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            font-weight: 800;
            color: var(--brand);
            text-decoration: none;
            letter-spacing: -.02em;
            font-size: 1.1rem;
        }

        .brand-dot {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--brand), #ff539c);
            color: #fff;
            display: grid;
            place-items: center;
            font-weight: 900;
        }

        .topbar-actions {
            display: flex;
            gap: .55rem;
            justify-content: end;
            flex-wrap: wrap;
        }

        .action-link {
            border: 1px solid var(--line);
            background: #fff;
            border-radius: 999px;
            color: #433a61;
            padding: .45rem .75rem;
            font-size: .86rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            gap: .35rem;
            align-items: center;
        }

        .action-link.login {
            background: var(--brand);
            color: #fff;
            border-color: var(--brand);
        }

        .hero {
            margin-top: 1rem;
            border-radius: 16px;
            overflow: hidden;
            background: linear-gradient(100deg, var(--sun) 0 48%, var(--blue) 48% 100%);
            min-height: 188px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.2rem 1.3rem;
            box-shadow: 0 14px 32px rgba(15, 17, 45, .15);
        }

        .hero h1 {
            margin: 0;
            font-size: clamp(1.55rem, 2.7vw, 2.2rem);
            line-height: 1.03;
            letter-spacing: -.02em;
        }

        .hero p {
            margin: .45rem 0 0;
            font-size: 1.05rem;
        }

        .hero-media {
            width: 132px;
            height: 132px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .86);
            display: grid;
            place-items: center;
            font-size: 3rem;
            color: #1f2540;
            box-shadow: inset 0 0 0 10px rgba(255, 255, 255, .42);
            flex: 0 0 auto;
        }

        .restaurant-head {
            margin-top: 1rem;
            background: linear-gradient(135deg, var(--brand) 0%, #f167a1 100%);
            color: #fff;
            border: 0;
            border-radius: 14px;
            box-shadow: 0 10px 26px rgba(234, 29, 111, .22);
        }

        .head-meta {
            opacity: .92;
            font-size: .92rem;
        }

        .search-input {
            min-width: 290px;
            border: 0;
            border-radius: 10px;
            padding: .66rem .8rem;
            background: rgba(255, 255, 255, .96);
            color: #3b3158;
        }

        .category-nav {
            display: flex;
            gap: .6rem;
            overflow-x: auto;
            padding: .25rem 0 .4rem;
            scrollbar-width: thin;
        }

        .category-chip {
            flex: 0 0 auto;
            border: 1px solid var(--line);
            background: #fff;
            border-radius: 999px;
            padding: .42rem .86rem;
            font-size: .88rem;
            font-weight: 700;
            color: #4b4368;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
        }

        .section-title {
            border-left: 4px solid var(--brand);
            padding-left: .65rem;
            margin-bottom: .9rem;
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        .product-card {
            border: 1px solid var(--line);
            border-radius: 12px;
            background: var(--card);
            height: 100%;
            transition: transform .15s ease;
            overflow: hidden;
            box-shadow: 0 7px 20px rgba(25, 18, 52, .06);
        }

        .product-card:hover {
            transform: translateY(-2px);
        }

        .product-media {
            height: 108px;
            position: relative;
            background: #f3f1fa;
        }

        .product-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .product-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            background: rgba(36, 27, 62, .78);
            color: #fff;
            border-radius: 999px;
            padding: .2rem .46rem;
            font-size: .8rem;
        }

        .product-name {
            font-size: .98rem;
            margin-bottom: 0;
            display: flex;
            gap: .38rem;
            align-items: center;
            font-weight: 700;
        }

        .product-desc {
            color: var(--muted);
            min-height: 2.3em;
            font-size: .93rem;
        }

        .product-options {
            display: block;
            margin-top: .15rem;
        }

        .flavors-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: .4rem;
            margin: .25rem 0;
        }

        .flavor-btn {
            border: 1.5px solid var(--line);
            background: #fff;
            border-radius: 8px;
            padding: .32rem .4rem;
            font-size: .76rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s ease;
            color: var(--ink);
        }

        .flavor-btn:hover {
            border-color: var(--brand);
            background: var(--brand-soft);
        }

        .flavor-btn.selected {
            background: var(--brand);
            color: #fff;
            border-color: var(--brand);
        }

        .flavor-sample {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 999px;
            margin-right: .3rem;
            vertical-align: middle;
        }

        @media (hover: hover) and (pointer: fine) {
            .product-options {
                display: none;
            }

            .product-card:hover .product-options,
            .product-card:focus-within .product-options {
                display: block;
            }
        }

        .price {
            font-size: 1.3rem;
            font-weight: 800;
            margin: .15rem 0;
            color: #261d41;
        }

        .floating-cart {
            position: fixed;
            right: 18px;
            top: 108px;
            width: 312px;
            z-index: 1200;
        }

        .cart-card {
            border: 1px solid #d8d5e2;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(25, 18, 52, .08);
            height: calc(100vh - 126px);
            max-height: calc(100vh - 126px);
            display: flex;
            flex-direction: column;
        }

        .cart-card .card-body {
            overflow-y: auto;
            flex: 1;
        }

        .cart-header {
            background: linear-gradient(90deg, #fff 0, #f8f7fc 100%);
            border-bottom: 1px solid var(--line);
            font-size: 1.05rem;
            font-weight: 800;
            padding: .85rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-pill {
            background: var(--brand-soft);
            color: var(--brand-dark);
            border-radius: 999px;
            padding: .2rem .5rem;
            font-size: .78rem;
            font-weight: 700;
        }

        .cart-items {
            max-height: 55vh;
            overflow: auto;
        }

        .mr-toast-wrap {
            position: fixed;
            right: 22px;
            top: 18px;
            bottom: auto;
            z-index: 2400;
            display: flex;
            flex-direction: column;
            gap: .55rem;
            pointer-events: none;
        }

        .mr-toast {
            min-width: 240px;
            max-width: 320px;
            background: #2b2342;
            color: #fff;
            border-radius: 10px;
            padding: .65rem .85rem;
            box-shadow: 0 10px 24px rgba(25, 18, 52, .22);
            border-left: 4px solid #fff;
            opacity: 0;
            transform: translateY(10px);
            animation: toastIn .18s ease forwards;
            font-size: .92rem;
            line-height: 1.25;
        }

        .mr-toast.success {
            border-left-color: #18b663;
        }

        .mr-toast.error {
            border-left-color: #e24c64;
        }

        @keyframes toastIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes toastOut {
            to {
                opacity: 0;
                transform: translateY(10px);
            }
        }

        .cart-item {
            border-bottom: 1px dashed #eceaf3;
            padding-bottom: .38rem;
            margin-bottom: .38rem;
            line-height: 1.2;
        }

        .cart-item:last-child {
            border-bottom: 0;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .cart-summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 1rem;
            margin-bottom: .25rem;
        }

        .cart-total {
            font-size: 1.85rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .btn-brand {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
            font-weight: 700;
        }

        .btn-brand:hover {
            background: var(--brand-dark);
            border-color: var(--brand-dark);
            color: #fff;
        }

        .small-muted {
            font-size: .84rem;
            color: var(--muted);
        }

        @media (max-width: 992px) {
            body {
                padding-right: 0;
            }

            .container-main {
                max-width: 1160px;
            }

            .search-input {
                min-width: 100%;
            }

            .floating-cart {
                position: static;
                width: 100%;
                margin-top: 1rem;
            }

            .cart-card {
                height: auto;
                max-height: none;
            }

            .hero {
                min-height: auto;
                padding: 1rem;
            }

            .hero-media {
                width: 92px;
                height: 92px;
                font-size: 2rem;
            }

            .topbar-actions {
                justify-content: start;
            }
        }

        /* ========== MEJORADO: CARDS CON ANIMACIÓN Y SHADOW ========== */
        .product-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid var(--line);
            background: #fff;
            box-shadow: 0 2px 8px rgba(36, 27, 62, 0.04);
        }

        .product-card:hover {
            transform: translateY(-6px);
            border-color: var(--brand-soft);
            box-shadow: 0 12px 28px rgba(234, 29, 111, 0.15);
        }

        /* ========== CARRUSEL Y VISTA GRID ========== */
        .view-toggle {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            align-items: center;
        }

        .view-btn {
            padding: 0.5rem 1rem;
            border: 1px solid var(--line);
            background: #fff;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .view-btn.active {
            background: var(--brand);
            color: #fff;
            border-color: var(--brand);
        }

        /* Carrusel por categoría */
        .category-carousel {
            position: relative;
            margin-bottom: 2.5rem;
        }

        .carousel-container {
            position: relative;
            overflow: hidden;
            padding: 0 2.25rem;
        }

        .carousel-track,
        .grid-5,
        .scroll-row {
            display: flex;
            gap: 1rem;
            scroll-behavior: smooth;
            scroll-snap-type: x mandatory;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .carousel-track::-webkit-scrollbar,
        .grid-5::-webkit-scrollbar,
        .scroll-row::-webkit-scrollbar {
            display: none;
        }

        .carousel-item {
            flex: 0 0 calc(25% - 0.75rem);
            scroll-snap-align: start;
        }

        /* Grid de 4 columnas con scroll */
        .grid-5 {
            padding-bottom: 0.5rem;
            cursor: grab;
        }

        .grid-5.is-dragging {
            cursor: grabbing;
            user-select: none;
        }

        .grid-5>div {
            flex: 0 0 calc(25% - 0.75rem);
            scroll-snap-align: start;
        }

        /* Responsive */
        @media (max-width: 1400px) {
            .grid-5>div {
                flex: 0 0 calc(33.33% - 0.66rem);
            }

            .carousel-item {
                flex: 0 0 calc(33.33% - 0.66rem);
            }
        }

        @media (max-width: 1200px) {
            .grid-5>div {
                flex: 0 0 calc(50% - 0.5rem);
            }

            .carousel-item {
                flex: 0 0 calc(50% - 0.5rem);
            }
        }

        @media (max-width: 768px) {
            .grid-5>div {
                flex: 0 0 calc(66.66% - 0.33rem);
            }

            .carousel-item {
                flex: 0 0 calc(66.66% - 0.33rem);
            }
        }

        @media (max-width: 576px) {
            .grid-5>div {
                flex: 0 0 100%;
            }

            .carousel-item {
                flex: 0 0 100%;
            }
        }

        /* Botones de navegación del carrusel */
        .carousel-nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            background: rgba(234, 29, 111, 0.9);
            color: #fff;
            border: none;
            border-radius: 999px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 1.1rem;
        }

        .carousel-nav-btn:hover {
            background: var(--brand);
            transform: translateY(-50%) scale(1.1);
        }

        .carousel-nav-btn.prev {
            left: 0.2rem;
        }

        .carousel-nav-btn.next {
            right: 0.2rem;
        }

        /* Scrollbar para carrusel */
        .carousel-track::-webkit-scrollbar {
            height: 6px;
        }

        .carousel-track::-webkit-scrollbar-track {
            background: var(--line);
            border-radius: 3px;
        }

        .carousel-track::-webkit-scrollbar-thumb {
            background: var(--brand);
            border-radius: 3px;
        }

        .carousel-track::-webkit-scrollbar-thumb:hover {
            background: var(--brand-dark);
        }

        /* ========== CANTIDAD CIRCULAR ========== */
        .qty-group {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .qty-btn {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--brand);
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            font-size: 0.9rem;
        }

        .qty-btn:hover {
            background: var(--brand-dark);
            transform: scale(1.1);
        }

        .qty-input {
            width: 38px;
            height: 38px;
            border-radius: 999px;
            border: 2px solid var(--line);
            text-align: center;
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--ink);
            background: #fff;
            cursor: default;
            padding: 0;
            appearance: textfield;
            -moz-appearance: textfield;
        }

        .qty-input::-webkit-outer-spin-button,
        .qty-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .qty-input:focus {
            outline: none;
            border-color: var(--brand);
        }

        /* Tooltip */
        .tooltip-trigger {
            position: relative;
            cursor: help;
            border-bottom: 1px dotted var(--muted);
        }

        .tooltip-text {
            visibility: hidden;
            width: 150px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 6px 8px;
            position: absolute;
            z-index: 1000;
            bottom: 125%;
            left: 50%;
            margin-left: -75px;
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 0.8rem;
            white-space: normal;
            word-wrap: break-word;
        }

        .tooltip-trigger:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        /* ========== MODAL VARIANTES ========== */
        .modal-variants {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-variants.show {
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.2s;
        }

        .modal-variants-content {
            background-color: #fff;
            border-radius: 12px;
            padding: 1.6rem;
            max-width: 680px;
            width: min(94vw, 680px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-variants-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--ink);
        }

        .modal-variants-body {
            margin-bottom: 1.5rem;
            max-height: 52vh;
            overflow-y: auto;
            padding-right: .25rem;
        }

        .variant-group {
            margin-bottom: 1.15rem;
        }

        .variant-options-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .55rem;
        }

        .variant-group-title {
            font-size: 0.92rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--ink);
        }

        .variant-option {
            padding: 0.5rem 0.6rem;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            column-gap: .45rem;
            font-size: .92rem;
            cursor: pointer;
        }

        .variant-option.is-selected {
            border-color: var(--brand);
            background: var(--brand-soft);
        }

        .variant-option input {
            margin: 0;
        }

        .variant-option .flavor-sample {
            margin-right: 0;
        }

        .variant-price {
            font-weight: 700;
            color: var(--brand);
            font-size: 0.88rem;
        }

        .modal-variants-footer {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .modal-variants-footer button {
            padding: 0.5rem 1.2rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .modal-variants-footer .btn-cancel {
            background: #f0f0f0;
            color: #333;
        }

        .modal-variants-footer .btn-cancel:hover {
            background: #e0e0e0;
        }

        .modal-variants-footer .btn-confirm {
            background: var(--brand);
            color: #fff;
        }

        .modal-variants-footer .btn-confirm:hover {
            background: var(--brand-dark);
        }

        .modal-variants-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--line);
            padding-bottom: 1rem;
        }

        .modal-variants-header h5 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--ink);
        }

        .modal-variants-header .btn-close {
            padding: 0;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        @media (max-width: 576px) {
            .variant-options-grid {
                grid-template-columns: 1fr;
            }

            .modal-variants-content {
                width: min(96vw, 680px);
                padding: 1.2rem;
            }
        }
    </style>
</head>

<body>
    <div class="container container-main py-4">
        <header class="topbar">
            <div class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <a class="brand" href="landing.php?slug=<?php echo htmlspecialchars($slug, ENT_QUOTES, 'UTF-8'); ?>">
                        <span class="brand-dot">M</span>
                        MiRestoApp
                    </a>
                </div>
                <div class="col-lg-8">
                    <div class="topbar-actions">
                        <span id="userGreeting" style="font-weight: 600; color: var(--brand); display:none;" class="action-link"><i class="bi bi-person-circle"></i> <span id="userGreetingName">Hola</span></span>
                        <button id="loginBtn" class="action-link login" data-bs-toggle="modal" data-bs-target="#loginModal" style="border:none;cursor:pointer;"><i class="bi bi-box-arrow-in-right"></i> Login</button>
                        <a class="action-link" href="mi-cuenta.php"><i class="bi bi-person"></i> Mi cuenta</a>
                        <a class="action-link" href="mis-pedidos.php"><i class="bi bi-receipt"></i> Mis pedidos</a>
                    </div>
                </div>
            </div>
        </header>

        <section class="hero">
            <div>
                <h1>Pedí directo al local<br>sin vueltas</h1>
                <p>Elegí tus productos, agregá extras y finalizá en segundos.</p>
            </div>
            <div class="hero-media" id="heroIcon">🍽️</div>
        </section>

        <section class="card restaurant-head mb-4">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="h3 mb-1" id="restoNombre">Cargando carta...</h2>
                    <div class="head-meta" id="restoMeta">Preparando datos del local...</div>
                </div>
                <div>
                    <input id="buscador" class="search-input" placeholder="Buscar producto..." aria-label="Buscar producto">
                </div>
            </div>
        </section>

        <div class="category-nav mb-3" id="categoryNav"></div>

        <div class="row g-4">
            <div class="col-lg-12">
                <div id="menuContainer"></div>
            </div>
        </div>
    </div>

    <aside class="floating-cart">
        <div class="cart-card card">
            <div class="cart-header">
                <span>Carrito</span>
                <span class="cart-pill" id="cartCount">0 items</span>
            </div>
            <div class="card-body">
                <div id="carritoItems" class="cart-items small-muted">Sin productos</div>
                <hr>
                <div class="cart-summary-row"><span>Subtotal</span><strong id="cartSubtotal">$ 0.00</strong></div>
                <div class="cart-summary-row"><span>Envío</span><strong id="cartEnvio">$ 0.00</strong></div>
                <div class="d-flex justify-content-between align-items-end mt-2">
                    <span>Total</span>
                    <strong class="cart-total" id="cartTotal">$ 0.00</strong>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <button class="btn btn-brand w-100" data-bs-toggle="modal" data-bs-target="#checkoutModal">Finalizar pedido</button>
            </div>
        </div>
    </aside>

    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Sección de Resumen (confirmación) -->
                    <div id="checkoutSummary" style="display: none; margin-bottom: 1.5rem;" data-confirmed="">
                        <!-- Sección de datos de envío y cliente -->
                        <div style="background: var(--brand-soft); border-radius: 8px; padding: 1.2rem; border-left: 4px solid var(--brand); margin-bottom: 1.5rem;">
                            <h6 style="margin-bottom: 1rem; color: var(--brand); font-weight: 700; font-size: 1rem;">Datos de tu pedido</h6>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <!-- Datos del cliente -->
                                <div>
                                    <div style="font-size: 0.85rem; margin-bottom: 0.5rem; color: var(--muted);"><strong>CLIENTE</strong></div>
                                    <div style="font-size: 0.95rem; margin-bottom: 0.2rem;" id="summaryNombre"></div>
                                    <div style="font-size: 0.85rem; color: var(--muted); margin-bottom: 0.2rem;" id="summaryTelefono"></div>
                                    <div style="font-size: 0.85rem; color: var(--muted);" id="summaryEmail"></div>
                                </div>

                                <!-- Tipo de entrega -->
                                <div>
                                    <div style="font-size: 0.85rem; margin-bottom: 0.5rem; color: var(--muted);"><strong>TIPO DE ENTREGA</strong></div>
                                    <div style="font-size: 0.95rem; margin-bottom: 0.2rem;" id="summaryTipo"></div>
                                    <div style="font-size: 0.85rem; color: var(--muted);" id="summaryMetodoPagoSmall"></div>
                                </div>
                            </div>

                            <!-- Dirección (si es delivery) -->
                            <div id="summaryAddressData" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(0,0,0,0.1);">
                                <div style="font-size: 0.85rem; margin-bottom: 0.5rem; color: var(--muted);"><strong>DIRECCIÓN DE ENTREGA</strong></div>
                                <div style="font-size: 0.95rem; margin-bottom: 0.2rem;" id="summaryDireccion"></div>
                                <div style="font-size: 0.85rem; color: var(--muted);" id="summaryReferenciaWrap"><span id="summaryReferencia"></span></div>
                            </div>

                            <!-- Observaciones -->
                            <div id="summaryObservacionesWrap" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(0,0,0,0.1);">
                                <div style="font-size: 0.85rem; margin-bottom: 0.5rem; color: var(--muted);"><strong>OBSERVACIONES</strong></div>
                                <div style="font-size: 0.95rem;" id="summaryObservaciones"></div>
                            </div>
                        </div>

                        <!-- Resumen de items -->
                        <div style="margin-bottom: 1.5rem;">
                            <h6 style="margin-bottom: 1rem; color: var(--ink); font-weight: 700; font-size: 1rem;">Detalle de tu pedido</h6>
                            <div id="summaryItems" style="background: #f9f8fd; border-radius: 8px; padding: 1rem;">
                                <!-- Los items se rellenarán con JavaScript -->
                            </div>
                        </div>

                        <!-- Totales -->
                        <div style="background: #f9f8fd; border-radius: 8px; padding: 1.2rem; border: 1px solid var(--line);">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.95rem;">
                                <span>Subtotal</span>
                                <strong id="summarySubtotal">$0,00</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; font-size: 0.95rem;">
                                <span>Envío</span>
                                <strong id="summaryEnvio">$0,00</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 1.1rem; padding-top: 1rem; border-top: 2px solid var(--line);">
                                <span style="font-weight: 700;">TOTAL A PAGAR</span>
                                <strong style="color: var(--brand); font-size: 1.2rem;" id="summaryTotal">$0,00</strong>
                            </div>
                        </div>
                    </div>

                    <form id="checkoutForm" class="row g-3" style="display: block;">
                        <!-- Datos del cliente (solo si no está logueado) -->
                        <div id="clientDataWrap" style="display: none;">
                            <div class="col-md-6">
                                <label class="form-label">Nombre</label>
                                <input class="form-control" name="nombre" id="checkoutNombre">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input class="form-control" name="telefono" id="checkoutTelefono">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input class="form-control" name="email" id="checkoutEmail" type="email">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tipo</label>
                            <select class="form-select" name="tipo" id="checkoutTipo">
                                <option value="delivery">Delivery</option>
                                <option value="retiro">Retiro en local</option>
                            </select>
                        </div>

                        <!-- Sección de Direcciones (delivery) -->
                        <div id="direccionWrap" class="col-12" style="display: none;">
                            <div id="addressSection">
                                <!-- Direcciones guardadas (si está logueado) -->
                                <div id="savedAddressesWrap" style="display: none;">
                                    <label class="form-label">Tus direcciones</label>
                                    <div id="savedAddressesList" class="mb-3"></div>
                                    <button type="button" class="btn btn-sm btn-outline-brand" id="btnNewAddress" style="border: 1px solid var(--brand); color: var(--brand); background: transparent; border-radius: 6px; padding: 0.4rem 0.8rem; font-weight: 600; cursor: pointer;">+ Agregar nueva dirección</button>
                                </div>

                                <!-- Entrada de dirección nueva/invitado -->
                                <div id="newAddressWrap">
                                    <label class="form-label">Dirección</label>
                                    <input class="form-control" name="direccion" id="checkoutDireccion">
                                    <small class="text-muted">Calle, número, piso, dpto</small>
                                </div>

                                <label class="form-label mt-3">Referencia (opcional)</label>
                                <input class="form-control" name="referencia" id="checkoutReferencia" placeholder="Ej: timbre 3, casa de color azul">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Método de pago</label>
                            <select class="form-select" name="metodo_pago">
                                <option value="contra_entrega">Pago contra entrega</option>
                                <option value="mercadopago">Mercado Pago</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Observaciones</label>
                            <input class="form-control" name="observaciones">
                        </div>
                    </form>

                    <div id="checkoutMsg" class="mt-3 small"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-outline-brand" id="btnEditCheckout" style="display: none; border: 1px solid var(--brand); color: var(--brand); background: transparent;">Editar</button>
                    <button type="button" class="btn btn-brand" id="btnConfirmarCheckout">Finalizar y revisar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Login/Registro -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-700" id="loginTitle">Iniciar sesión</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Pestaña Login -->
                    <div id="loginForm">
                        <form class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="loginTel" placeholder="1155551234" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="loginPass" placeholder="Tu contraseña" required>
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-brand w-100" onclick="handleLogin()">Ingresar</button>
                            </div>
                            <div class="col-12 text-center small">
                                ¿No tenés cuenta? <a href="#" onclick="toggleAuthForm(event)">Registrate aquí</a>
                            </div>
                        </form>
                    </div>

                    <!-- Pestaña Registro -->
                    <div id="registerForm" style="display:none;">
                        <form class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="regName" placeholder="Tu nombre completo" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="regTel" placeholder="1155551234" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="regEmail" placeholder="tu@email.com">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="regPass" placeholder="Crea una contraseña" required>
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-brand w-100" onclick="handleRegister()">Registrarse</button>
                            </div>
                            <div class="col-12 text-center small">
                                ¿Ya tenés cuenta? <a href="#" onclick="toggleAuthForm(event)">Iniciá sesión aquí</a>
                            </div>
                        </form>
                    </div>

                    <div id="loginMsg" class="mt-3 small alert" style="display:none;"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const slug = <?php echo json_encode($slug, JSON_UNESCAPED_UNICODE); ?>;

        let menuData = null;
        let cart = JSON.parse(localStorage.getItem('mr_cart_' + slug) || '[]');

        function persist() {
            localStorage.setItem('mr_cart_' + slug, JSON.stringify(cart));
        }

        function money(value) {
            const num = Number(value || 0);
            return '$' + num.toLocaleString('es-AR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function showNotification(type = 'success', text = '') {
            const container = document.getElementById('mrToastContainer');
            if (!container || !text) return;

            const toneClass = type === 'error' ? 'error' : 'success';
            const toast = document.createElement('div');
            toast.className = `mr-toast ${toneClass}`;
            toast.textContent = text;
            container.appendChild(toast);

            setTimeout(() => {
                toast.style.animation = 'toastOut .18s ease forwards';
                setTimeout(() => toast.remove(), 220);
            }, 2200);
        }

        function normalize(text) {
            return (text || '').toLowerCase();
        }

        function iconForText(text) {
            const t = normalize(text);
            if (t.includes('pizza')) return '🍕';
            if (t.includes('empan')) return '🥟';
            if (t.includes('helad')) return '🍨';
            if (t.includes('hambur') || t.includes('burger')) return '🍔';
            if (t.includes('papas')) return '🍟';
            if (t.includes('milanesa')) return '🥩';
            if (t.includes('ensalad')) return '🥗';
            if (t.includes('bebida') || t.includes('gaseosa') || t.includes('jugo')) return '🥤';
            if (t.includes('cafe')) return '☕';
            if (t.includes('postre')) return '🍰';
            return '🍽️';
        }

        function gradientForText(text) {
            const t = normalize(text);
            if (t.includes('pizza')) return 'linear-gradient(130deg,#ff875f,#e74372)';
            if (t.includes('empan')) return 'linear-gradient(130deg,#f5c658,#e0832f)';
            if (t.includes('helad')) return 'linear-gradient(130deg,#8ddfff,#5f83ff)';
            if (t.includes('hambur') || t.includes('burger')) return 'linear-gradient(130deg,#f3b86b,#cc6847)';
            if (t.includes('bebida') || t.includes('gaseosa')) return 'linear-gradient(130deg,#65d1c3,#2d8fc5)';
            return 'linear-gradient(130deg,#8c8be8,#e65ca8)';
        }

        function hashTextToInt(text) {
            let hash = 0;
            const source = String(text || '');
            for (let index = 0; index < source.length; index += 1) {
                hash = ((hash << 5) - hash) + source.charCodeAt(index);
                hash |= 0;
            }
            return Math.abs(hash);
        }

        function imageQueryForProduct(text) {
            const t = normalize(text);
            if (t.includes('pizza')) return 'pizza,italian-food';
            if (t.includes('empan')) return 'empanadas,latin-food';
            if (t.includes('helad')) return 'ice-cream,gelato';
            if (t.includes('postre')) return 'dessert,cake';
            if (t.includes('bebida') || t.includes('gaseosa') || t.includes('jugo')) return 'soft-drink,beverage';
            if (t.includes('hambur') || t.includes('burger')) return 'burger,fast-food';
            if (t.includes('papas')) return 'french-fries,fast-food';
            if (t.includes('milanesa')) return 'breaded-steak,food';
            return 'food,dish,restaurant';
        }

        function colorForProductType(text) {
            const t = normalize(text);
            if (t.includes('pizza')) return '#ff875f';
            if (t.includes('empan')) return '#f5c658';
            if (t.includes('helad')) return '#8ddfff';
            if (t.includes('hambur') || t.includes('burger')) return '#f3b86b';
            if (t.includes('bebida') || t.includes('gaseosa') || t.includes('jugo')) return '#65d1c3';
            if (t.includes('postre')) return '#ffb6c1';
            if (t.includes('papas')) return '#ffa500';
            return '#8c8be8';
        }

        function createSolidFallback(productName, catNombre) {
            const color = colorForProductType(`${productName} ${catNombre}`);
            const icon = iconForText(`${productName} ${catNombre}`);
            const width = 640,
                height = 360;
            return `data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='${width}' height='${height}'%3E%3Crect fill='${encodeURIComponent(color)}' width='${width}' height='${height}'/%3E%3Ctext x='50%25' y='50%25' font-size='64' text-anchor='middle' dominant-baseline='middle' fill='%23fff' transform='translate(0,-10)'%3E${encodeURIComponent(icon)}%3C/text%3E%3C/svg%3E`;
        }

        function resolveProductPhoto(prod, catNombre) {
            const img = String(prod?.imagen || '').trim();
            if (img && !img.includes('picsum') && !img.includes('source.unsplash')) {
                return img;
            }
            return createSolidFallback(prod?.nombre || '', catNombre || '');
        }

        function isIceCreamProduct(productName) {
            return normalize(productName).includes('helad');
        }

        // Flavor data cache
        let flavorsByProduct = {};

        async function loadFlavorsForProduct(productId) {
            // Check if already loaded
            if (flavorsByProduct[productId]) {
                return flavorsByProduct[productId];
            }

            try {
                const response = await fetch('app/api/mr/helado_gustos.php');
                const data = await response.json();
                if (data.ok && Array.isArray(data.gustos)) {
                    flavorsByProduct[productId] = data.gustos;
                    return data.gustos;
                }
            } catch (err) {
                console.error('Error loading ice cream flavors:', err);
            }
            return [];
        }

        async function renderFlavorButtons(productId) {
            const flavors = await loadFlavorsForProduct(productId);
            const container = document.getElementById(`flavors_${productId}`);
            if (!container) return;

            if (!flavors.length) {
                container.innerHTML = '<div class="text-muted small">No hay gustos disponibles.</div>';
                return;
            }

            container.innerHTML = flavors.map((flavor) => `
                <button type="button" class="flavor-btn flavor_${productId}" data-flavor="${flavor.nombre}" data-color="${flavor.color_hex}" title="${flavor.descripcion || flavor.nombre}">
                    <span class="flavor-sample" style="background-color: ${flavor.color_hex}"></span>
                    <span class="flavor-label">${flavor.nombre}</span>
                </button>
            `).join('');

            // Attach click handlers for flavor selection (max 3)
            document.querySelectorAll(`.flavor_${productId}`).forEach((btn) => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const selectedCount = document.querySelectorAll(`.flavor_${productId}.selected`).length;
                    const isSelected = btn.classList.contains('selected');

                    if (isSelected) {
                        btn.classList.remove('selected');
                    } else if (selectedCount < 3) {
                        btn.classList.add('selected');
                    } else {
                        alert('Máximo 3 gustos permitidos');
                    }
                });
            });
        }

        function renderCart() {
            const box = document.getElementById('carritoItems');
            const subtotal = cart.reduce((acc, item) => acc + item.total, 0);
            const envio = 0;
            const total = subtotal + envio;
            const itemsCount = cart.reduce((acc, item) => acc + Number(item.cantidad || 0), 0);

            if (!cart.length) {
                box.innerHTML = '<span class="small-muted">Sin productos</span>';
            } else {
                box.innerHTML = cart.map((item, idx) => `
          <div class="cart-item">
            <div class="d-flex justify-content-between align-items-start gap-2">
              <strong>${iconForText(item.nombre)} ${item.nombre} x${item.cantidad}</strong>
              <button class="btn btn-sm btn-link text-danger p-0" onclick="removeCartItem(${idx})">Quitar</button>
            </div>
            <div class="small-muted">${item.detalle_texto || 'Sin extras'}</div>
            <div><strong>${money(item.total)}</strong></div>
          </div>
        `).join('');
            }

            document.getElementById('cartSubtotal').textContent = money(subtotal);
            document.getElementById('cartEnvio').textContent = money(envio);
            document.getElementById('cartTotal').textContent = money(total);
            document.getElementById('cartCount').textContent = `${itemsCount} item${itemsCount === 1 ? '' : 's'}`;
            persist();
        }

        window.removeCartItem = (index) => {
            cart.splice(index, 1);
            renderCart();
        };

        function buildCategoryNav() {
            const nav = document.getElementById('categoryNav');
            if (!menuData || !menuData.categorias) {
                nav.innerHTML = '';
                return;
            }

            nav.innerHTML = menuData.categorias
                .map((cat) => `<a class="category-chip" href="#cat_${cat.id}">${iconForText(cat.nombre)} ${cat.nombre}</a>`)
                .join('');
        }

        function renderProductCard(prod, catNombre) {
            const icon = iconForText(prod.nombre + ' ' + catNombre);
            const bg = gradientForText(prod.nombre + ' ' + catNombre);
            const photo = resolveProductPhoto(prod, catNombre);
            const fallbackPhoto = createSolidFallback(prod.nombre + ' ' + catNombre, catNombre);

            return `
                <article class="product-card card h-100">
                    <div class="product-media" style="background:${bg}">
                        <img src="${photo}" data-fallback="${fallbackPhoto}" alt="${prod.nombre}" loading="lazy" onerror="this.onerror=null;this.src=this.dataset.fallback;">
                        <span class="product-badge">${icon}</span>
                    </div>
                    <div class="card-body d-flex flex-column gap-2">
                        <h4 class="product-name h5"><span>${icon}</span> ${prod.nombre}</h4>
                        
                        <!-- DESCRIPCIÓN CON TOOLTIP -->
                        ${prod.descripcion ? `
                            <div class="tooltip-trigger">
                                <div class="product-desc">${prod.descripcion}</div>
                                <span class="tooltip-text">${prod.descripcion}</span>
                            </div>
                        ` : ''}
                        
                        <div class="price">${money(prod.precio_base)}</div>

                        <!-- CANTIDAD CON BOTONES CIRCULARES -->
                        <div class="mt-2">
                            <div class="qty-group justify-content-center">
                                <button class="qty-btn" onclick="decreaseQty(this)">−</button>
                                <input type="number" min="1" value="1" class="qty-input" id="qty_${prod.id}" readonly>
                                <button class="qty-btn" onclick="increaseQty(this)">+</button>
                            </div>
                        </div>

                        <div class="mt-auto pt-2">
                            <button class="btn btn-sm btn-brand w-100" onclick="handleAddClick(${prod.id})"><i class="bi bi-plus-circle"></i> Agregar</button>
                        </div>
                    </div>
                </article>
            `;
        }

        function renderMenu() {
            if (!menuData) return;

            const query = document.getElementById('buscador').value.trim().toLowerCase();

            // Filtrar productos
            const categoriesWithProducts = menuData.categorias.map((cat) => {
                const filteredProducts = cat.productos.filter((p) => {
                    return p.nombre.toLowerCase().includes(query) ||
                        (p.descripcion || '').toLowerCase().includes(query);
                });
                return {
                    ...cat,
                    productos: filteredProducts
                };
            }).filter(cat => cat.productos.length > 0);

            if (!categoriesWithProducts.length) {
                document.getElementById('menuContainer').innerHTML = '<div class="alert alert-warning">No se encontraron productos.</div>';
                return;
            }

            document.getElementById('menuContainer').innerHTML = renderGridView(categoriesWithProducts);

            // Load flavors for ice cream products
            menuData.categorias.flatMap((c) => c.productos).forEach((prod) => {
                if (isIceCreamProduct(prod.nombre)) {
                    renderFlavorButtons(prod.id);
                }
            });

            setupCategorySwipeRows();

        }

        function renderGridView(categories) {
            return categories.map((cat) => `
                <section class="mb-5" id="cat_${cat.id}">
                    <h3 class="section-title h4 mb-3">${iconForText(cat.nombre)} ${cat.nombre}</h3>
                    <div class="carousel-container">
                        <button class="carousel-nav-btn prev" onclick="scrollCategoryRow(this, -1)" title="Anterior">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <div class="grid-5" data-swipe-row="1">
                            ${cat.productos.map((prod) => `<div>${renderProductCard(prod, cat.nombre)}</div>`).join('')}
                        </div>
                        <button class="carousel-nav-btn next" onclick="scrollCategoryRow(this, 1)" title="Siguiente">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </section>
            `).join('');
        }

        function setupCategorySwipeRows() {
            document.querySelectorAll('.grid-5[data-swipe-row="1"]').forEach((track) => {
                if (track.dataset.swipeBound === '1') return;
                track.dataset.swipeBound = '1';

                let isDragging = false;
                let startX = 0;
                let startScrollLeft = 0;

                track.addEventListener('pointerdown', (event) => {
                    if (event.pointerType === 'mouse' && event.button !== 0) return;

                    const interactiveTarget = event.target.closest('button, a, input, select, textarea, label');
                    if (interactiveTarget) return;

                    isDragging = true;
                    startX = event.clientX;
                    startScrollLeft = track.scrollLeft;
                    track.classList.add('is-dragging');
                });

                track.addEventListener('pointermove', (event) => {
                    if (!isDragging) return;
                    const dx = event.clientX - startX;
                    track.scrollLeft = startScrollLeft - dx;
                });

                const stopDragging = () => {
                    isDragging = false;
                    track.classList.remove('is-dragging');
                };

                track.addEventListener('pointerup', stopDragging);
                track.addEventListener('pointercancel', stopDragging);
                track.addEventListener('pointerleave', stopDragging);

                track.addEventListener('wheel', (event) => {
                    if (Math.abs(event.deltaY) > Math.abs(event.deltaX)) {
                        track.scrollLeft += event.deltaY;
                    }
                }, {
                    passive: true
                });
            });
        }

        window.scrollCategoryRow = function(btn, direction) {
            const container = btn.closest('.carousel-container');
            const track = container?.querySelector('.grid-5');
            if (!track) return;

            const firstCard = track.querySelector(':scope > div');
            const step = firstCard ? (firstCard.getBoundingClientRect().width + 16) : 320;
            track.scrollBy({
                left: step * direction,
                behavior: 'smooth'
            });
        };

        window.increaseQty = function(btn) {
            const group = btn?.closest?.('.qty-group');
            if (!group) return;
            const input = group.querySelector('.qty-input');
            if (!input) return;
            const current = parseInt(input.value || 1);
            input.value = current + 1;
        };

        window.decreaseQty = function(btn) {
            const group = btn?.closest?.('.qty-group');
            if (!group) return;
            const input = group.querySelector('.qty-input');
            if (!input) return;
            const current = parseInt(input.value || 1);
            input.value = Math.max(1, current - 1);
        };

        let pendingVariantProductId = null;

        function syncModalOptionStates(modalEl = document.getElementById('productVariantsModal')) {
            if (!modalEl) return;
            modalEl.querySelectorAll('.variant-option').forEach((label) => {
                const input = label.querySelector('input[type="radio"], input[type="checkbox"]');
                label.classList.toggle('is-selected', !!input?.checked);
            });
        }

        window.openVariantsModal = async function(productId) {
            const product = (menuData.categorias || []).flatMap(c => c.productos || []).find(p => p.id == productId);
            if (!product) return;

            pendingVariantProductId = productId;
            const modal = document.getElementById('productVariantsModal');
            const title = modal?.querySelector('#variantsModalTitle');
            const body = modal?.querySelector('#variantsModalBody');

            if (title) title.textContent = product.nombre;
            if (body) {
                body.innerHTML = '';
                // Variantes (radio buttons)
                if (product.variantes && product.variantes.length > 0) {
                    const varGroup = document.createElement('div');
                    varGroup.className = 'variant-group';
                    const varTitle = document.createElement('div');
                    varTitle.className = 'variant-group-title';
                    varTitle.textContent = 'Variantes';
                    varGroup.appendChild(varTitle);

                    const varGrid = document.createElement('div');
                    varGrid.className = 'variant-options-grid';

                    product.variantes.forEach(v => {
                        const opt = document.createElement('label');
                        opt.className = 'variant-option';
                        opt.innerHTML = `
                            <input type="radio" name="variant" value="${v.id}" data-variant-name="${v.nombre}" data-variant-price="${v.precio || 0}">
                            <span>${v.nombre}</span>
                            <span class="variant-price">${money(parseFloat(v.precio || 0))}</span>
                        `;
                        varGrid.appendChild(opt);
                    });
                    varGroup.appendChild(varGrid);
                    body.appendChild(varGroup);
                }

                // Modificadores (checkboxes)
                if (product.modificadores && product.modificadores.length > 0) {
                    const modGroup = document.createElement('div');
                    modGroup.className = 'variant-group';
                    const modTitle = document.createElement('div');
                    modTitle.className = 'variant-group-title';
                    modTitle.textContent = 'Extras';
                    modGroup.appendChild(modTitle);

                    const modGrid = document.createElement('div');
                    modGrid.className = 'variant-options-grid';

                    product.modificadores.forEach(m => {
                        const opt = document.createElement('label');
                        opt.className = 'variant-option';
                        opt.innerHTML = `
                            <input type="checkbox" name="modifier" value="${m.id}" data-modifier-name="${m.nombre}" data-modifier-price="${m.precio || 0}">
                            <span>${m.nombre}</span>
                            <span class="variant-price">${money(parseFloat(m.precio || 0))}</span>
                        `;
                        modGrid.appendChild(opt);
                    });
                    modGroup.appendChild(modGrid);
                    body.appendChild(modGroup);
                }

                if (isIceCreamProduct(product.nombre)) {
                    const iceGroup = document.createElement('div');
                    iceGroup.className = 'variant-group';
                    const iceTitle = document.createElement('div');
                    iceTitle.className = 'variant-group-title';
                    iceTitle.textContent = 'Gustos (elegí hasta 3)';
                    iceGroup.appendChild(iceTitle);

                    const iceGrid = document.createElement('div');
                    iceGrid.className = 'variant-options-grid';

                    const flavors = await loadFlavorsForProduct(productId);
                    if (!flavors.length) {
                        const empty = document.createElement('div');
                        empty.className = 'small text-muted';
                        empty.textContent = 'No hay gustos disponibles.';
                        iceGroup.appendChild(empty);
                    } else {
                        flavors.forEach((flavor) => {
                            const opt = document.createElement('label');
                            opt.className = 'variant-option';
                            const color = flavor.color_hex || '#ccc';
                            opt.innerHTML = `
                                <input type="checkbox" name="ice_flavor" value="${flavor.id || flavor.nombre}" data-flavor-name="${flavor.nombre}">
                                <span><span class="flavor-sample" style="background-color:${color}"></span>${flavor.nombre}</span>
                                <span class="variant-price">&nbsp;</span>
                            `;
                            iceGrid.appendChild(opt);
                        });
                        iceGroup.appendChild(iceGrid);
                    }

                    body.appendChild(iceGroup);
                }

                syncModalOptionStates(modal);
            }

            if (modal) modal.classList.add('show');
        };

        window.closeVariantsModal = function() {
            const modal = document.getElementById('productVariantsModal');
            if (modal) modal.classList.remove('show');
            pendingVariantProductId = null;
        };

        function getModalSelectedOptions() {
            const modal = document.getElementById('productVariantsModal');
            if (!modal) return {};
            const variant = modal.querySelector('input[name="variant"]:checked');
            const modifiers = Array.from(modal.querySelectorAll('input[name="modifier"]:checked'));
            const flavors = Array.from(modal.querySelectorAll('input[name="ice_flavor"]:checked'));
            return {
                variantId: variant?.value || null,
                variantName: variant?.dataset.variantName || null,
                variantPrice: parseFloat(variant?.dataset.variantPrice || 0),
                modifierIds: modifiers.map(m => m.value),
                modifierNames: modifiers.map(m => m.dataset.modifierName),
                modifierPrices: modifiers.map(m => parseFloat(m.dataset.modifierPrice || 0)),
                flavorIds: flavors.map((f) => f.value),
                flavorNames: flavors.map((f) => f.dataset.flavorName || f.value)
            };
        }

        window.handleAddClick = function(productId) {
            const product = (menuData.categorias || []).flatMap(c => c.productos || []).find(p => p.id == productId);
            if (!product) return;
            if ((product.variantes && product.variantes.length > 0) || (product.modificadores && product.modificadores.length > 0) || isIceCreamProduct(product.nombre)) {
                window.openVariantsModal(productId);
            } else {
                addToCart(productId);
            }
        };

        window.addToCart = function(productId, selectedOptions = null) {
            const product = (menuData.categorias || []).flatMap(c => c.productos || []).find(p => p.id == productId);
            if (!product) return;

            const qtyInput = document.getElementById('qty_' + productId);
            const qty = parseInt(qtyInput?.value || 1);
            if (qty <= 0) return;

            const options = selectedOptions || getModalSelectedOptions();
            if (isIceCreamProduct(product.nombre) && (!options.flavorNames || options.flavorNames.length === 0)) {
                alert('Elegí al menos 1 gusto para el helado.');
                return;
            }
            if ((options.flavorNames || []).length > 3) {
                alert('Podés elegir hasta 3 gustos.');
                return;
            }

            const detailParts = [];
            if (options.variantName) detailParts.push(`Variante: ${options.variantName}`);
            if (options.modifierNames && options.modifierNames.length > 0) detailParts.push(`Extras: ${options.modifierNames.join(', ')}`);
            if (options.flavorNames && options.flavorNames.length > 0) detailParts.push(`Gustos: ${options.flavorNames.join(', ')}`);

            const variantPrice = options.variantPrice || 0;
            const modifiersPrice = (options.modifierPrices || []).reduce((a, b) => a + b, 0);
            const unitPrice = product.precio_base + variantPrice + modifiersPrice;

            const newItem = {
                producto_id: product.id,
                nombre: product.nombre,
                cantidad: qty,
                variante_id: options.variantId || null,
                modificadores: options.modifierIds || [],
                gustos: options.flavorIds || [],
                detalle_texto: detailParts.join(' | '),
                precio_unitario: unitPrice,
                total: unitPrice * qty
            };

            cart.push(newItem);
            renderCart();
            window.closeVariantsModal();
            showNotification('success', `✓ ${product.nombre} agregado al carrito`);
        };

        async function cargarCarta() {
            const response = await fetch('app/api/mr/menu.php?slug=' + encodeURIComponent(slug));
            const data = await response.json();

            if (!data.ok) {
                document.getElementById('menuContainer').innerHTML = `<div class="alert alert-danger">${data.error || 'No se pudo cargar la carta.'}</div>`;
                return;
            }

            menuData = data;
            document.getElementById('restoNombre').textContent = data.restaurante.nombre;

            const address = data.restaurante.direccion || '';
            const phone = data.restaurante.telefono || '';
            const status = data.restaurante.estado === 'abierto' ? ' · Abierto ahora' : '';
            document.getElementById('restoMeta').textContent = [address, phone].filter(Boolean).join(' · ') + status;

            const heroIcon = document.getElementById('heroIcon');
            heroIcon.textContent = iconForText((data.restaurante.nombre || '') + ' ' + (data.restaurante.rubro || ''));

            buildCategoryNav();
            renderMenu();
        }

        // Variables para manejo de direcciones
        let isUserLoggedIn = false;
        let userName = '';
        let userTelefono = '';
        let userEmail = '';
        let userAddresses = [];
        let selectedAddressId = null;

        // Función para cargar si el usuario está logueado
        async function loadUserData() {
            try {
                const response = await fetch('app/api/mr/check_auth.php');
                const data = await response.json();
                isUserLoggedIn = data.is_logged_in || false;
                userName = data.nombre || '';
                userTelefono = data.telefono || '';
                userEmail = data.email || '';

                // Actualizar header
                const userGreeting = document.getElementById('userGreeting');
                const loginBtn = document.getElementById('loginBtn');
                if (isUserLoggedIn) {
                    userGreeting.style.display = 'inline-flex';
                    loginBtn.style.display = 'none';
                    document.getElementById('userGreetingName').textContent = `Hola, ${userName}`;
                } else {
                    userGreeting.style.display = 'none';
                    loginBtn.style.display = 'inline-block';
                }

                // Cargar direcciones si está logueado y es delivery
                if (isUserLoggedIn) {
                    loadUserAddresses();
                }
            } catch (err) {
                console.error('Error checking auth:', err);
            }
        }

        // Función para cargar direcciones del usuario
        async function loadUserAddresses() {
            try {
                const response = await fetch('app/api/mr/get_addresses.php');
                const raw = await response.text();
                let data = null;

                try {
                    data = JSON.parse(raw);
                } catch (parseErr) {
                    console.error('Error loading addresses: invalid JSON response', raw.slice(0, 220));
                    userAddresses = [];
                    return;
                }

                if (data.ok && Array.isArray(data.direcciones)) {
                    userAddresses = data.direcciones;
                } else {
                    userAddresses = [];
                }
            } catch (err) {
                console.error('Error loading addresses:', err);
                userAddresses = [];
            }
        }

        // Mapear el modal de checkout para cargar datos
        const checkoutModal = document.getElementById('checkoutModal');
        if (checkoutModal) {
            checkoutModal.addEventListener('show.bs.modal', async () => {
                const form = document.getElementById('checkoutForm');
                const clientDataWrap = document.getElementById('clientDataWrap');
                const savedAddressesWrap = document.getElementById('savedAddressesWrap');
                const newAddressWrap = document.getElementById('newAddressWrap');
                const isDelivery = document.getElementById('checkoutTipo').value === 'delivery';
                document.getElementById('direccionWrap').style.display = isDelivery ? 'block' : 'none';

                // Si el usuario está logueado
                if (isUserLoggedIn) {
                    // Ocultar campos de nombre/teléfono/email
                    clientDataWrap.style.display = 'none';

                    // Si es delivery, cargar direcciones guardadas
                    if (isDelivery) {
                        await loadUserAddresses();
                        renderSavedAddresses();
                    } else {
                        savedAddressesWrap.style.display = 'none';
                        newAddressWrap.style.display = 'none';
                    }
                } else {
                    // Si no está logueado, mostrar campos de datos
                    clientDataWrap.style.display = 'grid';
                    document.getElementById('checkoutNombre').setAttribute('required', '');
                    document.getElementById('checkoutTelefono').setAttribute('required', '');
                    savedAddressesWrap.style.display = 'none';
                    newAddressWrap.style.display = isDelivery ? 'block' : 'none';
                }
            });
        }

        // Renderizar direcciones guardadas
        function renderSavedAddresses() {
            const savedAddressesWrap = document.getElementById('savedAddressesWrap');
            const savedAddressesList = document.getElementById('savedAddressesList');
            const newAddressWrap = document.getElementById('newAddressWrap');

            if (!userAddresses.length) {
                savedAddressesWrap.style.display = 'none';
                newAddressWrap.style.display = 'block';
                return;
            }

            savedAddressesWrap.style.display = 'block';
            newAddressWrap.style.display = 'none';
            const defaultAddress = userAddresses.find((addr) => Number(addr.is_favorita) === 1) || userAddresses[0];
            selectedAddressId = defaultAddress ? Number(defaultAddress.id) : null;

            savedAddressesList.innerHTML = userAddresses.map((addr) => `
                <div style="border: 1px solid var(--line); border-radius: 8px; padding: 1rem; margin-bottom: 0.5rem; cursor: pointer; transition: all 0.15s; ${selectedAddressId === Number(addr.id) ? 'border-color: var(--brand); background: var(--brand-soft);' : ''}" onclick="selectAddress(${addr.id}, this)">
                    <div style="display: flex; justify-content: space-between; align-items: start; gap: 0.5rem;">
                        <div style="flex: 1;">
                            <strong>${addr.direccion}</strong>
                            ${addr.referencia ? `<br><small style="color: var(--muted);">${addr.referencia}</small>` : ''}
                        </div>
                        <div style="text-align: right;">
                            ${addr.is_favorita ? '<i class="bi bi-star-fill" style="color: var(--sun);"></i>' : '<i class="bi bi-star" style="color: var(--muted);"></i>'}
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Seleccionar dirección guardada
        window.selectAddress = (addressId, element) => {
            selectedAddressId = addressId;
            document.querySelectorAll('#savedAddressesList > div').forEach(el => {
                el.style.borderColor = 'var(--line)';
                el.style.background = '#fff';
            });
            element.style.borderColor = 'var(--brand)';
            element.style.background = 'var(--brand-soft)';
            document.getElementById('newAddressWrap').style.display = 'none';
        };

        // Botón agregar nueva dirección
        const btnNewAddress = document.getElementById('btnNewAddress');
        if (btnNewAddress) {
            btnNewAddress.addEventListener('click', (e) => {
                e.preventDefault();
                selectedAddressId = null;
                document.querySelectorAll('#savedAddressesList > div').forEach(el => {
                    el.style.borderColor = 'var(--line)';
                    el.style.background = '#fff';
                });
                document.getElementById('newAddressWrap').style.display = 'block';
                document.getElementById('checkoutDireccion').value = '';
                document.getElementById('checkoutReferencia').value = '';
            });
        }

        // Listener para cambiar tipo de pedido
        document.getElementById('checkoutTipo').addEventListener('change', (e) => {
            const delivery = e.target.value === 'delivery';
            document.getElementById('direccionWrap').style.display = delivery ? 'block' : 'none';

            if (delivery && isUserLoggedIn) {
                renderSavedAddresses();
            } else {
                document.getElementById('newAddressWrap').style.display = delivery ? 'block' : 'none';
                document.getElementById('savedAddressesWrap').style.display = 'none';
            }
        });

        async function checkout() {
            const checkoutMsg = document.getElementById('checkoutMsg');
            const checkoutSummary = document.getElementById('checkoutSummary');
            const checkoutForm = document.getElementById('checkoutForm');
            const btnConfirmarCheckout = document.getElementById('btnConfirmarCheckout');
            const btnEditCheckout = document.getElementById('btnEditCheckout');

            checkoutMsg.className = 'mt-3 small';

            if (!cart.length) {
                checkoutMsg.classList.add('text-danger');
                checkoutMsg.textContent = 'El carrito está vacío.';
                return;
            }

            const form = document.getElementById('checkoutForm');
            const formData = new FormData(form);
            const tipo = formData.get('tipo');

            // Validar direccion si es delivery
            if (tipo === 'delivery') {
                if (isUserLoggedIn) {
                    if (!selectedAddressId && !formData.get('direccion')) {
                        checkoutMsg.classList.add('text-danger');
                        checkoutMsg.textContent = 'Debes seleccionar o ingresar una dirección.';
                        return;
                    }
                } else {
                    if (!formData.get('direccion')) {
                        checkoutMsg.classList.add('text-danger');
                        checkoutMsg.textContent = 'Debes ingresar una dirección.';
                        return;
                    }
                }
            }

            // Si es la primera vez, mostrar resumen
            if (!checkoutSummary.dataset.confirmed) {
                // Guardar FormData para después
                window.checkoutFormData = formData;

                // Obtener datos del cliente
                let clientNombre, clientTelefono, clientEmail;

                if (isUserLoggedIn) {
                    clientNombre = userName;
                    // Obtener teléfono y email del usuario (si están disponibles)
                    // Por ahora usaremos lo que tengamos en la sesión
                } else {
                    clientNombre = formData.get('nombre');
                    clientTelefono = formData.get('telefono');
                    clientEmail = formData.get('email');
                }

                // Obtener dirección
                let direccionTexto = '';
                let referenciaTexto = '';

                if (tipo === 'delivery') {
                    if (selectedAddressId) {
                        const dirSeleccionada = userAddresses.find(d => d.id == selectedAddressId);
                        if (dirSeleccionada) {
                            direccionTexto = dirSeleccionada.direccion;
                            referenciaTexto = dirSeleccionada.referencia || '';
                        }
                    } else {
                        direccionTexto = formData.get('direccion');
                        referenciaTexto = formData.get('referencia') || '';
                    }
                }

                // Llenar el resumen
                document.getElementById('summaryNombre').textContent = clientNombre || 'No especificado';
                document.getElementById('summaryTelefono').textContent = clientTelefono || userTelefono || 'No especificado';
                document.getElementById('summaryEmail').textContent = clientEmail || userEmail || 'No especificado';

                const tipoLabel = tipo === 'delivery' ? 'Delivery' : 'Retiro en local';
                document.getElementById('summaryTipo').textContent = tipoLabel;

                const metodoLabel = formData.get('metodo_pago') === 'mercadopago' ? 'Mercado Pago' : 'Pago contra entrega';
                document.getElementById('summaryMetodoPagoSmall').textContent = metodoLabel;

                if (tipo === 'delivery') {
                    document.getElementById('summaryDireccion').textContent = direccionTexto;
                    document.getElementById('summaryReferencia').textContent = referenciaTexto || 'Sin referencia';
                    if (!referenciaTexto) {
                        document.getElementById('summaryReferenciaWrap').style.display = 'none';
                    } else {
                        document.getElementById('summaryReferenciaWrap').style.display = 'block';
                    }
                    document.getElementById('summaryAddressData').style.display = 'block';
                } else {
                    document.getElementById('summaryAddressData').style.display = 'none';
                }

                const observaciones = formData.get('observaciones') || '';
                if (observaciones) {
                    document.getElementById('summaryObservaciones').textContent = observaciones;
                    document.getElementById('summaryObservacionesWrap').style.display = 'block';
                } else {
                    document.getElementById('summaryObservacionesWrap').style.display = 'none';
                }

                // Llenar resumen de items
                const subtotal = cart.reduce((acc, item) => acc + item.total, 0);
                const envio = 0;
                const total = subtotal + envio;

                const itemsHtml = cart.map(item => `
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                        <div>
                            <div style="font-weight: 600; font-size: 0.95rem;">${item.nombre} x${item.cantidad}</div>
                            ${item.detalle_texto ? `<div style="font-size: 0.85rem; color: var(--muted);">${item.detalle_texto}</div>` : ''}
                        </div>
                        <strong style="color: var(--brand); white-space: nowrap; margin-left: 1rem;">${money(item.total)}</strong>
                    </div>
                `).join('');

                document.getElementById('summaryItems').innerHTML = itemsHtml;
                document.getElementById('summarySubtotal').textContent = money(subtotal);
                document.getElementById('summaryEnvio').textContent = money(envio);
                document.getElementById('summaryTotal').textContent = money(total);

                // Mostrar resumen y ocultar formulario
                checkoutForm.style.display = 'none';
                checkoutSummary.style.display = 'block';
                checkoutSummary.dataset.confirmed = 'true';

                // Cambiar botones
                btnConfirmarCheckout.textContent = 'Confirmar pedido';
                btnEditCheckout.style.display = 'inline-block';
                checkoutMsg.className = 'mt-3 small alert alert-info';
                checkoutMsg.textContent = 'Revisa los datos. Si todo está correcto, confirma el pedido.';
                checkoutMsg.style.display = 'block';
                return;
            }

            // Si llegamos aquí, proceder a confirmar el pedido
            await confirmCheckout();
        }

        async function confirmCheckout() {
            const checkoutMsg = document.getElementById('checkoutMsg');
            const form = document.getElementById('checkoutForm');
            const formData = window.checkoutFormData || new FormData(form);
            const tipo = formData.get('tipo');

            // Construir payload
            const payload = {
                slug,
                tipo,
                metodo_pago: formData.get('metodo_pago'),
                observaciones: formData.get('observaciones'),
                cliente: {},
                direccion: {},
                items: cart.map((item) => ({
                    producto_id: item.producto_id,
                    cantidad: item.cantidad,
                    variante_id: item.variante_id,
                    modificadores: item.modificadores
                }))
            };

            // Si está logueado, no enviar datos de cliente (usa sesión)
            if (!isUserLoggedIn) {
                payload.cliente = {
                    nombre: formData.get('nombre'),
                    telefono: formData.get('telefono'),
                    email: formData.get('email')
                };
            }

            // Manejo de dirección
            if (tipo === 'delivery') {
                if (selectedAddressId) {
                    // Usar dirección guardada
                    payload.direccion_id = selectedAddressId;
                } else {
                    // Nueva dirección
                    const newDireccion = formData.get('direccion');
                    const newReferencia = formData.get('referencia');

                    if (isUserLoggedIn) {
                        // Guardar dirección primero
                        const saveAddrResponse = await fetch('app/api/mr/save_address.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                direccion: newDireccion,
                                referencia: newReferencia,
                                is_favorita: 0
                            })
                        });

                        const saveAddrRaw = await saveAddrResponse.text();
                        let saveAddrData = null;
                        try {
                            saveAddrData = JSON.parse(saveAddrRaw);
                        } catch (parseErr) {
                            checkoutMsg.classList.add('text-danger');
                            checkoutMsg.textContent = 'Error al guardar la dirección (respuesta inválida del servidor).';
                            return;
                        }
                        if (saveAddrData.ok) {
                            payload.direccion_id = saveAddrData.direccion_id;
                        } else {
                            checkoutMsg.classList.add('text-danger');
                            checkoutMsg.textContent = 'Error al guardar la dirección.';
                            return;
                        }
                    } else {
                        // Enviar dirección directamente
                        payload.direccion = {
                            direccion: newDireccion,
                            referencia: newReferencia
                        };
                    }
                }
            }

            const response = await fetch('app/api/mr/checkout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            const rawCheckout = await response.text();
            let data = null;
            try {
                data = JSON.parse(rawCheckout);
            } catch (parseErr) {
                checkoutMsg.classList.add('text-danger');
                checkoutMsg.textContent = 'El servidor devolvió una respuesta inválida al confirmar.';
                return;
            }

            if (!data.ok) {
                checkoutMsg.classList.add('text-danger');
                checkoutMsg.textContent = data.error || 'No se pudo generar el pedido.';
                return;
            }

            if (formData.get('metodo_pago') === 'mercadopago') {
                const paymentResponse = await fetch('app/api/mr/payments/mercadopago_preference.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        pedido_id: data.pedido_id
                    })
                });
                const rawPayment = await paymentResponse.text();
                let paymentData = null;
                try {
                    paymentData = JSON.parse(rawPayment);
                } catch (parseErr) {
                    checkoutMsg.classList.add('text-danger');
                    checkoutMsg.textContent = 'Pedido creado, pero la respuesta de pago fue inválida.';
                    return;
                }

                if (!paymentData.ok) {
                    checkoutMsg.classList.add('text-danger');
                    checkoutMsg.textContent = paymentData.error || 'Pedido creado, pero no se pudo iniciar Mercado Pago.';
                    return;
                }

                const initUrl = paymentData.init_point || paymentData.sandbox_init_point;
                if (initUrl) {
                    window.location.href = initUrl;
                    return;
                }
            }

            checkoutMsg.classList.add('text-success');
            checkoutMsg.textContent = `Pedido #${data.pedido_id} generado correctamente.`;
            cart = [];
            renderCart();
            setTimeout(() => {
                const targetUrl = data.redirect_url || `pedido-finalizado.php?pedido_id=${encodeURIComponent(data.pedido_id)}&slug=${encodeURIComponent(slug)}`;
                window.location.href = targetUrl;
            }, 900);
        }

        document.getElementById('btnConfirmarCheckout').addEventListener('click', checkout);

        document.getElementById('btnEditCheckout').addEventListener('click', (e) => {
            e.preventDefault();
            const checkoutSummary = document.getElementById('checkoutSummary');
            const checkoutForm = document.getElementById('checkoutForm');
            const btnConfirmarCheckout = document.getElementById('btnConfirmarCheckout');
            const btnEditCheckout = document.getElementById('btnEditCheckout');
            const checkoutMsg = document.getElementById('checkoutMsg');

            // Volver al formulario
            checkoutForm.style.display = 'block';
            checkoutSummary.style.display = 'none';
            checkoutSummary.dataset.confirmed = '';

            // Resetear botones
            btnConfirmarCheckout.textContent = 'Finalizar y revisar';
            btnEditCheckout.style.display = 'none';
            checkoutMsg.style.display = 'none';
        });

        document.getElementById('buscador').addEventListener('input', renderMenu);

        // Auth functions
        window.toggleAuthForm = (e) => {
            e.preventDefault();
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const title = document.getElementById('loginTitle');

            if (loginForm.style.display === 'none') {
                loginForm.style.display = 'block';
                registerForm.style.display = 'none';
                title.textContent = 'Iniciar sesión';
            } else {
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
                title.textContent = 'Registrarse';
            }
        };

        window.handleLogin = async () => {
            const tel = document.getElementById('loginTel').value.trim();
            const pass = document.getElementById('loginPass').value.trim();
            const msg = document.getElementById('loginMsg');

            if (!tel || !pass) {
                msg.style.display = 'block';
                msg.className = 'mt-3 small alert alert-warning';
                msg.textContent = 'Completa todos los campos';
                return;
            }

            try {
                const response = await fetch('app/api/mr/auth_login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        telefono: tel,
                        password: pass
                    })
                });

                const data = await response.json();

                if (data.ok) {
                    msg.style.display = 'block';
                    msg.className = 'mt-3 small alert alert-success';
                    msg.textContent = 'Bienvenido! Redirigiendo...';
                    setTimeout(() => {
                        window.location.href = 'mi-cuenta.php';
                    }, 1500);
                } else {
                    msg.style.display = 'block';
                    msg.className = 'mt-3 small alert alert-danger';
                    msg.textContent = data.error || 'Error al iniciar sesión';
                }
            } catch (err) {
                msg.style.display = 'block';
                msg.className = 'mt-3 small alert alert-danger';
                msg.textContent = 'Error de conexión';
            }
        };

        window.handleRegister = async () => {
            const nombre = document.getElementById('regName').value.trim();
            const tel = document.getElementById('regTel').value.trim();
            const email = document.getElementById('regEmail').value.trim();
            const pass = document.getElementById('regPass').value.trim();
            const msg = document.getElementById('loginMsg');

            if (!nombre || !tel || !pass) {
                msg.style.display = 'block';
                msg.className = 'mt-3 small alert alert-warning';
                msg.textContent = 'Completa nombre, teléfono y contraseña';
                return;
            }

            try {
                const response = await fetch('app/api/mr/auth_register.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        nombre,
                        telefono: tel,
                        email,
                        password: pass
                    })
                });

                const data = await response.json();

                if (data.ok) {
                    msg.style.display = 'block';
                    msg.className = 'mt-3 small alert alert-success';
                    msg.textContent = 'Registro exitoso! Incia sesión con tus datos';
                    setTimeout(() => {
                        document.getElementById('loginForm').style.display = 'block';
                        document.getElementById('registerForm').style.display = 'none';
                        document.getElementById('loginTitle').textContent = 'Iniciar sesión';
                        document.getElementById('regName').value = '';
                        document.getElementById('regTel').value = '';
                        document.getElementById('regEmail').value = '';
                        document.getElementById('regPass').value = '';
                    }, 1500);
                } else {
                    msg.style.display = 'block';
                    msg.className = 'mt-3 small alert alert-danger';
                    msg.textContent = data.error || 'Error al registrarse';
                }
            } catch (err) {
                msg.style.display = 'block';
                msg.className = 'mt-3 small alert alert-danger';
                msg.textContent = 'Error de conexión';
            }
        };

        renderCart();
        cargarCarta();
        loadUserData();

        // Modal de variantes - event listeners
        document.addEventListener('click', (e) => {
            const modal = document.getElementById('productVariantsModal');
            if (!modal) return;

            if (e.target.id === 'variantsCancelBtn' || e.target.id === 'variantsCloseBtn' || (e.target === modal && e.target.classList.contains('modal-variants'))) {
                window.closeVariantsModal();
                return;
            }

            if (e.target.closest('#variantsConfirmBtn')) {
                if (pendingVariantProductId !== null) {
                    addToCart(pendingVariantProductId);
                }
            }
        });

        document.addEventListener('change', (e) => {
            const modal = document.getElementById('productVariantsModal');
            if (!modal || !modal.classList.contains('show')) return;

            if (e.target.matches('input[name="ice_flavor"]')) {
                const selected = modal.querySelectorAll('input[name="ice_flavor"]:checked').length;
                if (selected > 3) {
                    e.target.checked = false;
                    alert('Máximo 3 gustos permitidos.');
                }
            }

            if (e.target.matches('input[name="variant"], input[name="modifier"], input[name="ice_flavor"]')) {
                syncModalOptionStates(modal);
            }
        });
    </script>
    <div id="productVariantsModal" class="modal-variants">
        <div class="modal-variants-content">
            <div class="modal-variants-header">
                <h5 id="variantsModalTitle">Selecciona opciones</h5>
                <button type="button" class="btn-close" id="variantsCloseBtn" aria-label="Close">✕</button>
            </div>
            <div id="variantsModalBody" class="modal-variants-body"></div>
            <div class="modal-variants-footer">
                <button type="button" class="btn btn-secondary btn-cancel" id="variantsCancelBtn">Cancelar</button>
                <button type="button" class="btn btn-primary btn-confirm" id="variantsConfirmBtn">Agregar</button>
            </div>
        </div>
    </div>
    <div id="mrToastContainer" class="mr-toast-wrap" aria-live="polite" aria-atomic="true"></div>
</body>

</html>