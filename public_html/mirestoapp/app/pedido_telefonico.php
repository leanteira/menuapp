<?php
include('include-header.php');

$mrUser = mr_user();
$defaultRestauranteId = mr_resolve_restaurante_id($mrUser, 0);
?>

<body>
	<style>
		.content-wrapper {
			background: linear-gradient(180deg, #edf2ff 0%, #f4f7ff 55%, #eef3ff 100%);
		}

		.card {
			border: 1px solid #dbe4ff;
			box-shadow: 0 10px 24px rgba(44, 72, 146, 0.08);
		}

		#panelClienteCompacto .badge {
			font-size: 0.75rem;
		}

		#tablaItems thead th,
		#tablaPedidosHoy thead th {
			background: #eef3ff;
			color: #4d5b80;
			font-weight: 700;
		}

		.estado-badge {
			display: inline-flex;
			align-items: center;
			padding: 4px 10px;
			border-radius: 999px;
			font-size: 0.74rem;
			font-weight: 700;
			text-transform: capitalize;
			border: 1px solid transparent;
		}

		.estado-nuevo,
		.estado-confirmado,
		.estado-preparando,
		.estado-listo {
			background: #eef3ff;
			color: #3f5591;
			border-color: #d8e3ff;
		}

		.estado-enviado {
			background: #eef8ff;
			color: #22678a;
			border-color: #cfeeff;
		}

		.estado-entregado {
			background: #ecf9f1;
			color: #2f8758;
			border-color: #c9f0db;
		}

		.estado-cancelado {
			background: #fff1f1;
			color: #b24c4c;
			border-color: #ffd8d8;
		}

		.estado-rapido-btn {
			text-transform: capitalize;
		}

		.producto-row {
			border-bottom: 1px solid #edf0f6;
			padding: 10px 0;
		}

		.producto-row:last-child {
			border-bottom: 0;
		}

		.qty-control {
			width: 38px;
			height: 38px;
			padding: 0;
		}

		.qty-input {
			width: 58px;
			text-align: center;
		}

		.categoria-chip {
			border: 1px solid #d6def7;
			border-radius: 999px;
			background: #f5f8ff;
			color: #355090;
			font-size: 0.84rem;
			padding: 6px 10px;
			cursor: pointer;
			user-select: none;
		}

		.categoria-chip.active {
			background: #5f6bff;
			color: #fff;
			border-color: #5f6bff;
		}

		#listaCoincidenciasCliente .list-group-item {
			cursor: pointer;
		}

		.opcion-item {
			border: 1px solid #dfe5f5;
			border-radius: 10px;
			padding: 10px;
			margin-bottom: 8px;
		}

		.flavor-sample {
			display: inline-block;
			width: 12px;
			height: 12px;
			border-radius: 50%;
			margin-right: 8px;
			vertical-align: middle;
		}

		.gusto-empanada-row {
			display: flex;
			justify-content: space-between;
			align-items: center;
			gap: 10px;
			padding: 8px 10px;
			border: 1px solid #e1e7f7;
			border-radius: 10px;
			margin-bottom: 8px;
			background: #f9fbff;
		}

		.gusto-empanada-qty {
			width: 70px;
			text-align: center;
		}
	</style>

	<div class="layout-wrapper layout-content-navbar">
		<div class="layout-container">
			<?php include('include-menu.php'); ?>
			<div class="layout-page">
				<?php include('include-navbar.php'); ?>
				<div class="content-wrapper">
					<div class="container-xxl flex-grow-1 container-p-y">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h4 class="mb-0">Pedido Telefónico</h4>
						</div>

						<div class="row g-3">
							<div class="col-lg-4">
								<div class="card" id="panelClienteCompacto">
									<div class="card-body">
										<div class="d-flex justify-content-between align-items-center mb-2">
											<h6 class="mb-0">Cliente</h6>
											<span id="estadoClienteBadge" class="badge bg-label-secondary">Sin buscar</span>
										</div>

										<div class="input-group mb-2">
											<input id="telefonoBusqueda" class="form-control" placeholder="Teléfono del cliente">
											<button id="btnBuscarCliente" class="btn btn-primary">Buscar</button>
										</div>

										<div class="small text-muted mb-2">Si hay varias coincidencias, el sistema te deja elegir.</div>

										<div id="clienteDataWrap" class="d-none">
											<input id="clienteNombre" class="form-control mb-2" placeholder="Nombre">
											<input id="clienteTelefono" class="form-control mb-2" placeholder="Teléfono">
											<input id="clienteEmail" class="form-control mb-2" placeholder="Email">
											<input id="clienteDireccion" class="form-control mb-2" placeholder="Dirección">
											<input id="clienteReferencia" class="form-control mb-2" placeholder="Referencia">

											<select id="tipoPedido" class="form-select mb-2">
												<option value="telefono">Telefónico</option>
												<option value="delivery">Delivery</option>
												<option value="retiro">Retiro</option>
											</select>

											<div id="zonaWrap" class="d-none mb-2">
												<select id="zonaSelect" class="form-select">
													<option value="">Seleccionar zona de envío</option>
												</select>
											</div>

											<select id="metodoPago" class="form-select mb-2">
												<option value="contra_entrega">Contra entrega</option>
												<option value="mercadopago">Mercado Pago</option>
											</select>

											<textarea id="observaciones" class="form-control" placeholder="Observaciones"></textarea>

											<hr>
											<div id="historialCliente" class="small text-muted">Sin historial.</div>
										</div>
									</div>
								</div>
							</div>

							<div class="col-lg-8">
								<div class="card mb-3">
									<div class="card-body d-flex justify-content-between align-items-center">
										<div>
											<h6 class="mb-1">Agregar ítems</h6>
											<div class="small text-muted">Carga rápida por categoría, filtro y botones + / -</div>
											<div class="small text-muted">Atajos: <strong>F2</strong> productos · <strong>F4</strong> teléfono · <strong>Alt+B</strong> buscar cliente · <strong>Ctrl+Enter</strong> crear pedido</div>
										</div>
										<button id="btnAbrirModalProductos" class="btn btn-primary">Agregar productos</button>
									</div>
								</div>

								<div class="card">
									<div class="card-body">
										<table class="table" id="tablaItems">
											<thead>
												<tr>
													<th>Producto</th>
													<th style="width: 120px;">Cant</th>
													<th>Detalle</th>
													<th>Subtotal</th>
													<th></th>
												</tr>
											</thead>
											<tbody></tbody>
										</table>

										<div class="d-flex justify-content-between">
											<strong>Total</strong>
											<strong id="totalPedido">$ 0.00</strong>
										</div>

										<div class="mt-3 d-grid">
											<button id="btnCrearPedido" class="btn btn-success">Crear pedido</button>
										</div>
										<div id="msgEstado" class="mt-2 small"></div>
									</div>
								</div>

								<div class="card mt-3">
									<div class="card-body">
										<div class="d-flex justify-content-between align-items-center mb-2">
											<div>
												<h6 class="mb-0">Pedidos de hoy</h6>
												<div class="small text-muted">Seguimiento rápido de pendientes y entregados</div>
											</div>
											<div class="d-flex gap-2 align-items-center">
												<span id="resumenPedidosHoy" class="small text-muted">Cargando...</span>
												<button id="btnRefrescarPedidosHoy" class="btn btn-sm btn-outline-primary">Refrescar</button>
											</div>
										</div>

										<div class="table-responsive">
											<table class="table table-sm table-hover" id="tablaPedidosHoy">
												<thead>
													<tr>
														<th>#</th>
														<th>Hora</th>
														<th>Cliente</th>
														<th>Tipo</th>
														<th>Total</th>
														<th>Estado / Acción</th>
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

					<div class="modal fade" id="modalSeleccionCliente" tabindex="-1" aria-hidden="true">
						<div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title">Seleccionar cliente</h5>
									<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body">
									<p class="text-muted mb-2">Se encontraron varias coincidencias para ese teléfono.</p>
									<div id="listaCoincidenciasCliente" class="list-group"></div>
								</div>
							</div>
						</div>
					</div>

					<div class="modal fade" id="modalAgregarProductos" tabindex="-1" aria-hidden="true">
						<div class="modal-dialog modal-xl modal-dialog-scrollable">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title">Agregar productos al pedido</h5>
									<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body">
									<div class="row g-2 mb-3">
										<div class="col-md-8">
											<input id="filtroProductoRapido" class="form-control" placeholder="Buscar por código, nombre o categoría...">
										</div>
										<div class="col-md-4 d-flex align-items-center">
											<span class="small text-muted" id="resumenFiltroProductos">Todos los productos</span>
										</div>
									</div>

									<div class="d-flex flex-wrap gap-2 mb-3" id="categoriasProductosWrap"></div>

									<div id="listadoProductosModal"></div>
								</div>
							</div>
						</div>
					</div>

					<div class="modal fade" id="modalOpcionesProducto" tabindex="-1" aria-hidden="true">
						<div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="opcionesProductoTitulo">Opciones del producto</h5>
									<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body" id="opcionesProductoBody"></div>
								<div class="modal-footer">
									<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
									<button type="button" class="btn btn-primary" id="btnConfirmarOpcionesProducto">Agregar al pedido</button>
								</div>
							</div>
						</div>
					</div>

					<div class="modal fade" id="modalEstadoPedido" tabindex="-1" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title">Actualizar estado del pedido</h5>
									<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body">
									<div class="small text-muted mb-2" id="estadoPedidoMeta">Pedido</div>
									<label class="form-label" for="estadoPedidoSelect">Estado</label>
									<select id="estadoPedidoSelect" class="form-select mb-3"></select>

									<div class="small text-muted mb-2">Cambios rápidos</div>
									<div class="d-flex flex-wrap gap-2" id="estadosRapidosWrap"></div>
									<div id="estadoPedidoMsg" class="small mt-3"></div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
									<button type="button" class="btn btn-primary" id="btnGuardarEstadoPedido">Guardar estado</button>
								</div>
							</div>
						</div>
					</div>

					<?php include('include-footer.php'); ?>
					<div class="content-backdrop fade"></div>
				</div>
			</div>
		</div>
	</div>

	<?php include('include-js-import.php'); ?>
	<script>
		const userRole = <?php echo json_encode($rol ?? ''); ?>;
		const items = [];
		let productos = [];
		let categoriaActiva = '';
		let modalClientes = null;
		let modalProductos = null;
		let modalOpcionesProducto = null;
		let modalEstadoPedido = null;
		const opcionesCache = {};
		const gustosCache = {};
		const gustosEmpanadaCache = {};
		let pendingItemContext = null;
		let pedidoEstadoActivo = null;
		const estadosPedido = ['nuevo', 'confirmado', 'preparando', 'listo', 'enviado', 'entregado', 'cancelado'];

		const defaultRestauranteId = Number(<?php echo json_encode((int) $defaultRestauranteId); ?> || 0);

		function rest() {
			if (userRole !== 'superadmin') return 0;
			const fromQuery = Number(new URLSearchParams(location.search).get('restaurante_id') || 0);
			return fromQuery > 0 ? fromQuery : defaultRestauranteId;
		}

		function withQ(url) {
			const restaurantId = rest();
			if (restaurantId <= 0) return url;
			return url + (url.includes('?') ? '&' : '?') + 'restaurante_id=' + restaurantId;
		}

		function withRestauranteIfNeeded(url) {
			const restaurantId = rest();
			if (restaurantId <= 0) return url;
			return url + (url.includes('?') ? '&' : '?') + 'restaurante_id=' + restaurantId;
		}

		function money(value) {
			return '$' + Number(value || 0).toLocaleString('es-AR', {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2
			});
		}

		function escapeHtml(text) {
			return String(text || '')
				.replace(/&/g, '&amp;')
				.replace(/</g, '&lt;')
				.replace(/>/g, '&gt;')
				.replace(/"/g, '&quot;')
				.replace(/'/g, '&#039;');
		}

		function estadoClass(estado) {
			const normalized = String(estado || '').toLowerCase();
			if (estadosPedido.includes(normalized)) {
				return 'estado-' + normalized;
			}
			return 'estado-nuevo';
		}

		function estadoLabel(estado) {
			const normalized = String(estado || '').toLowerCase();
			if (!normalized) return '-';
			return normalized.charAt(0).toUpperCase() + normalized.slice(1);
		}

		function estadoSiguiente(estadoActual) {
			const normalized = String(estadoActual || '').toLowerCase();
			const index = estadosPedido.indexOf(normalized);
			if (index === -1 || index >= estadosPedido.length - 1) return normalized || 'nuevo';
			return estadosPedido[index + 1];
		}

		function isIceCreamProduct(nombreProducto) {
			const normalized = String(nombreProducto || '').toLowerCase();
			return normalized.includes('helado') || normalized.includes('ice cream');
		}

		function updateGustosCounter() {
			const counter = document.getElementById('gustosCounter');
			if (!counter) return;

			const selected = document.querySelectorAll('input[name="opcionGusto"]:checked').length;
			counter.textContent = `${selected}/3 seleccionados`;
			counter.className = 'small ' + (selected > 3 ? 'text-danger' : 'text-muted');
		}

		function updateGustosEmpanadaCounter() {
			const counter = document.getElementById('gustosEmpanadaCounter');
			if (!counter) return;

			const inputs = document.querySelectorAll('input[name="opcionGustoEmpanada"]');
			let total = 0;
			inputs.forEach(input => {
				total += Math.max(0, Number(input.value || 0));
			});
			counter.textContent = `${total}/12 unidades`;
			counter.className = 'small ' + (total === 12 ? 'text-success' : 'text-muted');
		}

		function setClienteVisible(visible) {
			const wrap = document.getElementById('clienteDataWrap');
			if (visible) {
				wrap.classList.remove('d-none');
			} else {
				wrap.classList.add('d-none');
			}
		}

		function setBadgeCliente(text, type) {
			const badge = document.getElementById('estadoClienteBadge');
			badge.className = 'badge';
			if (type === 'ok') badge.classList.add('bg-label-success');
			else if (type === 'new') badge.classList.add('bg-label-warning');
			else badge.classList.add('bg-label-secondary');
			badge.textContent = text;
		}

		function renderCategorias() {
			const wrap = document.getElementById('categoriasProductosWrap');
			const categoriasUnicas = [];
			const seen = new Set();

			productos.forEach(producto => {
				const categoria = (producto.categoria_nombre || 'Sin categoría').trim();
				if (!seen.has(categoria)) {
					seen.add(categoria);
					categoriasUnicas.push(categoria);
				}
			});

			const chips = ['Todas', ...categoriasUnicas].map(categoria => {
				const value = categoria === 'Todas' ? '' : categoria;
				const activeClass = categoriaActiva === value ? 'active' : '';
				return `<button type="button" class="categoria-chip ${activeClass}" data-cat="${escapeHtml(value)}">${escapeHtml(categoria)}</button>`;
			}).join('');

			wrap.innerHTML = chips;

			wrap.querySelectorAll('.categoria-chip').forEach(button => {
				button.addEventListener('click', () => {
					categoriaActiva = button.getAttribute('data-cat') || '';
					renderCategorias();
					renderProductosModal();
				});
			});
		}

		function getProductosFiltrados() {
			const filtro = (document.getElementById('filtroProductoRapido').value || '').trim().toLowerCase();

			return productos.filter(producto => {
				const categoria = (producto.categoria_nombre || 'Sin categoría').trim();
				if (categoriaActiva && categoria !== categoriaActiva) {
					return false;
				}

				if (!filtro) {
					return true;
				}

				const texto = `${producto.codigo || ''} ${producto.nombre} ${categoria} ${producto.descripcion || ''}`.toLowerCase();
				return texto.includes(filtro);
			});
		}

		function renderProductosModal() {
			const contenedor = document.getElementById('listadoProductosModal');
			const lista = getProductosFiltrados();

			document.getElementById('resumenFiltroProductos').textContent = `${lista.length} producto(s)`;

			if (!lista.length) {
				contenedor.innerHTML = '<div class="alert alert-secondary mb-0">No hay productos con ese filtro.</div>';
				return;
			}

			contenedor.innerHTML = lista.map(producto => `
			<div class="producto-row" data-producto-id="${producto.id}">
				<div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
					<div>
						<div class="fw-semibold">${escapeHtml(producto.nombre)} ${producto.codigo ? `<span class="badge bg-label-primary ms-1">${escapeHtml(producto.codigo)}</span>` : ''}</div>
						<div class="small text-muted">${escapeHtml(producto.categoria_nombre || 'Sin categoría')} · ${money(producto.precio_base)}</div>
					</div>
					<div class="d-flex align-items-center gap-1">
						<button type="button" class="btn btn-outline-secondary qty-control btn-restar" data-pid="${producto.id}">-</button>
						<input type="number" min="1" value="1" class="form-control qty-input" id="qty_${producto.id}">
						<button type="button" class="btn btn-outline-secondary qty-control btn-sumar" data-pid="${producto.id}">+</button>
						<button type="button" class="btn btn-primary btn-agregar-producto" data-pid="${producto.id}">Agregar</button>
					</div>
				</div>
			</div>
		`).join('');

			contenedor.querySelectorAll('.btn-restar').forEach(btn => {
				btn.addEventListener('click', () => {
					const productId = btn.getAttribute('data-pid');
					const input = document.getElementById(`qty_${productId}`);
					const current = Number(input.value || 1);
					input.value = Math.max(1, current - 1);
				});
			});

			contenedor.querySelectorAll('.btn-sumar').forEach(btn => {
				btn.addEventListener('click', () => {
					const productId = btn.getAttribute('data-pid');
					const input = document.getElementById(`qty_${productId}`);
					const current = Number(input.value || 1);
					input.value = current + 1;
				});
			});

			contenedor.querySelectorAll('.btn-agregar-producto').forEach(btn => {
				btn.addEventListener('click', () => {
					const productId = Number(btn.getAttribute('data-pid') || 0);
					const input = document.getElementById(`qty_${productId}`);
					const cantidad = Math.max(1, Number(input.value || 1));
					agregarItemPorProducto(productId, cantidad);
				});
			});

			contenedor.querySelectorAll('.qty-input').forEach(input => {
				input.addEventListener('keydown', (event) => {
					if (event.key !== 'Enter') return;
					event.preventDefault();
					const productId = Number((input.id || '').replace('qty_', '') || 0);
					const cantidad = Math.max(1, Number(input.value || 1));
					agregarItemPorProducto(productId, cantidad);
				});
			});
		}

		function abrirModalProductosYFoco() {
			modalProductos.show();
			setTimeout(() => {
				const filtro = document.getElementById('filtroProductoRapido');
				if (filtro) filtro.focus();
			}, 120);
		}

		function agregarPrimerProductoFiltrado() {
			const lista = getProductosFiltrados();
			if (!lista.length) {
				return;
			}

			const primero = lista[0];
			const qtyInput = document.getElementById(`qty_${primero.id}`);
			const cantidad = Math.max(1, Number(qtyInput?.value || 1));
			agregarItemPorProducto(primero.id, cantidad);
		}

		async function cargarOpcionesProducto(productoId) {
			if (opcionesCache[productoId]) {
				return opcionesCache[productoId];
			}

			const [responseVariantes, responseMods] = await Promise.all([
				fetch('api/mr/admin/variantes.php?producto_id=' + productoId, {
					credentials: 'same-origin'
				}),
				fetch('api/mr/admin/modificadores.php?producto_id=' + productoId, {
					credentials: 'same-origin'
				})
			]);

			const dataVariantes = await responseVariantes.json();
			const dataMods = await responseMods.json();

			const opciones = {
				variantes: dataVariantes.ok ? (dataVariantes.variantes || []) : [],
				modificadores: dataMods.ok ? (dataMods.modificadores || []) : []
			};

			opcionesCache[productoId] = opciones;
			return opciones;
		}

		async function cargarGustosHelado(productoId) {
			if (gustosCache[productoId]) {
				return gustosCache[productoId];
			}

			try {
				const response = await fetch('api/mr/helado_gustos.php', {
					credentials: 'same-origin'
				});
				const data = await response.json();
				const gustos = data.ok && Array.isArray(data.gustos) ? data.gustos : [];
				gustosCache[productoId] = gustos;
				return gustos;
			} catch (_) {
				gustosCache[productoId] = [];
				return [];
			}
		}

		async function cargarGustosEmpanada(productoId) {
			if (gustosEmpanadaCache[productoId]) {
				return gustosEmpanadaCache[productoId];
			}

			try {
				const response = await fetch('api/mr/empanada_gustos.php', {
					credentials: 'same-origin'
				});
				const data = await response.json();
				const gustos = data.ok && Array.isArray(data.gustos) ? data.gustos : [];
				gustosEmpanadaCache[productoId] = gustos;
				return gustos;
			} catch (_) {
				gustosEmpanadaCache[productoId] = [];
				return [];
			}
		}

		function agregarItemAlPedido(producto, cantidad, varianteSeleccionada = null, modificadoresSeleccionados = [], gustosSeleccionados = [], gustosEmpanadaSeleccionados = []) {
			let precioUnitario = Number(producto.precio_base || 0);
			const detalle = [];

			if (varianteSeleccionada) {
				precioUnitario += Number(varianteSeleccionada.precio_adicional || 0);
				detalle.push(varianteSeleccionada.nombre);
			}

			modificadoresSeleccionados.forEach(modificador => {
				precioUnitario += Number(modificador.precio_adicional || 0);
				detalle.push(modificador.nombre);
			});

			if (gustosSeleccionados.length) {
				detalle.push('Gustos: ' + gustosSeleccionados.map(g => g.nombre).join(', '));
			}

			if (gustosEmpanadaSeleccionados.length) {
				detalle.push('Empanadas: ' + gustosEmpanadaSeleccionados.map(g => `${g.nombre} x${g.cantidad}`).join(', '));
			}

			items.push({
				producto_id: producto.id,
				nombre: producto.codigo ? `${producto.codigo} · ${producto.nombre}` : producto.nombre,
				cantidad,
				variante_id: varianteSeleccionada ? Number(varianteSeleccionada.id) : null,
				modificadores: modificadoresSeleccionados.map(modificador => Number(modificador.id)),
				gustos: gustosSeleccionados.map(g => Number(g.id || 0)).filter(id => id > 0),
				gustos_empanada: gustosEmpanadaSeleccionados.map(g => ({
					id: Number(g.id || 0),
					cantidad: Number(g.cantidad || 0)
				})).filter(g => g.id > 0 && g.cantidad > 0),
				detalle: detalle.length ? detalle.join(', ') : '-',
				subtotal: precioUnitario * cantidad,
			});

			renderItems();
		}

		async function abrirModalOpcionesProducto(producto, cantidad, opciones) {
			pendingItemContext = {
				producto,
				cantidad,
				opciones
			};

			document.getElementById('opcionesProductoTitulo').textContent = `${producto.nombre}${producto.codigo ? ` (${producto.codigo})` : ''}`;
			const body = document.getElementById('opcionesProductoBody');
			let html = `<div class="small text-muted mb-2">Precio base: <strong>${money(producto.precio_base)}</strong> · Cantidad: <strong>${cantidad}</strong></div>`;

			if (opciones.variantes.length > 0) {
				html += `<h6>Variantes</h6>`;
				html += opciones.variantes.map(variante => `
					<label class="opcion-item d-flex justify-content-between align-items-center">
						<div>
							<input type="radio" name="opcionVariante" value="${variante.id}">
							<span class="ms-2">${escapeHtml(variante.nombre)}</span>
						</div>
						<span>${money(variante.precio_adicional)}</span>
					</label>
				`).join('');
			}

			if (opciones.modificadores.length > 0) {
				html += `<h6 class="mt-3">Modificadores</h6>`;
				html += opciones.modificadores.map(modificador => `
					<label class="opcion-item d-flex justify-content-between align-items-center">
						<div>
							<input type="checkbox" name="opcionModificador" value="${modificador.id}">
							<span class="ms-2">${escapeHtml(modificador.nombre)}</span>
						</div>
						<span>${money(modificador.precio_adicional)}</span>
					</label>
				`).join('');
			}

			if (producto.es_combo_gustos) {
				const gustosEmpanada = await cargarGustosEmpanada(producto.id);
				html += `<div class="d-flex justify-content-between align-items-center mt-3 mb-2"><h6 class="mb-0">Armar docena de empanadas</h6><span id="gustosEmpanadaCounter" class="small text-muted">0/12 unidades</span></div>`;

				if (!gustosEmpanada.length) {
					html += `<div class="small text-muted">No hay gustos disponibles para empanadas.</div>`;
				} else {
					html += gustosEmpanada.map(gusto => `
						<div class="gusto-empanada-row">
							<div>
								<div class="fw-semibold">${escapeHtml(gusto.nombre)}</div>
								<div class="small text-muted">${escapeHtml(gusto.descripcion || '')}</div>
							</div>
							<div class="d-flex align-items-center gap-1">
								<button type="button" class="btn btn-outline-secondary btn-sm btn-gusto-emp-restar" data-gusto-id="${gusto.id}">-</button>
								<input type="number" min="0" value="0" class="form-control gusto-empanada-qty" name="opcionGustoEmpanada" data-gusto-id="${gusto.id}" data-gusto-nombre="${escapeHtml(gusto.nombre)}">
								<button type="button" class="btn btn-outline-secondary btn-sm btn-gusto-emp-sumar" data-gusto-id="${gusto.id}">+</button>
							</div>
						</div>
					`).join('');
				}
			}

			if (isIceCreamProduct(producto.nombre)) {
				const gustos = await cargarGustosHelado(producto.id);
				html += `<div class="d-flex justify-content-between align-items-center mt-3 mb-1"><h6 class="mb-0">Gustos de helado (elegí de 1 a 3)</h6><span id="gustosCounter" class="small text-muted">0/3 seleccionados</span></div>`;

				if (!gustos.length) {
					html += `<div class="small text-muted">No hay gustos disponibles.</div>`;
				} else {
					html += gustos.map(gusto => {
						const color = gusto.color_hex || '#d1d5db';
						return `
							<label class="opcion-item d-flex justify-content-between align-items-center">
								<div>
									<input type="checkbox" name="opcionGusto" value="${gusto.id}" data-gusto-nombre="${escapeHtml(gusto.nombre)}">
									<span class="ms-2"><span class="flavor-sample" style="background-color:${escapeHtml(color)}"></span>${escapeHtml(gusto.nombre)}</span>
								</div>
								<span class="small text-muted">sin cargo</span>
							</label>
						`;
					}).join('');
				}
			}

			body.innerHTML = html;

			body.querySelectorAll('input[name="opcionGusto"]').forEach(input => {
				input.addEventListener('change', () => {
					const selected = body.querySelectorAll('input[name="opcionGusto"]:checked').length;
					if (selected > 3) {
						input.checked = false;
						alert('Podés elegir hasta 3 gustos.');
					}
					updateGustosCounter();
				});
			});

			body.querySelectorAll('.btn-gusto-emp-restar').forEach(btn => {
				btn.addEventListener('click', () => {
					const gustoId = btn.dataset.gustoId;
					const input = body.querySelector(`input[name="opcionGustoEmpanada"][data-gusto-id="${gustoId}"]`);
					const current = Number(input?.value || 0);
					input.value = Math.max(0, current - 1);
					updateGustosEmpanadaCounter();
				});
			});

			body.querySelectorAll('.btn-gusto-emp-sumar').forEach(btn => {
				btn.addEventListener('click', () => {
					const gustoId = btn.dataset.gustoId;
					const input = body.querySelector(`input[name="opcionGustoEmpanada"][data-gusto-id="${gustoId}"]`);
					const current = Number(input?.value || 0);
					input.value = current + 1;
					updateGustosEmpanadaCounter();
				});
			});

			body.querySelectorAll('input[name="opcionGustoEmpanada"]').forEach(input => {
				input.addEventListener('input', () => {
					if (Number(input.value || 0) < 0) input.value = 0;
					updateGustosEmpanadaCounter();
				});
			});

			updateGustosCounter();
			updateGustosEmpanadaCounter();
			modalOpcionesProducto.show();
		}

		function agregarItemPorProducto(productoId, cantidad) {
			const producto = productos.find(item => Number(item.id) === Number(productoId));
			if (!producto || cantidad <= 0) {
				return;
			}

			cargarOpcionesProducto(producto.id).then(opciones => {
				const esHelado = isIceCreamProduct(producto.nombre);
				const esComboGustos = !!producto.es_combo_gustos;
				if (!opciones.variantes.length && !opciones.modificadores.length && !esHelado && !esComboGustos) {
					agregarItemAlPedido(producto, cantidad, null, [], [], []);
					return;
				}

				abrirModalOpcionesProducto(producto, cantidad, opciones).catch(() => {
					alert('No se pudieron cargar opciones del producto.');
				});
			}).catch(() => {
				alert('No se pudieron cargar variantes/modificadores.');
			});
		}

		function resetCallCenterForm() {
			document.getElementById('telefonoBusqueda').value = '';
			setBadgeCliente('Sin buscar', 'idle');
			setClienteVisible(false);

			document.getElementById('clienteNombre').value = '';
			document.getElementById('clienteTelefono').value = '';
			document.getElementById('clienteEmail').value = '';
			document.getElementById('clienteDireccion').value = '';
			document.getElementById('clienteReferencia').value = '';
			document.getElementById('historialCliente').textContent = 'Sin historial.';

			document.getElementById('tipoPedido').value = 'telefono';
			document.getElementById('metodoPago').value = 'contra_entrega';
			document.getElementById('observaciones').value = '';
			document.getElementById('zonaSelect').value = '';
			document.getElementById('zonaWrap').classList.add('d-none');

			setTimeout(() => {
				document.getElementById('telefonoBusqueda')?.focus();
			}, 120);
		}

		function renderItems() {
			const tbody = document.querySelector('#tablaItems tbody');
			tbody.innerHTML = '';

			let total = 0;
			items.forEach((item, index) => {
				total += Number(item.subtotal || 0);
				const row = document.createElement('tr');
				row.innerHTML = `
				<td>${escapeHtml(item.nombre)}</td>
				<td>
					<div class="d-flex align-items-center gap-1">
						<button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.cambiarCantidadItem(${index}, -1)">-</button>
						<span class="fw-semibold">${item.cantidad}</span>
						<button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.cambiarCantidadItem(${index}, 1)">+</button>
					</div>
				</td>
				<td>${escapeHtml(item.detalle || '-')}</td>
				<td>${money(item.subtotal)}</td>
				<td><button class="btn btn-sm btn-outline-danger" onclick="window.removeItem(${index})">Quitar</button></td>
			`;
				tbody.appendChild(row);
			});

			document.getElementById('totalPedido').textContent = money(total);
		}

		window.removeItem = (index) => {
			items.splice(index, 1);
			renderItems();
		};

		window.cambiarCantidadItem = (index, delta) => {
			const item = items[index];
			if (!item) return;
			const nuevaCantidad = item.cantidad + delta;
			if (nuevaCantidad <= 0) return;

			const precioUnitario = Number(item.subtotal) / Number(item.cantidad || 1);
			item.cantidad = nuevaCantidad;
			item.subtotal = precioUnitario * nuevaCantidad;
			renderItems();
		};

		async function cargarProductos() {
			const response = await fetch(withQ('api/mr/admin/productos.php'), {
				credentials: 'same-origin'
			});
			const data = await response.json();
			if (!data.ok) {
				alert(data.error || 'Error al cargar productos');
				return;
			}

			productos = (data.productos || []).filter(producto => producto.activo !== false);
			renderCategorias();
			renderProductosModal();
		}

		async function cargarZonas() {
			const response = await fetch(withQ('api/mr/admin/zonas.php'), {
				credentials: 'same-origin'
			});
			const data = await response.json();
			if (!data.ok) {
				return;
			}

			const select = document.getElementById('zonaSelect');
			select.innerHTML = '<option value="">Seleccionar zona de envío</option>';

			(data.zonas || []).filter(zona => zona.activo).forEach(zona => {
				const option = document.createElement('option');
				option.value = zona.id;
				option.textContent = `${zona.nombre} · Envío ${money(zona.costo_envio)}`;
				select.appendChild(option);
			});
		}

		function aplicarCliente(clienteData) {
			setClienteVisible(true);
			setBadgeCliente('Cliente encontrado', 'ok');

			document.getElementById('clienteNombre').value = clienteData.cliente?.nombre || '';
			document.getElementById('clienteTelefono').value = clienteData.cliente?.telefono || '';
			document.getElementById('clienteEmail').value = clienteData.cliente?.email || '';

			const primeraDireccion = (clienteData.direcciones || [])[0] || null;
			document.getElementById('clienteDireccion').value = primeraDireccion?.direccion || '';
			document.getElementById('clienteReferencia').value = primeraDireccion?.referencia || '';

			const historial = clienteData.historial || [];
			document.getElementById('historialCliente').innerHTML = historial.length ?
				historial.map(item => `#${item.id} · ${item.estado} · ${money(item.total)} · ${item.created_at}`).join('<br>') :
				'Sin historial';
		}

		function prepararClienteNuevo(telefono) {
			setClienteVisible(true);
			setBadgeCliente('Cliente nuevo', 'new');

			document.getElementById('clienteNombre').value = '';
			document.getElementById('clienteTelefono').value = telefono || '';
			document.getElementById('clienteEmail').value = '';
			document.getElementById('clienteDireccion').value = '';
			document.getElementById('clienteReferencia').value = '';
			document.getElementById('historialCliente').textContent = 'Cliente nuevo, sin historial.';
		}

		function abrirModalCoincidencias(matches) {
			const lista = document.getElementById('listaCoincidenciasCliente');
			lista.innerHTML = '';

			matches.forEach(match => {
				const button = document.createElement('button');
				button.type = 'button';
				button.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
				button.innerHTML = `
				<div>
					<div class="fw-semibold">${escapeHtml(match.nombre || '(Sin nombre)')}</div>
					<div class="small text-muted">${escapeHtml(match.telefono || '')} · ${escapeHtml(match.email || '')}</div>
				</div>
				<span class="badge bg-primary">Seleccionar</span>
			`;
				button.addEventListener('click', async () => {
					await seleccionarClientePorId(Number(match.id));
					modalClientes.hide();
				});
				lista.appendChild(button);
			});

			modalClientes.show();
		}

		async function seleccionarClientePorId(clienteId) {
			const response = await fetch(withQ(`api/mr/admin/cliente_telefono.php?cliente_id=${clienteId}`), {
				credentials: 'same-origin'
			});
			const data = await response.json();
			if (!data.ok) {
				alert(data.error || 'No se pudo cargar el cliente');
				return;
			}

			if (!data.cliente) {
				return;
			}

			aplicarCliente(data);
		}

		async function buscarCliente() {
			const telefono = document.getElementById('telefonoBusqueda').value.trim();
			if (!telefono) {
				alert('Ingresá teléfono');
				return;
			}

			const response = await fetch(withQ('api/mr/admin/cliente_telefono.php?telefono=' + encodeURIComponent(telefono)), {
				credentials: 'same-origin'
			});
			const data = await response.json();

			if (!data.ok) {
				alert(data.error || 'Error buscando cliente');
				return;
			}

			const matches = data.matches || [];
			if (data.cliente) {
				aplicarCliente(data);
				return;
			}

			if (matches.length > 1) {
				abrirModalCoincidencias(matches);
				return;
			}

			prepararClienteNuevo(telefono);
		}

		async function crearPedido() {
			const msgEstado = document.getElementById('msgEstado');
			msgEstado.className = 'mt-2 small';

			if (!items.length) {
				msgEstado.classList.add('text-danger');
				msgEstado.textContent = 'Agregá al menos un ítem.';
				return;
			}

			if (document.getElementById('clienteDataWrap').classList.contains('d-none')) {
				msgEstado.classList.add('text-danger');
				msgEstado.textContent = 'Buscá o cargá un cliente antes de crear el pedido.';
				return;
			}

			const tipoPedido = document.getElementById('tipoPedido').value;
			const zonaId = Number(document.getElementById('zonaSelect').value || 0);
			if (tipoPedido === 'delivery' && zonaId <= 0) {
				msgEstado.classList.add('text-danger');
				msgEstado.textContent = 'Seleccioná una zona de envío para delivery.';
				return;
			}

			const payload = {
				tipo: tipoPedido,
				metodo_pago: document.getElementById('metodoPago').value,
				observaciones: document.getElementById('observaciones').value,
				cliente: {
					nombre: document.getElementById('clienteNombre').value,
					telefono: document.getElementById('clienteTelefono').value,
					email: document.getElementById('clienteEmail').value,
				},
				direccion: {
					direccion: document.getElementById('clienteDireccion').value,
					referencia: document.getElementById('clienteReferencia').value,
				},
				zona_id: tipoPedido === 'delivery' ? zonaId : 0,
				items: items.map(item => ({
					producto_id: item.producto_id,
					cantidad: item.cantidad,
					variante_id: item.variante_id,
					modificadores: item.modificadores,
					gustos: item.gustos || [],
					gustos_empanada: item.gustos_empanada || [],
				})),
			};

			if (userRole === 'superadmin') {
				payload.restaurante_id = rest();
			}

			const response = await fetch('api/mr/admin/pedido_manual.php', {
				method: 'POST',
				credentials: 'same-origin',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify(payload),
			});
			const data = await response.json();

			if (!data.ok) {
				msgEstado.classList.add('text-danger');
				msgEstado.textContent = data.error || 'No se pudo crear el pedido';
				return;
			}

			if (payload.metodo_pago === 'mercadopago') {
				const paymentResponse = await fetch('api/mr/payments/mercadopago_preference.php', {
					method: 'POST',
					credentials: 'same-origin',
					headers: {
						'Content-Type': 'application/json'
					},
					body: JSON.stringify({
						pedido_id: data.pedido_id
					}),
				});

				const paymentData = await paymentResponse.json();
				if (paymentData.ok && (paymentData.init_point || paymentData.sandbox_init_point)) {
					window.open(paymentData.init_point || paymentData.sandbox_init_point, '_blank');
				}
			}

			msgEstado.classList.add('text-success');
			msgEstado.textContent = `Pedido #${data.pedido_id} creado correctamente.`;
			items.length = 0;
			renderItems();
			resetCallCenterForm();
			cargarPedidosHoy();
		}

		function formatHora(fechaTexto) {
			if (!fechaTexto) return '-';
			const parts = String(fechaTexto).split(' ');
			if (parts.length < 2) return fechaTexto;
			return parts[1].slice(0, 5);
		}

		function abrirModalEstadoPedido(pedido) {
			pedidoEstadoActivo = pedido;
			const meta = document.getElementById('estadoPedidoMeta');
			const select = document.getElementById('estadoPedidoSelect');
			const rapidosWrap = document.getElementById('estadosRapidosWrap');
			const msg = document.getElementById('estadoPedidoMsg');
			const sugerido = estadoSiguiente(pedido.estado);

			meta.textContent = `Pedido #${pedido.id} · ${pedido.cliente?.nombre || 'Sin cliente'}`;
			msg.className = 'small mt-3';
			msg.textContent = '';

			select.innerHTML = estadosPedido.map(estado =>
				`<option value="${estado}" ${estado === sugerido ? 'selected' : ''}>${estadoLabel(estado)}</option>`
			).join('');

			rapidosWrap.innerHTML = estadosPedido.map(estado =>
				`<button type="button" class="btn btn-sm btn-outline-primary estado-rapido-btn js-estado-rapido ${estado === sugerido ? 'active' : ''}" data-estado="${estado}">${estadoLabel(estado)}</button>`
			).join('');

			rapidosWrap.querySelectorAll('.js-estado-rapido').forEach(button => {
				button.addEventListener('click', () => {
					select.value = button.dataset.estado || '';
					rapidosWrap.querySelectorAll('.js-estado-rapido').forEach(btn => btn.classList.remove('active'));
					button.classList.add('active');
				});
			});

			modalEstadoPedido.show();
		}

		async function actualizarEstadoPedido(pedidoId, estadoNuevo) {
			const response = await fetch('api/mr/admin/pedido_estado.php', {
				method: 'POST',
				credentials: 'same-origin',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({
					pedido_id: Number(pedidoId),
					estado: estadoNuevo
				})
			});

			const data = await response.json();
			if (!data.ok) {
				const msg = document.getElementById('estadoPedidoMsg');
				if (msg) {
					msg.className = 'small mt-3 text-danger';
					msg.textContent = data.error || 'No se pudo actualizar el estado.';
				} else {
					alert(data.error || 'No se pudo actualizar el estado.');
				}
				return;
			}

			const msg = document.getElementById('estadoPedidoMsg');
			if (msg) {
				msg.className = 'small mt-3 text-success';
				msg.textContent = 'Estado actualizado correctamente.';
			}

			if (modalEstadoPedido) {
				setTimeout(() => modalEstadoPedido.hide(), 350);
			}

			cargarPedidosHoy();
		}

		async function cargarPedidosHoy() {
			const tbody = document.querySelector('#tablaPedidosHoy tbody');
			const resumen = document.getElementById('resumenPedidosHoy');
			if (!tbody || !resumen) return;

			const urlBase = 'api/mr/admin/pedidos.php?hoy=1&limit=100';
			const url = withRestauranteIfNeeded(urlBase);

			const response = await fetch(url, {
				credentials: 'same-origin'
			});
			const data = await response.json();

			if (!data.ok) {
				resumen.textContent = 'Error al cargar';
				tbody.innerHTML = '<tr><td colspan="6" class="text-danger small">No se pudieron cargar los pedidos de hoy.</td></tr>';
				return;
			}

			const pedidos = data.pedidos || [];
			const pendientes = pedidos.filter(p => p.estado !== 'entregado' && p.estado !== 'cancelado').length;
			const entregados = pedidos.filter(p => p.estado === 'entregado').length;
			resumen.textContent = `${pedidos.length} totales · ${pendientes} pendientes · ${entregados} entregados`;

			if (!pedidos.length) {
				tbody.innerHTML = '<tr><td colspan="6" class="text-muted small">No hay pedidos hoy.</td></tr>';
				return;
			}

			tbody.innerHTML = pedidos.map(pedido => `
				<tr>
					<td>#${pedido.id}</td>
					<td>${escapeHtml(formatHora(pedido.created_at))}</td>
					<td>${escapeHtml(pedido.cliente?.nombre || '-')}<br><small class="text-muted">${escapeHtml(pedido.cliente?.telefono || '')}</small></td>
					<td>${escapeHtml(pedido.tipo || '-')}</td>
					<td>${money(pedido.total)}</td>
					<td>
						<div class="d-flex align-items-center gap-2">
							<span class="estado-badge ${estadoClass(pedido.estado)}">${escapeHtml(estadoLabel(pedido.estado))}</span>
							<button type="button" class="btn btn-sm btn-outline-primary js-abrir-estado">Cambiar</button>
						</div>
					</td>
				</tr>
			`).join('');

			tbody.querySelectorAll('.js-abrir-estado').forEach((button, index) => {
				button.addEventListener('click', () => {
					abrirModalEstadoPedido(pedidos[index]);
				});
			});
		}

		document.getElementById('btnBuscarCliente').addEventListener('click', buscarCliente);
		document.getElementById('telefonoBusqueda').addEventListener('keypress', (event) => {
			if (event.key === 'Enter') {
				event.preventDefault();
				buscarCliente();
			}
		});

		document.getElementById('filtroProductoRapido').addEventListener('input', renderProductosModal);
		document.getElementById('filtroProductoRapido').addEventListener('keydown', (event) => {
			if (event.key !== 'Enter') return;
			event.preventDefault();
			agregarPrimerProductoFiltrado();
		});
		document.getElementById('btnAbrirModalProductos').addEventListener('click', abrirModalProductosYFoco);
		document.getElementById('btnCrearPedido').addEventListener('click', crearPedido);
		document.getElementById('btnRefrescarPedidosHoy').addEventListener('click', cargarPedidosHoy);

		document.getElementById('tipoPedido').addEventListener('change', (event) => {
			const isDelivery = event.target.value === 'delivery';
			const zonaWrap = document.getElementById('zonaWrap');
			if (isDelivery) zonaWrap.classList.remove('d-none');
			else zonaWrap.classList.add('d-none');
		});

		modalClientes = new bootstrap.Modal(document.getElementById('modalSeleccionCliente'));
		modalProductos = new bootstrap.Modal(document.getElementById('modalAgregarProductos'));
		modalOpcionesProducto = new bootstrap.Modal(document.getElementById('modalOpcionesProducto'));
		modalEstadoPedido = new bootstrap.Modal(document.getElementById('modalEstadoPedido'));

		document.getElementById('btnGuardarEstadoPedido').addEventListener('click', () => {
			if (!pedidoEstadoActivo) return;
			const estadoNuevo = (document.getElementById('estadoPedidoSelect').value || '').trim();
			if (!estadoNuevo) return;
			actualizarEstadoPedido(Number(pedidoEstadoActivo.id), estadoNuevo);
		});

		document.getElementById('btnConfirmarOpcionesProducto').addEventListener('click', () => {
			if (!pendingItemContext) {
				modalOpcionesProducto.hide();
				return;
			}

			const selectedVarianteId = Number(document.querySelector('input[name="opcionVariante"]:checked')?.value || 0);
			const selectedModificadores = Array.from(document.querySelectorAll('input[name="opcionModificador"]:checked')).map(input => Number(input.value));
			const selectedGustos = Array.from(document.querySelectorAll('input[name="opcionGusto"]:checked'));
			const selectedGustosEmpanada = Array.from(document.querySelectorAll('input[name="opcionGustoEmpanada"]'))
				.map(input => ({
					id: Number(input.dataset.gustoId || 0),
					nombre: input.dataset.gustoNombre || input.value,
					cantidad: Number(input.value || 0)
				}))
				.filter(item => item.id > 0 && item.cantidad > 0);

			const totalEmpanadas = selectedGustosEmpanada.reduce((acc, item) => acc + Number(item.cantidad || 0), 0);

			if (isIceCreamProduct(pendingItemContext.producto?.nombre || '') && selectedGustos.length === 0) {
				alert('Elegí al menos 1 gusto para el helado.');
				return;
			}

			if (pendingItemContext.producto?.es_combo_gustos) {
				if (totalEmpanadas !== 12) {
					alert('La docena debe sumar 12 empanadas.');
					return;
				}
				if (selectedGustosEmpanada.length === 0) {
					alert('Seleccioná al menos un gusto para la docena.');
					return;
				}
			}

			if (selectedGustos.length > 3) {
				alert('Podés elegir hasta 3 gustos.');
				return;
			}

			const varianteSeleccionada = pendingItemContext.opciones.variantes.find(variante => Number(variante.id) === selectedVarianteId) || null;
			const modificadoresSeleccionados = pendingItemContext.opciones.modificadores.filter(modificador => selectedModificadores.includes(Number(modificador.id)));
			const gustosSeleccionados = selectedGustos.map(gusto => ({
				id: Number(gusto.value || 0),
				nombre: gusto.dataset.gustoNombre || gusto.value
			}));

			agregarItemAlPedido(
				pendingItemContext.producto,
				pendingItemContext.cantidad,
				varianteSeleccionada,
				modificadoresSeleccionados,
				gustosSeleccionados,
				selectedGustosEmpanada
			);

			pendingItemContext = null;
			modalOpcionesProducto.hide();
		});

		document.addEventListener('keydown', (event) => {
			if (event.key === 'F2') {
				event.preventDefault();
				abrirModalProductosYFoco();
				return;
			}

			if (event.key === 'F4') {
				event.preventDefault();
				document.getElementById('telefonoBusqueda')?.focus();
				return;
			}

			if (event.altKey && event.key.toLowerCase() === 'b') {
				event.preventDefault();
				buscarCliente();
				return;
			}

			if (event.ctrlKey && event.key === 'Enter') {
				event.preventDefault();
				crearPedido();
			}
		});

		cargarProductos();
		cargarZonas();
		cargarPedidosHoy();
	</script>

</body>

</html>