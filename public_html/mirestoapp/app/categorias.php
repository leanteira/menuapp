<?php
include('include-header.php');
?>
<body>
  <div class="layout-wrapper layout-content-navbar"><div class="layout-container"><?php include('include-menu.php'); ?><div class="layout-page"><?php include('include-navbar.php'); ?><div class="content-wrapper"><div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Categorías</h4></div>
  <div class="card mb-3"><div class="card-body"><form id="formCategoria" class="row g-2"><input type="hidden" id="catId"><div class="col-md-5"><input class="form-control" id="catNombre" placeholder="Nombre" required></div><div class="col-md-2"><input type="number" class="form-control" id="catOrden" placeholder="Orden" value="0"></div><div class="col-md-2"><select id="catActivo" class="form-select"><option value="1">Activa</option><option value="0">Inactiva</option></select></div><div class="col-md-3 d-grid"><button class="btn btn-primary" type="submit">Guardar</button></div></form></div></div>
  <div class="card"><div class="card-body"><div class="table-responsive"><table class="table table-hover" id="tablaCategorias"><thead><tr><th>ID</th><th>Nombre</th><th>Orden</th><th>Estado</th><th></th></tr></thead><tbody></tbody></table></div></div></div>
  </div><?php include('include-footer.php'); ?><div class="content-backdrop fade"></div></div></div></div></div>
  <?php include('include-js-import.php'); ?>
  <script>
  const userRole=<?php echo json_encode($rol ?? ''); ?>;
  function q(){if(userRole!=='superadmin')return '';const r=Number(new URLSearchParams(location.search).get('restaurante_id')||0);return r>0?('?restaurante_id='+r):'';}
  async function load(){const r=await fetch('api/mr/admin/categorias.php'+q(),{credentials:'same-origin'});const d=await r.json();if(!d.ok){alert(d.error||'Error');return;}const tb=document.querySelector('#tablaCategorias tbody');tb.innerHTML='';d.categorias.forEach(c=>{const tr=document.createElement('tr');tr.innerHTML=`<td>${c.id}</td><td>${c.nombre}</td><td>${c.orden}</td><td>${c.activo?'Activa':'Inactiva'}</td><td><button class="btn btn-sm btn-outline-primary" onclick='editCat(${JSON.stringify(c)})'>Editar</button></td>`;tb.appendChild(tr);});}
  function editCat(c){catId.value=c.id;catNombre.value=c.nombre;catOrden.value=c.orden;catActivo.value=c.activo?'1':'0';} window.editCat=editCat;
  formCategoria.addEventListener('submit',async(e)=>{e.preventDefault();const id=Number(catId.value||0);const p={action:id?'update':'create',id,nombre:catNombre.value,orden:Number(catOrden.value||0),activo:Number(catActivo.value)===1};if(userRole==='superadmin'){p.restaurante_id=Number(new URLSearchParams(location.search).get('restaurante_id')||0);}const r=await fetch('api/mr/admin/categorias.php',{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json'},body:JSON.stringify(p)});const d=await r.json();if(!d.ok){alert(d.error||'Error');return;}e.target.reset();catId.value='';load();});
  load();
  </script>
</body></html>
