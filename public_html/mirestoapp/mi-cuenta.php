<?php
session_start();

// Verificar si el usuario está autenticado
$isAuthenticated = isset($_SESSION['cliente_id']);
$clienteNombre = $_SESSION['cliente_nombre'] ?? 'Mi cuenta';
$clienteTel = $_SESSION['cliente_telefono'] ?? '';
$clienteEmail = $_SESSION['cliente_email'] ?? '';
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
    <title>Mi Cuenta · MiRestoApp</title>
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
        }

        body {
            background: radial-gradient(circle at top right, #ffe5f2 0, transparent 35%), var(--bg);
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
            justify-content: flex-end;
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

        .account-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 2rem;
            margin-top: 2rem;
        }

        .account-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid var(--line);
        }

        .account-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--brand), #ff539c);
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 2rem;
            font-weight: 900;
        }

        .account-info h2 {
            margin: 0;
            font-size: 1.5rem;
        }

        .account-info p {
            margin: 0.25rem 0;
            color: var(--muted);
            font-size: 0.9rem;
        }

        .info-group {
            margin-bottom: 1.5rem;
        }

        .info-label {
            font-weight: 600;
            color: var(--muted);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            margin-bottom: 0.5rem;
        }

        .info-value {
            font-size: 1rem;
            color: var(--ink);
        }

        .btn-brand {
            background: var(--brand);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-brand:hover {
            background: var(--brand-dark);
            color: #fff;
            text-decoration: none;
        }

        .btn-light {
            background: var(--bg);
            border: 1px solid var(--line);
            color: var(--muted);
            border-radius: 8px;
            padding: 0.6rem 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-light:hover {
            background: #fff;
            border-color: var(--muted);
        }

        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .address-card {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: .85rem .95rem;
            background: #fff;
            margin-bottom: .65rem;
        }

        .address-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: .8rem;
        }

        .address-main {
            font-weight: 600;
            font-size: .95rem;
            margin-bottom: .15rem;
        }

        .address-ref {
            color: var(--muted);
            font-size: .84rem;
        }

        .address-tag {
            display: inline-block;
            font-size: .74rem;
            padding: .16rem .46rem;
            border-radius: 999px;
            background: var(--brand-soft);
            color: var(--brand-dark);
            font-weight: 700;
            margin-left: .45rem;
        }

        .section-title {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: .75rem;
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
                        <a class="action-link" href="mis-pedidos.php"><i class="bi bi-receipt"></i> Mis pedidos</a>
                        <button class="action-link" onclick="handleLogout()" style="border:none;background:#fff;cursor:pointer;"><i class="bi bi-box-arrow-right"></i> Salir</button>
                    </div>
                </div>
            </div>
        </header>

        <div class="account-card">
            <div class="account-header">
                <div class="account-avatar"><?php echo strtoupper(substr($clienteNombre, 0, 1)); ?></div>
                <div class="account-info">
                    <h2 id="accountName"><?php echo htmlspecialchars($clienteNombre, ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p id="accountPhone"><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($clienteTel, ENT_QUOTES, 'UTF-8'); ?></p>
                    <p id="accountEmail"><i class="bi bi-envelope"></i> <?php echo htmlspecialchars($clienteEmail, ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="info-group">
                        <div class="info-label">📱 Teléfono</div>
                        <div class="info-value" id="accountPhoneValue"><?php echo htmlspecialchars($clienteTel, ENT_QUOTES, 'UTF-8'); ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-group">
                        <div class="info-label">✉️ Email</div>
                        <div class="info-value" id="accountEmailValue"><?php echo htmlspecialchars($clienteEmail, ENT_QUOTES, 'UTF-8'); ?></div>
                    </div>
                </div>
            </div>

            <div class="mt-2">
                <div class="section-title"><i class="bi bi-geo-alt"></i> Mis direcciones</div>
                <div id="addressesList"></div>
                <button class="btn-light" type="button" id="btnNewAddress"><i class="bi bi-plus-circle"></i> Agregar dirección</button>
            </div>

            <div class="button-group">
                <button class="btn-brand" data-bs-toggle="modal" data-bs-target="#editProfileModal"><i class="bi bi-pencil"></i> Editar perfil</button>
                <button class="btn-light" onclick="changePassword()"><i class="bi bi-key"></i> Cambiar contraseña</button>
                <button class="btn-light" onclick="handleLogout()"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="editProfileForm" class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nombre</label>
                            <input class="form-control" id="editNombre" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Teléfono</label>
                            <input class="form-control" id="editTelefono" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail">
                        </div>
                    </form>
                    <div id="editProfileMsg" class="small mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-brand" id="btnSaveProfile">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editAddressModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addressModalTitle">Editar dirección</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="editAddressForm" class="row g-3">
                        <input type="hidden" id="editDireccionId">
                        <div class="col-12">
                            <label class="form-label">Dirección</label>
                            <input class="form-control" id="editDireccion" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Referencia</label>
                            <input class="form-control" id="editReferencia">
                        </div>
                        <div class="col-12 form-check ms-1">
                            <input class="form-check-input" type="checkbox" id="editFavorita">
                            <label class="form-check-label" for="editFavorita">Marcar como favorita</label>
                        </div>
                    </form>
                    <div id="editAddressMsg" class="small mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-brand" id="btnSaveAddress">Guardar dirección</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const initialProfile = {
            nombre: <?php echo json_encode($clienteNombre, JSON_UNESCAPED_UNICODE); ?>,
            telefono: <?php echo json_encode($clienteTel, JSON_UNESCAPED_UNICODE); ?>,
            email: <?php echo json_encode($clienteEmail, JSON_UNESCAPED_UNICODE); ?>
        };

        let userAddresses = [];

        const editProfileModal = document.getElementById('editProfileModal');
        if (editProfileModal) {
            editProfileModal.addEventListener('show.bs.modal', () => {
                document.getElementById('editNombre').value = document.getElementById('accountName').textContent.trim();
                document.getElementById('editTelefono').value = (document.getElementById('accountPhoneValue').textContent || '').trim();
                document.getElementById('editEmail').value = (document.getElementById('accountEmailValue').textContent || '').trim();
                const msg = document.getElementById('editProfileMsg');
                msg.textContent = '';
                msg.className = 'small mt-3';
            });
        }

        document.getElementById('btnSaveProfile').addEventListener('click', async () => {
            const nombre = document.getElementById('editNombre').value.trim();
            const telefono = document.getElementById('editTelefono').value.trim();
            const email = document.getElementById('editEmail').value.trim();
            const msg = document.getElementById('editProfileMsg');

            msg.className = 'small mt-3';

            if (!nombre || !telefono) {
                msg.classList.add('text-danger');
                msg.textContent = 'Nombre y teléfono son obligatorios.';
                return;
            }

            try {
                const response = await fetch('app/api/mr/update_profile.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        nombre,
                        telefono,
                        email
                    })
                });

                const data = await response.json();
                if (!data.ok) {
                    msg.classList.add('text-danger');
                    msg.textContent = data.error || 'No se pudo actualizar el perfil.';
                    return;
                }

                const cliente = data.cliente || {
                    nombre,
                    telefono,
                    email
                };

                document.getElementById('accountName').textContent = cliente.nombre || '';
                document.getElementById('accountPhone').innerHTML = `<i class="bi bi-telephone"></i> ${cliente.telefono || ''}`;
                document.getElementById('accountEmail').innerHTML = `<i class="bi bi-envelope"></i> ${cliente.email || ''}`;
                document.getElementById('accountPhoneValue').textContent = cliente.telefono || '';
                document.getElementById('accountEmailValue').textContent = cliente.email || '';
                msg.classList.add('text-success');
                msg.textContent = 'Perfil actualizado correctamente.';

                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(editProfileModal);
                    modal?.hide();
                }, 700);
            } catch (err) {
                msg.classList.add('text-danger');
                msg.textContent = 'Error de conexión al actualizar perfil.';
            }
        });

        function renderAddresses() {
            const list = document.getElementById('addressesList');
            if (!list) return;

            if (!userAddresses.length) {
                list.innerHTML = '<div class="text-muted small mb-2">No tenés direcciones cargadas.</div>';
                return;
            }

            list.innerHTML = userAddresses.map((addr) => `
                <div class="address-card">
                    <div class="address-row">
                        <div>
                            <div class="address-main">${addr.direccion || ''} ${addr.is_favorita ? '<span class="address-tag">Favorita</span>' : ''}</div>
                            <div class="address-ref">${addr.referencia || 'Sin referencia'}</div>
                        </div>
                        <button class="btn btn-sm btn-light" type="button" onclick="openEditAddress(${addr.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                </div>
            `).join('');
        }

        async function loadAddresses() {
            try {
                const response = await fetch('app/api/mr/get_addresses.php');
                const raw = await response.text();
                const data = JSON.parse(raw);
                userAddresses = data.ok && Array.isArray(data.direcciones) ? data.direcciones : [];
            } catch (err) {
                userAddresses = [];
            }
            renderAddresses();
        }

        window.openEditAddress = function(addressId) {
            const addr = userAddresses.find((a) => Number(a.id) === Number(addressId));
            if (!addr) return;

            document.getElementById('addressModalTitle').textContent = 'Editar dirección';
            document.getElementById('editDireccionId').value = String(addr.id || '');
            document.getElementById('editDireccion').value = addr.direccion || '';
            document.getElementById('editReferencia').value = addr.referencia || '';
            document.getElementById('editFavorita').checked = !!addr.is_favorita;
            const msg = document.getElementById('editAddressMsg');
            msg.textContent = '';
            msg.className = 'small mt-3';

            const modal = new bootstrap.Modal(document.getElementById('editAddressModal'));
            modal.show();
        };

        document.getElementById('btnNewAddress').addEventListener('click', () => {
            document.getElementById('addressModalTitle').textContent = 'Agregar dirección';
            document.getElementById('editDireccionId').value = '';
            document.getElementById('editDireccion').value = '';
            document.getElementById('editReferencia').value = '';
            document.getElementById('editFavorita').checked = false;
            const msg = document.getElementById('editAddressMsg');
            msg.textContent = '';
            msg.className = 'small mt-3';

            const modal = new bootstrap.Modal(document.getElementById('editAddressModal'));
            modal.show();
        });

        document.getElementById('btnSaveAddress').addEventListener('click', async () => {
            const direccionId = parseInt(document.getElementById('editDireccionId').value || '0', 10);
            const direccion = document.getElementById('editDireccion').value.trim();
            const referencia = document.getElementById('editReferencia').value.trim();
            const isFavorita = document.getElementById('editFavorita').checked ? 1 : 0;
            const msg = document.getElementById('editAddressMsg');

            msg.textContent = '';
            msg.className = 'small mt-3';

            if (!direccion) {
                msg.classList.add('text-danger');
                msg.textContent = 'La dirección es obligatoria.';
                return;
            }

            try {
                const endpoint = direccionId > 0 ? 'app/api/mr/update_address.php' : 'app/api/mr/save_address.php';
                const payload = direccionId > 0 ? {
                    direccion_id: direccionId,
                    direccion,
                    referencia,
                    is_favorita: isFavorita
                } : {
                    direccion,
                    referencia,
                    is_favorita: isFavorita
                };

                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                const raw = await response.text();
                const data = JSON.parse(raw);

                if (!data.ok) {
                    msg.classList.add('text-danger');
                    msg.textContent = data.error || 'No se pudo guardar la dirección.';
                    return;
                }

                msg.classList.add('text-success');
                msg.textContent = 'Dirección guardada correctamente.';
                await loadAddresses();
                setTimeout(() => {
                    const modalEl = document.getElementById('editAddressModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal?.hide();
                }, 600);
            } catch (err) {
                msg.classList.add('text-danger');
                msg.textContent = 'Error de conexión al guardar la dirección.';
            }
        });

        window.changePassword = () => {
            alert('Funcionalidad en desarrollo. Pronto podrás cambiar tu contraseña.');
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

        loadAddresses();
    </script>
</body>

</html>