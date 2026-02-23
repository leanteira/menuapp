<?php
session_start();

// Verificar si el usuario está autenticado
$isAuthenticated = isset($_SESSION['cliente_id']);
$clienteNombre = $_SESSION['cliente_nombre'] ?? 'Mi cuenta';
$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : 'demo-resto';

if (!$isAuthenticated) {
    header('Location: index.php');
    exit;
}
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis Pedidos · MiRestoApp</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --brand: #2f8a3b;
            --brand-dark: #246d2f;
            --brand-soft: #e9f6e8;
            --bg: #f3f7ee;
            --ink: #1f2a1f;
            --muted: #5f6f61;
            --line: #dce8d9;
            --card: #fff;
            --good: #2f8a3b;
            --sun: #dceea5;
        }

        body {
            background: radial-gradient(circle at top right, #e8f5e4 0, transparent 36%), var(--bg);
            color: var(--ink);
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
        }

        .container-main {
            max-width: 1120px;
        }

        .topbar {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(39, 79, 39, .08);
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
            background: linear-gradient(135deg, var(--brand), #4dae5c);
            color: #fff;
            display: grid;
            place-items: center;
            font-weight: 900;
        }

        .topbar-actions {
            display: flex;
            gap: .55rem;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .action-link {
            border: 1px solid var(--line);
            background: #fff;
            border-radius: 999px;
            color: #355239;
            padding: .45rem .75rem;
            font-size: .86rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            gap: .35rem;
            align-items: center;
        }

        .page-header {
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: var(--muted);
            font-size: 1rem;
        }

        .order-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: .9rem 1rem;
            margin-bottom: .75rem;
            transition: all 0.15s ease;
        }

        .order-card:hover {
            box-shadow: 0 4px 12px rgba(39, 79, 39, .12);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: .55rem;
            margin-bottom: .55rem;
        }

        .order-id {
            font-size: 0.9rem;
            color: var(--muted);
            font-weight: 600;
        }

        .order-number {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--brand);
        }

        .order-date {
            font-size: 0.8rem;
            color: var(--muted);
        }

        .order-status {
            display: inline-block;
            padding: 0.25rem 0.65rem;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .order-status.nuevo {
            background: #fff3cd;
            color: #856404;
        }

        .order-status.confirmado {
            background: #d1ecf1;
            color: #0c5460;
        }

        .order-status.preparando {
            background: #e1f0dc;
            color: #2d6a31;
        }

        .order-status.listo {
            background: #d4edda;
            color: #155724;
        }

        .order-status.entregado {
            background: #d4edda;
            color: #155724;
        }

        .order-body {
            margin-bottom: .55rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .65rem;
            flex-wrap: wrap;
        }

        .order-items {
            font-size: 0.85rem;
            color: var(--ink);
            margin-bottom: 0;
        }

        .order-address {
            font-size: 0.8rem;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: .65rem;
            padding-top: .45rem;
            border-top: 1px dashed var(--line);
        }

        .order-total {
            font-size: 1rem;
            font-weight: 700;
            color: var(--brand);
        }

        .btn-small {
            background: var(--brand);
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 0.3rem 0.65rem;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .btn-small:hover {
            background: var(--brand-dark);
            color: #fff;
        }

        .empty-state {
            background: #fff;
            border: 2px dashed var(--line);
            border-radius: 12px;
            padding: 3rem 2rem;
            text-align: center;
            color: var(--muted);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state p {
            margin: 0.5rem 0;
        }

        .detail-item {
            padding: .45rem 0;
            border-bottom: 1px solid var(--line);
        }

        .detail-item:last-child {
            border-bottom: 0;
        }

        .detail-meta {
            color: var(--muted);
            font-size: .8rem;
        }
    </style>
</head>

<body>
    <div class="container container-main py-4">
        <header class="topbar">
            <div class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <a class="brand" href="index.php?slug=<?php echo htmlspecialchars($slug, ENT_QUOTES, 'UTF-8'); ?>">
                        <span class="brand-dot">M</span>
                        MiRestoApp
                    </a>
                </div>
                <div class="col-lg-8">
                    <div class="topbar-actions">
                        <a class="action-link" href="index.php"><i class="bi bi-bag"></i> Pedir ahora</a>
                        <a class="action-link" href="mi-cuenta.php"><i class="bi bi-person"></i> Mi cuenta</a>
                        <button class="action-link" onclick="handleLogout()" style="border:none;background:#fff;cursor:pointer;"><i class="bi bi-box-arrow-right"></i> Salir</button>
                    </div>
                </div>
            </div>
        </header>

        <div class="page-header">
            <h1><i class="bi bi-receipt"></i> Mis Pedidos</h1>
            <p>Historial de tus pedidos y seguimiento en tiempo real</p>
        </div>

        <div id="ordersContainer">
            <!-- Los pedidos se cargan aquí con JavaScript -->
        </div>
    </div>

    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailsTitle">Detalle del pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div id="orderDetailsBody"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function money(value) {
            const num = Number(value || 0);
            return '$' + num.toLocaleString('es-AR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function getStatusLabel(status) {
            const labels = {
                'nuevo': 'Nuevo',
                'confirmado': 'Confirmado',
                'preparando': 'Preparando',
                'listo': 'Listo',
                'enviado': 'En camino',
                'entregado': 'Entregado',
                'cancelado': 'Cancelado'
            };
            return labels[status] || status;
        }

        async function loadOrders() {
            try {
                const response = await fetch('app/api/mr/get_pedidos.php');
                const raw = await response.text();
                let data = null;
                try {
                    data = JSON.parse(raw);
                } catch (parseErr) {
                    throw new Error('Respuesta inválida del servidor al cargar pedidos.');
                }

                const container = document.getElementById('ordersContainer');

                if (!data.ok || !data.pedidos || data.pedidos.length === 0) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p><strong>No tienes pedidos aún</strong></p>
                            <p>Realiza tu primer pedido ahora</p>
                            <a href="index.php" style="color: var(--brand); text-decoration: none; font-weight: 600;">Ir a pedir →</a>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = data.pedidos.map((order) => `
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <div class="order-id">Pedido</div>
                                <div class="order-number">#${order.id}</div>
                                <div class="order-date">${new Date(order.created_at).toLocaleString('es-AR')}</div>
                            </div>
                            <span class="order-status ${order.estado}">${getStatusLabel(order.estado)}</span>
                        </div>

                        <div class="order-body">
                            <div class="order-items">
                                <strong>Productos:</strong> ${order.items_count} item${order.items_count !== 1 ? 's' : ''}
                            </div>
                            ${order.tipo === 'delivery' ? `
                                <div class="order-address">
                                    <i class="bi bi-geo-alt"></i>
                                    ${order.direccion}
                                </div>
                            ` : `
                                <div class="order-address">
                                    <i class="bi bi-building"></i>
                                    Retiro en local
                                </div>
                            `}
                        </div>

                        <div class="order-footer">
                            <button class="btn-small" onclick="viewOrderDetails(${order.id})">Ver detalles</button>
                            <div class="order-total">${money(order.total)}</div>
                        </div>
                    </div>
                `).join('');
            } catch (err) {
                console.error('Error:', err);
                document.getElementById('ordersContainer').innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-exclamation-triangle"></i>
                        <p><strong>Error al cargar los pedidos</strong></p>
                        <p>${err?.message || 'Intenta recargar la página'}</p>
                    </div>
                `;
            }
        };

        window.viewOrderDetails = async (orderId) => {
            const title = document.getElementById('orderDetailsTitle');
            const body = document.getElementById('orderDetailsBody');
            title.textContent = `Detalle del pedido #${orderId}`;
            body.innerHTML = '<div class="text-muted">Cargando detalle...</div>';

            const modalEl = document.getElementById('orderDetailsModal');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            try {
                const response = await fetch(`app/api/mr/get_pedido_detalle.php?pedido_id=${encodeURIComponent(orderId)}`);
                const raw = await response.text();
                const data = JSON.parse(raw);

                if (!data.ok || !data.pedido) {
                    body.innerHTML = `<div class="alert alert-warning mb-0">${data.error || 'No se pudo cargar el detalle del pedido.'}</div>`;
                    return;
                }

                const pedido = data.pedido;
                const itemsHtml = (pedido.items || []).map((item) => {
                    const details = (item.detalles || []).map((d) => d.nombre).filter(Boolean);
                    return `
                        <div class="detail-item">
                            <div class="d-flex justify-content-between gap-2">
                                <strong>${item.nombre_producto} x${item.cantidad}</strong>
                                <strong>${money(item.total)}</strong>
                            </div>
                            ${details.length ? `<div class="detail-meta">${details.join(', ')}</div>` : ''}
                        </div>
                    `;
                }).join('');

                body.innerHTML = `
                    <div class="mb-3 small text-muted">
                        ${pedido.tipo === 'delivery' ? `<div><i class="bi bi-geo-alt"></i> ${pedido.direccion || 'Sin dirección'}</div>` : '<div><i class="bi bi-building"></i> Retiro en local</div>'}
                        <div><i class="bi bi-clock"></i> ${new Date(pedido.created_at).toLocaleString('es-AR')}</div>
                    </div>
                    <div>${itemsHtml || '<div class="text-muted">Sin items</div>'}</div>
                    <hr>
                    <div class="d-flex justify-content-between"><span>Subtotal</span><strong>${money(pedido.subtotal)}</strong></div>
                    <div class="d-flex justify-content-between"><span>Envío</span><strong>${money(pedido.costo_envio)}</strong></div>
                    <div class="d-flex justify-content-between mt-2" style="font-size:1.05rem;"><span><strong>Total</strong></span><strong style="color:var(--brand)">${money(pedido.total)}</strong></div>
                `;
            } catch (err) {
                body.innerHTML = '<div class="alert alert-danger mb-0">Error al cargar el detalle del pedido.</div>';
            }
        };

        window.handleLogout = async () => {
            if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                try {
                    await fetch('app/api/mr/auth_logout.php', {
                        method: 'POST'
                    });
                    window.location.href = 'index.php';
                } catch (err) {
                    console.error('Error:', err);
                    window.location.href = 'index.php';
                }
            }
        };

        // Cargar pedidos al iniciar
        loadOrders();
    </script>
</body>

</html>