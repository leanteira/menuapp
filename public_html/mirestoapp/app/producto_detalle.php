<?php
require_once __DIR__ . '/mr_auth.php';
mr_require_auth(['superadmin', 'admin', 'operador']);
$productoId = isset($_GET['producto_id']) ? (int) $_GET['producto_id'] : 0;
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Opciones producto</title>
    <link rel="stylesheet" href="assets/vendor/css/rtl/core.css">
    <link rel="stylesheet" href="assets/vendor/css/rtl/theme-semi-dark.css">
    <link rel="stylesheet" href="assets/css/demo.css">
</head>

<body>
    <div class="container-xxl py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Producto #<?php echo $productoId; ?> · Variantes/Modificadores</h4>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary" href="productos.php">Volver</a>
                <a class="btn btn-danger" href="logout.php">Salir</a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Variantes</h6>
                            <button type="button" class="btn btn-sm btn-primary" id="btnNuevaVar">Agregar variante</button>
                        </div>
                        <table class="table" id="tv">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>+Precio</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Modificadores</h6>
                            <button type="button" class="btn btn-sm btn-primary" id="btnNuevoMod">Agregar modificador</button>
                        </div>
                        <table class="table" id="tm">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>+Precio</th>
                                    <th>Tipo</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalVariante" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloModalVar">Nueva variante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formVar" class="row g-2">
                        <input type="hidden" id="varId">
                        <div class="col-12">
                            <label class="form-label" for="varNombre">Nombre</label>
                            <input id="varNombre" class="form-control" placeholder="Nombre" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="varPrecio">Precio adicional</label>
                            <input id="varPrecio" type="number" step="0.01" class="form-control" placeholder="+Precio">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="formVar" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalModificador" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloModalMod">Nuevo modificador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formMod" class="row g-2">
                        <input type="hidden" id="modId">
                        <div class="col-12">
                            <label class="form-label" for="modNombre">Nombre</label>
                            <input id="modNombre" class="form-control" placeholder="Nombre" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="modPrecio">Precio adicional</label>
                            <input id="modPrecio" type="number" step="0.01" class="form-control" placeholder="+Precio">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="modOblig">Tipo</label>
                            <select id="modOblig" class="form-select">
                                <option value="0">Opcional</option>
                                <option value="1">Obligatorio</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="formMod" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <?php include('include-js-import.php'); ?>
    <script>
        const productoId = <?php echo $productoId; ?>;
        const modalVariante = new bootstrap.Modal(document.getElementById('modalVariante'));
        const modalModificador = new bootstrap.Modal(document.getElementById('modalModificador'));

        async function lv() {
            const r = await fetch('api/mr/admin/variantes.php?producto_id=' + productoId, {
                credentials: 'same-origin'
            });
            const d = await r.json();
            const tb = document.querySelector('#tv tbody');
            tb.innerHTML = '';
            if (!d.ok) return;
            d.variantes.forEach(v => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${v.nombre}</td><td>${Number(v.precio_adicional).toFixed(2)}</td><td><button class="btn btn-sm btn-outline-primary" onclick='evv(${JSON.stringify(v)})'>Editar</button></td>`;
                tb.appendChild(tr);
            });
        }

        async function lm() {
            const r = await fetch('api/mr/admin/modificadores.php?producto_id=' + productoId, {
                credentials: 'same-origin'
            });
            const d = await r.json();
            const tb = document.querySelector('#tm tbody');
            tb.innerHTML = '';
            if (!d.ok) return;
            d.modificadores.forEach(m => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${m.nombre}</td><td>${Number(m.precio_adicional).toFixed(2)}</td><td>${m.obligatorio?'Obligatorio':'Opcional'}</td><td><button class="btn btn-sm btn-outline-primary" onclick='emm(${JSON.stringify(m)})'>Editar</button></td>`;
                tb.appendChild(tr);
            });
        }

        function resetVarForm() {
            varId.value = '';
            varNombre.value = '';
            varPrecio.value = '';
            document.getElementById('tituloModalVar').textContent = 'Nueva variante';
        }

        function resetModForm() {
            modId.value = '';
            modNombre.value = '';
            modPrecio.value = '';
            modOblig.value = '0';
            document.getElementById('tituloModalMod').textContent = 'Nuevo modificador';
        }

        function evv(v) {
            varId.value = v.id;
            varNombre.value = v.nombre;
            varPrecio.value = v.precio_adicional;
            document.getElementById('tituloModalVar').textContent = 'Editar variante';
            modalVariante.show();
        }
        window.evv = evv;

        function emm(m) {
            modId.value = m.id;
            modNombre.value = m.nombre;
            modPrecio.value = m.precio_adicional;
            modOblig.value = m.obligatorio ? '1' : '0';
            document.getElementById('tituloModalMod').textContent = 'Editar modificador';
            modalModificador.show();
        }
        window.emm = emm;

        document.getElementById('btnNuevaVar').addEventListener('click', () => {
            resetVarForm();
            modalVariante.show();
        });

        document.getElementById('btnNuevoMod').addEventListener('click', () => {
            resetModForm();
            modalModificador.show();
        });

        formVar.addEventListener('submit', async (e) => {
            e.preventDefault();
            const p = {
                action: Number(varId.value || 0) ? 'update' : 'create',
                id: Number(varId.value || 0),
                producto_id: productoId,
                nombre: varNombre.value,
                precio_adicional: Number(varPrecio.value || 0)
            };
            const r = await fetch('api/mr/admin/variantes.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(p)
            });
            const d = await r.json();
            if (!d.ok) {
                alert(d.error || 'Error');
                return;
            }
            resetVarForm();
            modalVariante.hide();
            lv();
        });

        formMod.addEventListener('submit', async (e) => {
            e.preventDefault();
            const p = {
                action: Number(modId.value || 0) ? 'update' : 'create',
                id: Number(modId.value || 0),
                producto_id: productoId,
                nombre: modNombre.value,
                precio_adicional: Number(modPrecio.value || 0),
                obligatorio: Number(modOblig.value) === 1
            };
            const r = await fetch('api/mr/admin/modificadores.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(p)
            });
            const d = await r.json();
            if (!d.ok) {
                alert(d.error || 'Error');
                return;
            }
            resetModForm();
            modalModificador.hide();
            lm();
        });

        lv();
        lm();
    </script>
</body>

</html>