<?php
session_start();

$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : 'demo-resto';
$clienteNombre = $_SESSION['paciente_nombre'] ?? 'Mi Perfil';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MiRestoApp | Descubrí locales y pedí online</title>
    <meta name="description" content="Pedí online en tus locales favoritos. Descubrí promociones y hacé tu pedido en pocos pasos.">

    <style>
        :root {
            --brand: #ea1d6f;
            --brand-dark: #cd175f;
            --ink: #1d1733;
            --muted: #726d86;
            --bg: #f6f6f8;
            --card: #ffffff;
            --line: #e7e5ee;
            --ok: #00a650;
            --radius-sm: 12px;
            --radius-md: 18px;
            --radius-lg: 24px;
            --shadow-soft: 0 8px 24px rgba(25, 18, 51, .08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            color: var(--ink);
            background: var(--bg);
        }

        img {
            max-width: 100%;
            display: block;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .container {
            width: min(1120px, 92vw);
            margin: 0 auto;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid var(--line);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .topbar-inner {
            min-height: 72px;
            display: grid;
            grid-template-columns: 220px 1fr 160px;
            align-items: center;
            gap: 18px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            color: var(--brand);
            letter-spacing: -.02em;
            font-size: 1.18rem;
        }

        .brand-pill {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, #ff3f87, var(--brand-dark));
            color: #fff;
            display: grid;
            place-items: center;
            font-weight: 800;
        }

        .location {
            font-size: .78rem;
            color: var(--muted);
            margin-top: 4px;
        }

        .location strong {
            display: block;
            color: var(--ink);
            font-size: .92rem;
            margin-top: 2px;
        }

        .search {
            position: relative;
        }

        .search input {
            width: 100%;
            height: 46px;
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 0 52px 0 16px;
            background: #f2f1f6;
            color: var(--ink);
            font-size: .95rem;
        }

        .search button {
            position: absolute;
            right: 4px;
            top: 4px;
            width: 38px;
            height: 38px;
            border: 0;
            border-radius: 999px;
            background: var(--brand);
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
        }

        .profile {
            justify-self: end;
            font-size: .92rem;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .main {
            padding: 20px 0 40px;
        }

        .grid-main {
            display: grid;
            grid-template-columns: 210px 1fr;
            gap: 18px;
            align-items: start;
        }

        .location-card {
            background: var(--card);
            border-radius: var(--radius-md);
            border: 1px solid var(--line);
            box-shadow: var(--shadow-soft);
            padding: 18px;
        }

        .location-card h3 {
            margin: 0 0 8px;
            font-size: 1.05rem;
            line-height: 1.3;
        }

        .location-card p {
            margin: 0;
            color: var(--muted);
            font-size: .9rem;
            line-height: 1.45;
        }

        .btn-mini {
            margin-top: 14px;
            border: 0;
            background: #f1eff6;
            color: var(--ink);
            border-radius: 10px;
            padding: 9px 12px;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
        }

        .content {
            min-width: 0;
        }

        .chip-row {
            display: grid;
            grid-template-columns: repeat(8, minmax(90px, 1fr));
            gap: 10px;
            margin-bottom: 10px;
        }

        .chip {
            background: #f1f0f5;
            border: 1px solid var(--line);
            border-radius: var(--radius-sm);
            padding: 10px 8px;
            text-align: center;
            font-size: .78rem;
            font-weight: 700;
            color: #2e2748;
            line-height: 1.15;
        }

        .chip i {
            display: block;
            font-style: normal;
            font-size: 1.2rem;
            margin-bottom: 4px;
        }

        .logo-strip {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 6px;
            margin-bottom: 18px;
            scrollbar-width: thin;
        }

        .logo-pill {
            flex: 0 0 auto;
            width: 68px;
            height: 44px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #fff;
            display: grid;
            place-items: center;
            font-size: .74rem;
            color: #4a4262;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(12, 8, 29, .05);
        }

        .promo-row {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 12px;
            margin-bottom: 20px;
        }

        .promo {
            border-radius: var(--radius-lg);
            min-height: 170px;
            overflow: hidden;
            display: grid;
            align-items: end;
            padding: 18px;
            color: #fff;
            position: relative;
        }

        .promo h2,
        .promo h3,
        .promo p {
            margin: 0;
            position: relative;
            z-index: 1;
            max-width: 320px;
        }

        .promo h2,
        .promo h3 {
            font-size: clamp(1.45rem, 2.5vw, 2rem);
            line-height: 1.04;
            margin-bottom: 8px;
            letter-spacing: -.03em;
        }

        .promo p {
            font-size: 1rem;
            opacity: .95;
            line-height: 1.2;
        }

        .promo-main {
            background: linear-gradient(105deg, #f5e61a 0 47%, #0094ff 47% 100%);
            color: #151129;
        }

        .promo-main .badge {
            position: absolute;
            top: 16px;
            right: 16px;
            background: rgba(255, 255, 255, .85);
            font-size: .75rem;
            font-weight: 800;
            padding: 5px 10px;
            border-radius: 999px;
            z-index: 1;
        }

        .promo-secondary {
            background: linear-gradient(120deg, #ffd94f 0 50%, #ff005f 50% 100%);
        }

        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 0 0 12px;
        }

        .section-head h3 {
            margin: 0;
            font-size: 1.65rem;
            letter-spacing: -.02em;
        }

        .pager {
            display: flex;
            gap: 8px;
        }

        .pager span {
            width: 32px;
            height: 32px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            background: #fff;
            border: 1px solid var(--line);
            font-size: 1rem;
            color: #6a6481;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .store-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-soft);
        }

        .store-media {
            height: 148px;
            background: #ddd;
            position: relative;
            overflow: hidden;
        }

        .store-media::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(130deg, rgba(0, 0, 0, .05), rgba(0, 0, 0, .35));
        }

        .store-offer {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 1;
            background: #f8f52f;
            color: #18122d;
            border-radius: 8px;
            font-size: .74rem;
            font-weight: 800;
            padding: 6px 8px;
            line-height: 1;
        }

        .store-body {
            padding: 11px 12px 14px;
        }

        .store-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 4px;
            font-size: 1rem;
            font-weight: 800;
        }

        .rate {
            font-size: .86rem;
            color: #1f1938;
            display: inline-flex;
            gap: 3px;
            align-items: center;
        }

        .meta {
            color: var(--muted);
            font-size: .87rem;
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .meta .dot {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: #a69fb8;
            display: inline-block;
        }

        .cta-bar {
            margin-top: 24px;
            border-radius: var(--radius-lg);
            background: linear-gradient(135deg, var(--brand) 0%, #fb4f97 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 16px 18px;
            flex-wrap: wrap;
        }

        .cta-bar p {
            margin: 0;
            font-size: .94rem;
            opacity: .94;
        }

        .cta-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            border: 0;
            border-radius: 999px;
            padding: 10px 16px;
            font-weight: 700;
            cursor: pointer;
            font-size: .9rem;
        }

        .btn-white {
            background: #fff;
            color: var(--brand-dark);
        }

        .btn-dark {
            background: #261f3f;
            color: #fff;
        }

        @media (max-width: 1050px) {
            .topbar-inner {
                grid-template-columns: 1fr;
                gap: 10px;
                padding: 12px 0;
            }

            .profile {
                justify-self: start;
            }

            .grid-main {
                grid-template-columns: 1fr;
            }

            .chip-row {
                grid-template-columns: repeat(4, minmax(90px, 1fr));
            }

            .promo-row {
                grid-template-columns: 1fr;
            }

            .cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 700px) {
            .chip-row {
                grid-template-columns: repeat(3, minmax(90px, 1fr));
            }

            .cards {
                grid-template-columns: 1fr;
            }

            .section-head h3 {
                font-size: 1.35rem;
            }
        }
    </style>
</head>

<body>
    <header class="topbar">
        <div class="container topbar-inner">
            <div>
                <a class="brand" href="landing.php?slug=<?php echo e($slug); ?>">
                    <span class="brand-pill">M</span>
                    MiRestoApp
                </a>
                <div class="location">Enviar a <strong>Buenos Aires</strong></div>
            </div>

            <form class="search" action="index.php" method="get">
                <input type="hidden" name="slug" value="<?php echo e($slug); ?>">
                <input type="search" name="q" placeholder="Buscar locales o platos" aria-label="Buscar locales">
                <button type="submit" aria-label="Buscar">⌕</button>
            </form>

            <a class="profile" href="app/login.php"><?php echo e($clienteNombre); ?></a>
        </div>
    </header>

    <main class="main">
        <div class="container grid-main">
            <aside class="location-card">
                <h3>Completá los detalles de tu ubicación</h3>
                <p>Descubrí locales cerca de vos, promos exclusivas y tiempos de entrega reales.</p>
                <button class="btn-mini" type="button">Entendido</button>
            </aside>

            <section class="content">
                <div class="chip-row">
                    <div class="chip"><i>🍔</i>Restaurantes</div>
                    <div class="chip"><i>🛒</i>Market</div>
                    <div class="chip"><i>🛍️</i>Súper</div>
                    <div class="chip"><i>🍦</i>Helados</div>
                    <div class="chip"><i>☕</i>Café</div>
                    <div class="chip"><i>💊</i>Farmacia</div>
                    <div class="chip"><i>🥤</i>Bebidas</div>
                    <div class="chip"><i>🐶</i>Mascotas</div>
                </div>

                <div class="logo-strip" aria-label="Locales destacados">
                    <div class="logo-pill">KFC</div>
                    <div class="logo-pill">Boom</div>
                    <div class="logo-pill">Sushi</div>
                    <div class="logo-pill">Burger</div>
                    <div class="logo-pill">Milanesa</div>
                    <div class="logo-pill">Pasta</div>
                    <div class="logo-pill">Veggie</div>
                    <div class="logo-pill">Café</div>
                    <div class="logo-pill">Empan.</div>
                </div>

                <div class="promo-row">
                    <article class="promo promo-main">
                        <span class="badge">Hasta 45% OFF</span>
                        <div>
                            <h2>Restaurantes</h2>
                            <p>Disfrutá las mejores promos de la semana.</p>
                        </div>
                    </article>

                    <article class="promo promo-secondary">
                        <div>
                            <h3>Medios de pago</h3>
                            <p>Pagá como quieras, rápido y seguro.</p>
                        </div>
                    </article>
                </div>

                <div class="section-head">
                    <h3>Descubrí estas opciones</h3>
                    <div class="pager"><span>‹</span><span>›</span></div>
                </div>

                <div class="cards" id="storesGrid"></div>

                <div class="cta-bar">
                    <div>
                        <strong>¿Listo para pedir?</strong>
                        <p>Entrá a la carta online de tu local y terminá tu pedido en segundos.</p>
                    </div>
                    <div class="cta-actions">
                        <a class="btn btn-white" href="index.php?slug=<?php echo e($slug); ?>">Ver carta</a>
                        <a class="btn btn-dark" href="app/login.php">Iniciar sesión</a>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <script>
        const stores = [{
                name: 'Sushi Pop - Downtown',
                eta: '15-35 min',
                price: '$ 1.689',
                rating: '4.6',
                offer: 'Hasta 39% OFF',
                bg: 'linear-gradient(130deg,#6dc5ea,#3d6bb3)'
            },
            {
                name: 'Club del Progreso',
                eta: '15-35 min',
                price: '$ 1.149',
                rating: '4.2',
                offer: 'Hasta 50% OFF',
                bg: 'linear-gradient(130deg,#ff8a7f,#d73f5c)'
            },
            {
                name: 'Poked - Microcentro',
                eta: '15-35 min',
                price: '$ 1.689',
                rating: '4.4',
                offer: 'Hasta 50% OFF',
                bg: 'linear-gradient(130deg,#88d884,#2f8f59)'
            },
            {
                name: 'Tifany Deli',
                eta: '10-25 min',
                price: '$ 1.250',
                rating: '4.1',
                offer: 'Hasta 30% OFF',
                bg: 'linear-gradient(130deg,#e5c39a,#9e6f49)'
            },
            {
                name: 'Empanadas House',
                eta: '20-40 min',
                price: '$ 1.100',
                rating: '4.3',
                offer: '2x1 en seleccionadas',
                bg: 'linear-gradient(130deg,#f0d35c,#e17f2f)'
            },
            {
                name: 'Pizza Rápida',
                eta: '20-45 min',
                price: '$ 1.450',
                rating: '4.5',
                offer: 'Envío gratis',
                bg: 'linear-gradient(130deg,#f86f78,#c6284c)'
            }
        ];

        const storesGrid = document.getElementById('storesGrid');

        storesGrid.innerHTML = stores.map((store) => `
      <article class="store-card">
        <div class="store-media" style="background:${store.bg}">
          <span class="store-offer">${store.offer}</span>
        </div>
        <div class="store-body">
          <div class="store-title">
            <span>${store.name}</span>
            <span class="rate">★ ${store.rating}</span>
          </div>
          <div class="meta">
            <span>⏱ ${store.eta}</span>
            <span class="dot"></span>
            <span>🚚 ${store.price}</span>
            <span class="dot"></span>
            <span style="color: var(--ok); font-weight:700;">Abierto</span>
          </div>
        </div>
      </article>
    `).join('');
    </script>
</body>

</html>