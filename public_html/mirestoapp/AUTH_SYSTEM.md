# Sistema de Autenticación y Gestión de Cuenta

## Descripción General

Se ha implementado un sistema completo de autenticación (login/registro) y gestión de cuenta de usuario con vistas para Mi Cuenta y Mis Pedidos.

## Archivos Creados/Modificados

### Frontend

**index.php (ACTUALIZADO)**
- Modal de Login/Registro con pestaña intercambiable
- Botón "Login" ahora abre modal en lugar de redirigir
- Funciones JavaScript para manejar login y registro
- Links actualizados a mi-cuenta.php y mis-pedidos.php

**mi-cuenta.php (NUEVO)**
- Perfil de usuario con avatar personalizado
- Muestra nombre, teléfono y email
- Botones para editar perfil (placeholder), cambiar contraseña (placeholder) y cerrar sesión
- Redirige a index.php si no está autenticado
- Mismo tema/template que index.php

**mis-pedidos.php (NUEVO)**
- Historial completo de pedidos del usuario
- Muestra estado, fecha, cantidad de items y total
- Diferencia entre delivery (con dirección) y retiro en local
- Badges de estado con colores diferenciados (nuevo, confirmado, preparando, listo, entregado, cancelado)
- Carga pedidos dinámicamente desde API
- Redirige a index.php si no está autenticado

### Backend API

**auth_login.php (NUEVO)**
- POST endpoint para login
- Valida teléfono y contraseña
- Inicia sesión y carga datos en $_SESSION
- Retorna cliente_id y nombre

**auth_register.php (NUEVO)**
- POST endpoint para registro de nuevo usuario
- Valida que no exista teléfono duplicado
- Crea nuevo cliente en la base de datos
- Retorna cliente_id y nombre

**auth_logout.php (NUEVO)**
- POST endpoint para cerrar sesión
- Destruye la sesión actual
- Retorna confirmación

**get_pedidos.php (NUEVO)**
- GET endpoint para obtener pedidos del usuario autenticado
- Requiere que el usuario esté en sesión
- Retorna array de pedidos con detalles:
  - id, tipo (delivery/retiro), estado, total
  - dirección o "Retiro en local"
  - cantidad de items
  - fecha de creación

### Database

**add_auth_columns.sql (NUEVO)**
- Agrega columna `stored_password` a tabla `clientes`
- Inicializa contraseña demo "123456" para usuarios existentes

## Flujo de Autenticación

### Login
1. Usuario hace click en botón "Login" en index.php
2. Se abre modal con formulario de login
3. Ingresa teléfono y contraseña
4. JavaScript llama a `/app/api/mr/auth_login.php`
5. Si es válido, se inicia sesión y redirige a mi-cuenta.php
6. La sesión se mantiene en `$_SESSION`

### Registro
1. En el modal, usuario hace click en "Registrate aquí"
2. Cambia a pestaña de registro
3. Ingresa nombre, teléfono, email (opcional) y contraseña
4. JavaScript llama a `/app/api/mr/auth_register.php`
5. Si es exitoso, vuelve a pestaña login con mensaje de éxito
6. Usuario puede iniciar sesión con sus datos

### Cierre de Sesión
1. Usuario hace click en botón "Salir" o "Cerrar sesión"
2. Confirma acción con popup
3. JavaScript llama a `/app/api/mr/auth_logout.php`
4. Se destruye sesión
5. Redirige a index.php

## Protección de Vistas

Las vistas `mi-cuenta.php` y `mis-pedidos.php` validan:
```php
if (!isset($_SESSION['cliente_id'])) {
    header('Location: index.php');
    exit;
}
```

Si el usuario no está autenticado, lo redirigen a la página de inicio.

## Datos de Sesión

Cuando se inicia sesión correctamente, se almacenan:
```php
$_SESSION['cliente_id']       // ID del cliente
$_SESSION['cliente_nombre']   // Nombre completo
$_SESSION['cliente_telefono'] // Teléfono
$_SESSION['cliente_email']    // Email
```

## Setup Instructions

