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
            <div class="card mb-4" style="background:linear-gradient(135deg,#e64389 0%,#e9bdd0 100%);color:#fff;">
              <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                  <h4 class="mb-1 text-white">Panel de Pedidos</h4>
                  <div class="text-white opacity-75">Usuario: <?php echo htmlspecialchars(($nombre ?? '') . ' ' . ($apellido ?? '')); ?> · Rol: <?php echo htmlspecialchars($rol ?? ''); ?></div>
                </div>
                <div class="d-flex gap-2">
                  <a href="categorias.php" class="btn btn-light">Categorías</a>
                  <a href="productos.php" class="btn btn-light">Productos</a>
                  <a href="zonas_envio.php" class="btn btn-light">Zonas</a>
                  <a href="pedido_telefonico.php" class="btn btn-light">Pedido telefónico</a>
                </div>
              </div>
            </div>

            <div class="card mb-3">
              <div class="card-body d-flex flex-wrap gap-2 align-items-end">
                <div>
                  <label class="form-label">Estado</label>
                  <select id="filtroEstado" class="form-select">
                    <option value="">Todos</option>
                    <option value="nuevo">Nuevo</option>
                    <option value="confirmado">Confirmado</option>
                    <option value="preparando">Preparando</option>
                    <option value="listo">Listo</option>
                    <option value="enviado">Enviado</option>
                    <option value="entregado">Entregado</option>
                    <option value="cancelado">Cancelado</option>
                  </select>
                </div>
                <div>
                  <label class="form-label">Período métricas</label>
                  <select id="filtroDias" class="form-select">
                    <option value="7">Últimos 7 días</option>
                    <option value="30" selected>Últimos 30 días</option>
                    <option value="90">Últimos 90 días</option>
                  </select>
                </div>
                <button class="btn btn-primary" id="btnRefrescar">Refrescar</button>
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-3">
                <div class="card h-100">
                  <div class="card-body">
                    <span class="text-muted">Ticket promedio</span>
                    <h4 class="mb-0" id="metricTicketPromedio">$ 0.00</h4>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card h-100">
                  <div class="card-body">
                    <span class="text-muted">Ventas del período</span>
                    <h4 class="mb-0" id="metricVentasTotales">$ 0.00</h4>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card h-100">
                  <div class="card-body">
                    <span class="text-muted">Cancelación</span>
                    <h4 class="mb-0" id="metricCancelacion">0%</h4>
                    <small class="text-muted" id="metricCancelacionDetalle">0 cancelados / 0 pedidos</small>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card h-100">
                  <div class="card-body">
                    <span class="text-muted">Clientes recurrentes</span>
                    <h4 class="mb-0" id="metricRecurrentes">0%</h4>
                    <small class="text-muted" id="metricRecurrentesDetalle">0 recurrentes / 0 únicos</small>
                  </div>
                </div>
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-lg-6">
                <div class="card h-100">
                  <div class="card-header pb-2">
                    <h5 class="mb-0">Ventas por hora</h5>
                  </div>
                  <div class="card-body">
                    <div id="chartVentasHora" style="min-height: 280px;"></div>
                    <div class="table-responsive">
                      <table class="table table-sm" id="tablaVentasHora">
                        <thead>
                          <tr>
                            <th>Hora</th>
                            <th>Pedidos</th>
                            <th>Ventas</th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="card h-100">
                  <div class="card-header pb-2">
                    <h5 class="mb-0">Top productos</h5>
                  </div>
                  <div class="card-body">
                    <div id="chartTopProductos" style="min-height: 280px;"></div>
                    <div class="table-responsive">
                      <table class="table table-sm" id="tablaTopProductos">
                        <thead>
                          <tr>
                            <th>Producto</th>
                            <th>Unidades</th>
                            <th>Monto</th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="card"><div class="card-body"><div class="table-responsive"><table class="table table-hover" id="tablaPedidos"><thead><tr><th>#</th><th>Fecha</th><th>Cliente</th><th>Tipo</th><th>Total</th><th>Pago</th><th>Estado</th><th>Acción</th></tr></thead><tbody></tbody></table></div></div></div>
          </div>

          <?php include('include-footer.php'); ?>
          <div class="content-backdrop fade"></div>
        </div>
      </div>
    </div>
  </div>

  <?php include('include-js-import.php'); ?>
  <script>
    const estados = ['nuevo', 'confirmado', 'preparando', 'listo', 'enviado', 'entregado', 'cancelado'];
    let chartVentasHora = null;
    let chartTopProductos = null;

    function formatMoney(value) {
      return '$ ' + Number(value || 0).toFixed(2);
    }

    function renderChartVentasHora(rows) {
      const categories = rows.map(r => r.hora);
      const seriesData = rows.map(r => Number(r.ventas || 0));

      if (chartVentasHora) {
        chartVentasHora.destroy();
      }

      chartVentasHora = new ApexCharts(document.querySelector('#chartVentasHora'), {
        chart: { type: 'bar', height: 280, toolbar: { show: false } },
        series: [{ name: 'Ventas', data: seriesData }],
        xaxis: { categories: categories },
        yaxis: {
          labels: {
            formatter: function (val) { return '$ ' + Number(val).toFixed(0); }
          }
        },
        dataLabels: { enabled: false },
        plotOptions: {
          bar: { borderRadius: 4, columnWidth: '45%' }
        },
        tooltip: {
          y: {
            formatter: function (val) { return formatMoney(val); }
          }
        },
        noData: { text: 'Sin datos para el período' }
      });

      chartVentasHora.render();
    }

    function renderChartTopProductos(rows) {
      const categories = rows.map(r => r.nombre);
      const seriesData = rows.map(r => Number(r.unidades || 0));

      if (chartTopProductos) {
        chartTopProductos.destroy();
      }

      chartTopProductos = new ApexCharts(document.querySelector('#chartTopProductos'), {
        chart: { type: 'bar', height: 280, toolbar: { show: false } },
        series: [{ name: 'Unidades', data: seriesData }],
        xaxis: { categories: categories },
        dataLabels: { enabled: false },
        plotOptions: {
          bar: { horizontal: true, borderRadius: 4 }
        },
        tooltip: {
          y: {
            formatter: function (val) { return Number(val || 0) + ' u.'; }
          }
        },
        noData: { text: 'Sin datos para el período' }
      });

      chartTopProductos.render();
    }

    async function cargarPedidos() {
      const estado = filtroEstado.value;
      const url = 'api/mr/admin/pedidos.php' + (estado ? ('?estado=' + encodeURIComponent(estado)) : '');
      const res = await fetch(url, { credentials: 'same-origin' });
      const data = await res.json();

      if (!data.ok) {
        alert(data.error || 'Error cargando pedidos');
        return;
      }

      const tb = document.querySelector('#tablaPedidos tbody');
      tb.innerHTML = '';

      data.pedidos.forEach(p => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${p.id}</td>
          <td>${p.created_at}</td>
          <td>${p.cliente?.nombre || '-'}<br><small>${p.cliente?.telefono || ''}</small></td>
          <td>${p.tipo}</td>
          <td>${formatMoney(p.total)}</td>
          <td>${p.pago?.metodo || '-'}<br><small>${p.pago?.estado || '-'}</small></td>
          <td><span class="badge bg-label-primary">${p.estado}</span></td>
          <td>
            <select class="form-select form-select-sm js-estado" data-id="${p.id}">
              ${estados.map(e => `<option value="${e}" ${e === p.estado ? 'selected' : ''}>${e}</option>`).join('')}
            </select>
          </td>`;
        tb.appendChild(tr);
      });

      document.querySelectorAll('.js-estado').forEach(select => {
        select.addEventListener('change', async (ev) => {
          await fetch('api/mr/admin/pedido_estado.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              pedido_id: Number(ev.target.dataset.id),
              estado: ev.target.value
            })
          });
          cargarPedidos();
          cargarMetricas();
        });
      });
    }

    async function cargarMetricas() {
      const days = Number(filtroDias.value || 30);
      const res = await fetch('api/mr/admin/metricas.php?days=' + days, { credentials: 'same-origin' });
      const data = await res.json();

      if (!data.ok) {
        alert(data.error || 'Error cargando métricas');
        return;
      }

      const overview = data.overview || {};
      const recurrentes = data.recurrentes || {};

      metricTicketPromedio.textContent = formatMoney(overview.ticket_promedio);
      metricVentasTotales.textContent = formatMoney(overview.ventas_totales);
      metricCancelacion.textContent = Number(overview.cancel_rate || 0).toFixed(2) + '%';
      metricCancelacionDetalle.textContent = `${overview.pedidos_cancelados || 0} cancelados / ${overview.pedidos_totales || 0} pedidos`;

      metricRecurrentes.textContent = Number(recurrentes.tasa_recurrentes || 0).toFixed(2) + '%';
      metricRecurrentesDetalle.textContent = `${recurrentes.clientes_recurrentes || 0} recurrentes / ${recurrentes.clientes_unicos || 0} únicos`;

      const tbHora = document.querySelector('#tablaVentasHora tbody');
      tbHora.innerHTML = '';
      const ventasHoraRows = data.ventas_por_hora || [];
      ventasHoraRows.forEach(row => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${row.hora}</td><td>${row.pedidos}</td><td>${formatMoney(row.ventas)}</td>`;
        tbHora.appendChild(tr);
      });
      renderChartVentasHora(ventasHoraRows);

      const tbTop = document.querySelector('#tablaTopProductos tbody');
      tbTop.innerHTML = '';
      const topRows = data.top_productos || [];
      topRows.forEach(row => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${row.nombre}</td><td>${row.unidades}</td><td>${formatMoney(row.monto)}</td>`;
        tbTop.appendChild(tr);
      });
      renderChartTopProductos(topRows);
    }

    btnRefrescar.addEventListener('click', () => {
      cargarPedidos();
      cargarMetricas();
    });

    filtroEstado.addEventListener('change', cargarPedidos);
    filtroDias.addEventListener('change', cargarMetricas);

    cargarPedidos();
    cargarMetricas();
    setInterval(() => {
      cargarPedidos();
      cargarMetricas();
    }, 15000);
  </script>
</body>
</html>
