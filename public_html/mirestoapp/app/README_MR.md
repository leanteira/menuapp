# MiRestoApp · Etapa 1 (MVP)

Esta etapa agrega una base nueva para el sistema de pedidos, separada del sistema médico existente.

## Objetivo de esta etapa

- Diseño multi-restaurante desde el inicio.
- Carta online por restaurante.
- Carrito persistente en frontend.
- Checkout con creación de pedido + pago desacoplado (`pendiente`).
- Panel básico para ver pedidos y cambiar estado.
- ABM básico de categorías y productos desde panel.
- ABM de variantes y modificadores por producto.
- Toma de pedidos telefónicos con búsqueda por teléfono e historial.
- Zonas de envío avanzadas con tipo manual/radio/polígono.
- Validación automática de pedido mínimo por zona en checkout y pedido manual.
- Integración real de Mercado Pago (preferencia + webhook + conciliación).

## Archivos principales

- `app/mr_config.php`: configuración del módulo MR.
- `app/mr_bootstrap.php`: conexión DB, utilidades JSON y helpers.
- `app/mr_auth.php`: autenticación por sesión para panel MR.
- `app/api/mr/menu.php`: API pública de carta.
- `app/api/mr/checkout.php`: API pública de checkout.
- `app/api/mr/admin/pedidos.php`: lista de pedidos (panel).
- `app/api/mr/admin/pedido_estado.php`: cambio de estado.
- `app/api/mr/admin/categorias.php`: ABM categorías.
- `app/api/mr/admin/productos.php`: ABM productos.
- `app/api/mr/admin/variantes.php`: ABM variantes por producto.
- `app/api/mr/admin/modificadores.php`: ABM modificadores por producto.
- `app/api/mr/admin/cliente_telefono.php`: búsqueda de cliente e historial.
- `app/api/mr/admin/pedido_manual.php`: creación de pedido telefónico/manual.
- `app/api/mr/admin/zonas.php`: ABM de zonas y geometría.
- `app/api/mr/payments/mercadopago_preference.php`: genera preferencia de pago por pedido.
- `app/api/mr/payments/mercadopago_webhook.php`: webhook para conciliación de estados.
- `app/api/mr/payments/estado_pago.php`: consulta estado del último pago de un pedido.
- `app/login.php`: login del panel.
- `app/index.php`: tablero de pedidos con polling.
- `app/categorias.php`: gestión de categorías.
- `app/productos.php`: gestión de productos.
- `app/producto_detalle.php`: variantes y modificadores por producto.
- `app/pedido_telefonico.php`: operador telefónico.
- `app/zonas_envio.php`: gestión de zonas de envío.
- `db/mr_stage3_zonas.sql`: migración de esquema para zonas avanzadas.
- `index.php`: frontend MVP cliente final.

## Flujo MVP

1. Cliente abre `index.php?slug=demo-resto`.
2. Front consulta `app/api/mr/menu.php`.
3. Cliente arma carrito y confirma checkout.
4. API `checkout.php` crea:
   - cliente y dirección (si aplica),
   - pedido,
   - items con snapshot (`nombre_producto`, `precio_unitario`),
   - detalles de variantes/modificadores,
   - historial de estado,
   - pago en estado `pendiente`.
5. Operador entra a `app/login.php` y gestiona en `app/index.php`.

## Datos de prueba

Ejecutar `db/mr_seed_stage1.sql`.

Para bootstrap completo (dummy + usuarios + pedidos para ver panel):

- `db/mr_dummy_login_data.sql`

Credenciales de prueba:

- Email: `admin@demo.com`
- Password: `admin123`

Credenciales extra:

- `superadmin@demo.com` / `admin123`
- `operador@demo.com` / `admin123`
- `repartidor@demo.com` / `admin123`

## Configuración DB

Por defecto usa:

- host: `localhost`
- db: `serv_mirestoapp`
- user: `root`
- pass: vacío

Podés sobrescribir con variables de entorno:

- `MR_DB_HOST`
- `MR_DB_NAME`
- `MR_DB_USER`
- `MR_DB_PASS`
- `MR_DB_PORT`

Variables de entorno para Mercado Pago:

- `MR_MP_ACCESS_TOKEN`
- `MR_MP_PUBLIC_KEY`
- `MR_MP_BASE_URL` (default `https://api.mercadopago.com`)
- `MR_MP_WEBHOOK_URL`
- `MR_MP_SUCCESS_URL`
- `MR_MP_PENDING_URL`
- `MR_MP_FAILURE_URL`

## Próxima etapa sugerida

- JWT/API desacoplada para app móvil.