### 1. Ejecutar Migración de BD
```sql
SOURCE db/add_auth_columns.sql;
```

Esto agrega la columna `stored_password` a la tabla clientes con contraseña "123456" para usuarios existentes.

### 2. Crear un Cliente para Testing
Opción A: Usar clientes existentes (con contraseña 123456)
- Teléfono: 1155551111
- Contraseña: 123456

Opción B: Registrarse nuevo
- Ir a index.php → click Login
- Cambiar a "Registrate aquí"
- Llenar formulario
- Crear cuenta

### 3. Probar Flujo
1. index.php → Login → Ingresar credenciales
2. Debería redirigir a mi-cuenta.php
3. Ver perfil del usuario
4. Click en "Mis pedidos" → Ver historial
5. Click en "Salir" → Vuelve a index.php

## Notas de Seguridad

⚠️ **Para Producción:**
- Usar `password_hash()` y `password_verify()` en lugar de almacenar contraseñas en texto plano
- Implementar validación más robusta de inputs
- Usar HTTPS
- Agregar CSRF tokens
- Implementar rate limiting en endpoints de auth
- Usar tokens JWT o similares para API autenticada

## Funcionalidades Pendientes (Placeholders)

Los siguientes botones abren `alert()` con mensaje "En desarrollo":
- "Editar perfil" en mi-cuenta.php
- "Cambiar contraseña" en mi-cuenta.php
- "Ver detalles" en cada pedido en mis-pedidos.php

Estos pueden ser completados después.

## Estructura de URLs

- **Login/Registro**: `index.php` (modal)
- **Mi Cuenta**: `mi-cuenta.php`
- **Mis Pedidos**: `mis-pedidos.php`
- **API Login**: `app/api/mr/auth_login.php`
- **API Registro**: `app/api/mr/auth_register.php`
- **API Logout**: `app/api/mr/auth_logout.php`
- **API Pedidos**: `app/api/mr/get_pedidos.php`

## Documentación API

### POST /app/api/mr/auth_login.php
```json
Request:
{
  "telefono": "1155551234",
  "password": "123456"
}

Response (Success):
{
  "ok": true,
  "cliente_id": 1,
  "nombre": "Juan Pérez"
}

Response (Error):
{
  "ok": false,
  "error": "Teléfono o contraseña incorrectos."
}
```

### POST /app/api/mr/auth_register.php
```json
Request:
{
  "nombre": "Juan Pérez",
  "telefono": "1155551234",
  "email": "juan@email.com",
  "password": "micontraseña"
}

Response (Success):
{
  "ok": true,
  "cliente_id": 5,
  "nombre": "Juan Pérez"
}

Response (Error):
{
  "ok": false,
  "error": "El teléfono ya está registrado."
}
```

### POST /app/api/mr/auth_logout.php
```json
Response:
{
  "ok": true,
  "message": "Sesión cerrada correctamente."
}
```

### GET /app/api/mr/get_pedidos.php
```json
Response (Success):
{
  "ok": true,
  "pedidos": [
    {
      "id": 1,
      "tipo": "delivery",
      "estado": "entregado",
      "total": 14300.00,
      "direccion": "Av. Corrientes 1234, CABA",
      "items_count": 2,
      "created_at": "2026-02-20 20:20:34"
    }
  ]
}

Response (Not Authenticated):
{
  "ok": false,
  "error": "Usuario no autenticado."
}
```

## Estilos y Diseño

- Mantiene la consistencia visual con el resto de la aplicación
- Usa el color de marca (#ea1d6f) y esquema de colores existentes
- Responsive en mobile y desktop
- Animations suaves en hover
- Estados de login con mensajes de exito/error

## Validaciones Implementadas

**Login:**
- Teléfono y contraseña requeridos
- Verifica credenciales contra BD
- Error específico si no coinciden

**Registro:**
- Nombre, teléfono y contraseña requeridos
- Email opcional
- Valida que teléfono no esté duplicado
- Crea cuenta en BD

**Sesión:**
- Verifica que _SESSION['cliente_id'] exista
- Redirige a index.php si no está autenticado
- Datos de sesión accesibles en vistas

