# Ice Cream Flavor Selector Feature

## Overview
The ice cream flavor selector feature allows customers to choose up to 3 flavor options for any ice cream (helado) product on the ordering page.

## Files Created/Modified

### Frontend (index.php)
- **Flavor Selection UI**: Added flavor grid section that appears for ice cream products
- **CSS Styling** (.flavor-btn, .flavor-sample, .flavors-grid): Color-coded flavor buttons with visual indicators
- **JavaScript Functions**:
  - `isIceCreamProduct(productName)`: Detects if a product is ice cream
  - `loadFlavorsForProduct(productId)`: Async function to fetch flavors from API
  - `renderFlavorButtons(productId)`: Renders flavor buttons with click handlers
  - **Max 3-flavor constraint**: Enforced via click handler validation
- **Updated addToCart()**: Now captures selected flavors and includes them in cart item details

### Backend API (app/api/mr/helado_gustos.php)
- **Endpoint**: GET `/app/api/mr/helado_gustos.php`
- **Response**: JSON array of active ice cream flavors with id, nombre, descripcion, color_hex
- **Caching**: Flavors are cached client-side in `flavorsByProduct` object to minimize API calls

### Database Schema (app/db/helado_gustos_migration.sql)
**Tables created:**
1. `helado_gustos`: Stores all available ice cream flavors
   - Includes 12 pre-populated classic flavors (Vainilla, Chocolate, Frutilla, Dulce de leche, etc.)
   - Color hex codes for visual representation
   - Active/inactive status flag

2. `producto_helado_gustos`: Junction table linking products to flavors
   - Allows fine-grained control per product
   - Tracks availability per product-flavor combination

## How It Works

1. **Product Detection**: When `renderMenu()` processes products, it checks if product name includes "helad"
2. **Flavor Loading**: For ice cream products, `renderFlavorButtons()` is called, which:
   - Fetches flavors from `/app/api/mr/helado_gustos.php`
   - Caches result in `flavorsByProduct`
   - Renders color-coded flavor buttons
3. **Selection**: Customer clicks up to 3 flavor buttons to select them
   - Selected flavors get `.selected` class (brand color background)
   - Click handlers prevent selection beyond 3
4. **Cart Addition**: When "Agregar" is clicked:
   - `addToCart()` collects selected flavor names
   - Adds "Gustos: Vainilla, Chocolate, Menta" to `detalle_texto`
   - Cart displays flavor information with the item

## Setup Instructions

### 1. Execute SQL Migration
Import the SQL file into your database:
```sql
SOURCE app/db/helado_gustos_migration.sql;
```

This creates the `helado_gustos` table with 12 classic flavors and the `producto_helado_gustos` junction table.

### 2. Verify Flavor API
Test the endpoint:
```bash
curl http://your-domain/mirestoapp/app/api/mr/helado_gustos.php
```

Expected response:
```json
{
  "ok": true,
  "gustos": [
    {
      "id": 1,
      "nombre": "Vainilla",
      "descripcion": "Clásico y cremoso",
      "color_hex": "#F3E5AB"
    },
    ...
  ]
}
```

### 3. Test in Browser
1. Create a test product with name "Helado de..." in your database
2. Browse to the ordering page (index.php)
3. Scroll to ice cream product - should display flavor selector
4. Select 1-3 flavors and add to cart
5. Verify cart shows "Gustos: Flavor1, Flavor2, Flavor3"

## Future Enhancements

### ABM (Admin Panel)
Could create `admin/helado_gustos_management.php` to:
- CRUD operations on flavors
- Manage which flavors are available per product (via `producto_helado_gustos` table)
- Set flavor availability status

### Product-Flavor Relationships
Currently, all active flavors appear for all ice cream products. To restrict flavors per product:
1. Populate `producto_helado_gustos` table with product-flavor mappings
2. Modify `helado_gustos.php` API to accept optional `producto_id` parameter
3. Filter flavors by availability: `SELECT g.* FROM helado_gustos g JOIN producto_helado_gustos p ON g.id = p.gusto_id WHERE p.producto_id = ? AND p.disponible = 1`

## Technical Details

- **Cache Strategy**: Client-side caching to minimize API calls (all ice cream products share same flavor list)
- **Max Selections**: Enforced on client via `selectedCount < 3` check
- **Data Persistence**: Flavor selections stored in cart items' `detalle_texto` field
- **Responsive**: Flavor grid uses CSS Grid (3 columns), mobile-friendly button sizing
- **Colors**: Visual flavor identification uses hex color codes from database for accurate representation

## Files Location Reference
- Frontend: `public_html/mirestoapp/index.php` (lines ~700-815)
- Backend API: `public_html/mirestoapp/app/api/mr/helado_gustos.php`
- Database: `public_html/mirestoapp/app/db/helado_gustos_migration.sql`
