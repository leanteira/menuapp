<?php
include('include-header.php');
?>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container"><?php include('include-menu.php'); ?><div class="layout-page"><?php include('include-navbar.php'); ?><div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Gustos de empanada</h4>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <form id="formGusto" class="row g-2"><input type="hidden" id="gustoId">
                                    <div class="col-md-4"><input class="form-control" id="gustoNombre" placeholder="Nombre" required></div>
                                    <div class="col-md-5"><input class="form-control" id="gustoDesc" placeholder="Descripcion"></div>
                                    <div class="col-md-2"><select id="gustoActivo" class="form-select">
                                            <option value="1">Activo</option>
                                            <option value="0">Inactivo</option>
                                        </select></div>
                                    <div class="col-md-1 d-grid"><button class="btn btn-primary" type="submit">Guardar</button></div>
                                </form>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="tablaGustos">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nombre</th>
                                                <th>Descripcion</th>
                                                <th>Estado</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div><?php include('include-footer.php'); ?><div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
    </div>
    <?php include('include-js-import.php'); ?>
    <script>
        async function loadGustos() {
            const r = await fetch('api/mr/admin/empanada_gustos.php', {
                credentials: 'same-origin'
            });
            const d = await r.json();
            if (!d.ok) {
                alert(d.error || 'Error');
                return;
            }
            const tb = document.querySelector('#tablaGustos tbody');
            tb.innerHTML = '';
            d.gustos.forEach(g => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${g.id}</td><td>${g.nombre}</td><td>${g.descripcion||''}</td><td>${g.activo?'Activo':'Inactivo'}</td><td><button class="btn btn-sm btn-outline-primary" onclick='editGusto(${JSON.stringify(g)})'>Editar</button></td>`;
                tb.appendChild(tr);
            });
        }

        function editGusto(g) {
            gustoId.value = g.id;
            gustoNombre.value = g.nombre;
            gustoDesc.value = g.descripcion || '';
            gustoActivo.value = g.activo ? '1' : '0';
        }
        window.editGusto = editGusto;

        formGusto.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = Number(gustoId.value || 0);
            const p = {
                action: id ? 'update' : 'create',
                id,
                nombre: gustoNombre.value,
                descripcion: gustoDesc.value,
                activo: Number(gustoActivo.value) === 1
            };
            const r = await fetch('api/mr/admin/empanada_gustos.php', {
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
            e.target.reset();
            gustoId.value = '';
            loadGustos();
        });

        loadGustos();
    </script>
</body>

</html>