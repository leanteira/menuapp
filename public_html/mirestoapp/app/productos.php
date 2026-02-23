<?php
include('include-header.php');
?>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include('include-menu.php'); ?>
            <div class="layout-page">
                <?php include('include-navbar.php'); ?>
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Productos</h4>
                        </div>

                        <div class="card mb-3">
                            <div class="card-body">
                                <form id="formProducto" class="row g-2">
                                    <input type="hidden" id="prodId">

                                    <div class="col-md-2">
                                        <input class="form-control" id="prodCodigo" placeholder="Código (ej: P0001)">
                                    </div>
                                    <div class="col-md-3">
                                        <input class="form-control" id="prodNombre" placeholder="Nombre" required>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select" id="prodCategoria"></select>
                                    </div>
                                    <div class="col-md-2">
                                        <input class="form-control" id="prodPrecio" type="number" min="0" step="0.01" placeholder="Precio" required>
                                    </div>
                                    <div class="col-md-3">
                                        <input class="form-control" id="prodDesc" placeholder="Descripción">
                                    </div>
                                    <div class="col-md-2">
                                        <select id="prodActivo" class="form-select">
                                            <option value="1">Activo</option>
                                            <option value="0">Inactivo</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" id="prodComboGustos">
                                            <label class="form-check-label" for="prodComboGustos">Combo gustos (docena)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-10 d-grid">
                                        <button class="btn btn-primary" type="submit">Guardar</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="row g-2 mb-3">
                                    <div class="col-md-4">
                                        <input class="form-control" id="filtroProductos" placeholder="Buscar por código, nombre o categoría">
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" id="filtroCategoria">
                                            <option value="">Todas las categorías</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" id="filtroEstado">
                                            <option value="">Todos los estados</option>
                                            <option value="1">Activos</option>
                                            <option value="0">Inactivos</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select" id="filtroCombo">
                                            <option value="">Combo gustos</option>
                                            <option value="1">Solo combos</option>
                                            <option value="0">Sin combo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="tablaProductos">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Código</th>
                                                <th>Nombre</th>
                                                <th>Categoría</th>
                                                <th>Precio</th>
                                                <th>Combo gustos</th>
                                                <th>Estado</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div id="paginacionProductos"></div>
                            </div>
                        </div>
                    </div>

                    <?php include('include-footer.php'); ?>
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalOpcionesProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloOpcionesProducto">Opciones del producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">Variantes</h6>
                                        <button type="button" class="btn btn-sm btn-primary" id="btnNuevaVar">Agregar variante</button>
                                    </div>
                                    <table class="table" id="tablaVariantesModal">
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
                                    <table class="table" id="tablaModificadoresModal">
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
        const userRole = <?php echo json_encode($rol ?? ''); ?>;
        let productosData = [];
        let productoOpcionesActivo = null;
        const modalOpcionesProducto = new bootstrap.Modal(document.getElementById('modalOpcionesProducto'));
        const modalVariante = new bootstrap.Modal(document.getElementById('modalVariante'));
        const modalModificador = new bootstrap.Modal(document.getElementById('modalModificador'));

        // Recuperar filtros guardados
        function getFiltrosSaved() {
            try {
                return JSON.parse(localStorage.getItem('filtrosProductos') || '{}');
            } catch {
                return {};
            }
        }

        function saveFiltros() {
            const filtros = {
                texto: filtroProductos.value || '',
                categoria: filtroCategoria.value || '',
                estado: filtroEstado.value || '',
                combo: filtroCombo.value || ''
            };
            localStorage.setItem('filtrosProductos', JSON.stringify(filtros));
        }

        function loadFiltros() {
            const saved = getFiltrosSaved();
            if (saved.texto) filtroProductos.value = saved.texto;
            if (saved.categoria) filtroCategoria.value = saved.categoria;
            if (saved.estado) filtroEstado.value = saved.estado;
            if (saved.combo) filtroCombo.value = saved.combo;
        }

        function rest() {
            return userRole !== 'superadmin' ? 0 : Number(new URLSearchParams(location.search).get('restaurante_id') || 0);
        }

        function q() {
            const restaurantId = rest();
            return restaurantId > 0 ? ('?restaurante_id=' + restaurantId) : '';
        }

        function money(value) {
            return '$' + Number(value || 0).toLocaleString('es-AR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        async function loadCategorias() {
            const response = await fetch('api/mr/admin/categorias.php' + q(), {
                credentials: 'same-origin'
            });
            const data = await response.json();
            if (!data.ok) {
                alert(data.error || 'Error categorías');
                return;
            }

            prodCategoria.innerHTML = '<option value="">Sin categoría</option>';
            filtroCategoria.innerHTML = '<option value="">Todas las categorías</option>';
            data.categorias.forEach(categoria => {
                const option = document.createElement('option');
                option.value = categoria.id;
                option.textContent = categoria.nombre;
                prodCategoria.appendChild(option);
                const optionFilter = document.createElement('option');
                optionFilter.value = categoria.id;
                optionFilter.textContent = categoria.nombre;
                filtroCategoria.appendChild(optionFilter);
            });
        }

        async function loadProductos() {
            const response = await fetch('api/mr/admin/productos.php' + q(), {
                credentials: 'same-origin'
            });
            const data = await response.json();
            if (!data.ok) {
                alert(data.error || 'Error productos');
                return;
            }

            productosData = data.productos || [];
            renderProductos();
        }

        let paginaActual = 1;
        const itemsPorPagina = 20;

        function renderProductos() {
            const tbody = document.querySelector('#tablaProductos tbody');
            tbody.innerHTML = '';

            const texto = (filtroProductos.value || '').trim().toLowerCase();
            const cat = filtroCategoria.value || '';
            const estado = filtroEstado.value;
            const combo = filtroCombo.value;

            const filtrados = productosData.filter(producto => {
                if (cat && String(producto.categoria_id || '') !== cat) return false;
                if (estado !== '' && String(producto.activo ? 1 : 0) !== estado) return false;
                if (combo !== '' && String(producto.es_combo_gustos ? 1 : 0) !== combo) return false;
                if (!texto) return true;
                const bucket = `${producto.codigo || ''} ${producto.nombre || ''} ${producto.categoria_nombre || ''}`.toLowerCase();
                return bucket.includes(texto);
            });

            const totalPaginas = Math.ceil(filtrados.length / itemsPorPagina);
            if (paginaActual > totalPaginas && totalPaginas > 0) paginaActual = totalPaginas;
            if (paginaActual < 1) paginaActual = 1;

            const inicio = (paginaActual - 1) * itemsPorPagina;
            const fin = inicio + itemsPorPagina;
            const paginados = filtrados.slice(inicio, fin);

            paginados.forEach(producto => {
                const row = document.createElement('tr');
                row.innerHTML = `
				<td>${producto.id}</td>
				<td>${producto.codigo || '-'}</td>
				<td>${producto.nombre}</td>
				<td>${producto.categoria_nombre || '-'}</td>
				<td>${money(producto.precio_base)}</td>
                    <td>${producto.es_combo_gustos ? 'Si' : 'No'}</td>
				<td>${producto.activo ? 'Activo' : 'Inactivo'}</td>
				<td class="d-flex gap-1">
					<button class="btn btn-sm btn-outline-primary" onclick='editProd(${JSON.stringify(producto)})'>Editar</button>
					<button class="btn btn-sm btn-outline-secondary" onclick='abrirOpciones(${JSON.stringify(producto)})'>Opciones</button>
				</td>
			`;
                tbody.appendChild(row);
            });

            renderPaginacion(filtrados.length, totalPaginas);
        }

        function renderPaginacion(total, totalPaginas) {
            const container = document.getElementById('paginacionProductos');
            if (!container) return;

            if (totalPaginas <= 1) {
                container.innerHTML = '';
                return;
            }

            const inicio = (paginaActual - 1) * itemsPorPagina + 1;
            const fin = Math.min(paginaActual * itemsPorPagina, total);

            let html = `<div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">Mostrando ${inicio}-${fin} de ${total} productos</div>
                <nav><ul class="pagination mb-0">`;

            // Anterior
            html += `<li class="page-item ${paginaActual === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="cambiarPagina(${paginaActual - 1}); return false;">Ant</a>
            </li>`;

            // Números de página
            let rangoInicio = Math.max(1, paginaActual - 2);
            let rangoFin = Math.min(totalPaginas, paginaActual + 2);

            if (rangoInicio > 1) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="cambiarPagina(1); return false;">1</a></li>`;
                if (rangoInicio > 2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }

            for (let i = rangoInicio; i <= rangoFin; i++) {
                html += `<li class="page-item ${i === paginaActual ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="cambiarPagina(${i}); return false;">${i}</a>
                </li>`;
            }

            if (rangoFin < totalPaginas) {
                if (rangoFin < totalPaginas - 1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                html += `<li class="page-item"><a class="page-link" href="#" onclick="cambiarPagina(${totalPaginas}); return false;">${totalPaginas}</a></li>`;
            }

            // Siguiente
            html += `<li class="page-item ${paginaActual === totalPaginas ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="cambiarPagina(${paginaActual + 1}); return false;">Sig</a>
            </li>`;

            html += `</ul></nav></div>`;
            container.innerHTML = html;
        }

        window.cambiarPagina = function(pagina) {
            paginaActual = pagina;
            renderProductos();
        };

        function editProd(producto) {
            prodId.value = producto.id;
            prodCodigo.value = producto.codigo || '';
            prodNombre.value = producto.nombre;
            prodCategoria.value = producto.categoria_id || '';
            prodPrecio.value = producto.precio_base;
            prodDesc.value = producto.descripcion || '';
            prodActivo.value = producto.activo ? '1' : '0';
            prodComboGustos.checked = !!producto.es_combo_gustos;
        }

        window.editProd = editProd;

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

        async function loadVariantes() {
            if (!productoOpcionesActivo) return;
            const r = await fetch('api/mr/admin/variantes.php?producto_id=' + productoOpcionesActivo.id, {
                credentials: 'same-origin'
            });
            const d = await r.json();
            const tb = document.querySelector('#tablaVariantesModal tbody');
            tb.innerHTML = '';
            if (!d.ok) return;
            d.variantes.forEach(v => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${v.nombre}</td><td>${Number(v.precio_adicional).toFixed(2)}</td><td class="d-flex gap-1"><button class="btn btn-sm btn-outline-primary" onclick='editarVar(${JSON.stringify(v)})'>Editar</button><button class="btn btn-sm btn-outline-danger" onclick='eliminarVar(${v.id})'>Eliminar</button></td>`;
                tb.appendChild(tr);
            });
        }

        async function loadModificadores() {
            if (!productoOpcionesActivo) return;
            const r = await fetch('api/mr/admin/modificadores.php?producto_id=' + productoOpcionesActivo.id, {
                credentials: 'same-origin'
            });
            const d = await r.json();
            const tb = document.querySelector('#tablaModificadoresModal tbody');
            tb.innerHTML = '';
            if (!d.ok) return;
            d.modificadores.forEach(m => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${m.nombre}</td><td>${Number(m.precio_adicional).toFixed(2)}</td><td>${m.obligatorio ? 'Obligatorio' : 'Opcional'}</td><td class="d-flex gap-1"><button class="btn btn-sm btn-outline-primary" onclick='editarMod(${JSON.stringify(m)})'>Editar</button><button class="btn btn-sm btn-outline-danger" onclick='eliminarMod(${m.id})'>Eliminar</button></td>`;
                tb.appendChild(tr);
            });
        }

        function abrirOpciones(producto) {
            productoOpcionesActivo = producto;
            document.getElementById('tituloOpcionesProducto').textContent = `Opciones · ${producto.nombre}`;
            loadVariantes();
            loadModificadores();
            modalOpcionesProducto.show();
        }
        window.abrirOpciones = abrirOpciones;

        function editarVar(v) {
            varId.value = v.id;
            varNombre.value = v.nombre;
            varPrecio.value = v.precio_adicional;
            document.getElementById('tituloModalVar').textContent = 'Editar variante';
            modalVariante.show();
        }
        window.editarVar = editarVar;

        function editarMod(m) {
            modId.value = m.id;
            modNombre.value = m.nombre;
            modPrecio.value = m.precio_adicional;
            modOblig.value = m.obligatorio ? '1' : '0';
            document.getElementById('tituloModalMod').textContent = 'Editar modificador';
            modalModificador.show();
        }
        window.editarMod = editarMod;

        async function eliminarVar(id) {
            if (!productoOpcionesActivo) return;
            if (!confirm('¿Eliminar esta variante?')) return;
            const payload = {
                action: 'delete',
                id,
                producto_id: productoOpcionesActivo.id
            };
            const r = await fetch('api/mr/admin/variantes.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            const d = await r.json();
            if (!d.ok) {
                alert(d.error || 'Error');
                return;
            }
            loadVariantes();
        }
        window.eliminarVar = eliminarVar;

        async function eliminarMod(id) {
            if (!productoOpcionesActivo) return;
            if (!confirm('¿Eliminar este modificador?')) return;
            const payload = {
                action: 'delete',
                id,
                producto_id: productoOpcionesActivo.id
            };
            const r = await fetch('api/mr/admin/modificadores.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            const d = await r.json();
            if (!d.ok) {
                alert(d.error || 'Error');
                return;
            }
            loadModificadores();
        }
        window.eliminarMod = eliminarMod;

        formProducto.addEventListener('submit', async (event) => {
            event.preventDefault();

            const id = Number(prodId.value || 0);
            const payload = {
                action: id ? 'update' : 'create',
                id,
                codigo: prodCodigo.value,
                nombre: prodNombre.value,
                categoria_id: Number(prodCategoria.value || 0),
                precio_base: Number(prodPrecio.value || 0),
                descripcion: prodDesc.value,
                activo: Number(prodActivo.value) === 1,
                es_combo_gustos: !!prodComboGustos.checked
            };

            if (userRole === 'superadmin') {
                payload.restaurante_id = rest();
            }

            const response = await fetch('api/mr/admin/productos.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            const data = await response.json();

            if (!data.ok) {
                alert(data.error || 'Error');
                return;
            }

            event.target.reset();
            prodId.value = '';
            prodComboGustos.checked = false;
            loadProductos();
        });

        filtroProductos.addEventListener('input', () => {
            paginaActual = 1;
            saveFiltros();
            renderProductos();
        });
        filtroCategoria.addEventListener('change', () => {
            paginaActual = 1;
            saveFiltros();
            renderProductos();
        });
        filtroEstado.addEventListener('change', () => {
            paginaActual = 1;
            saveFiltros();
            renderProductos();
        });
        filtroCombo.addEventListener('change', () => {
            paginaActual = 1;
            saveFiltros();
            renderProductos();
        });

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
            if (!productoOpcionesActivo) return;
            const p = {
                action: Number(varId.value || 0) ? 'update' : 'create',
                id: Number(varId.value || 0),
                producto_id: productoOpcionesActivo.id,
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
            loadVariantes();
        });

        formMod.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!productoOpcionesActivo) return;
            const p = {
                action: Number(modId.value || 0) ? 'update' : 'create',
                id: Number(modId.value || 0),
                producto_id: productoOpcionesActivo.id,
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
            loadModificadores();
        });

        (async () => {
            await loadCategorias();
            loadFiltros();
            await loadProductos();
        })();
    </script>
</body>

</html>