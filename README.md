# UI clínico VetTech

Sistema Blade con estilo clínico, limpio y amigable basado en Tailwind CSS. Paleta: blanco base, menta como acento y grises suaves para texto/bordes.

## Tailwind Setup
- Dependencias ya incluidas en `package.json` (Tailwind, Vite, PostCSS). Si necesitas reinstalar:
  1. `npm install`
  2. `npx tailwindcss init -p` (ya generado como `tailwind.config.js`).
- Comandos de assets:
  - `npm run dev` para desarrollo con Vite.
  - `npm run build` para compilar CSS/JS.
- Tokens de color definidos en `resources/css/app.css` y expuestos en `tailwind.config.js`:
  - Menta: `--mint-50`, `--mint-100`, `--mint-200`, `--mint-500`, `--mint-600`.
  - Grises: `--gray-50`, `--gray-100`, `--gray-200`, `--gray-500`, `--gray-700`.
  - Alertas: `--danger-500`, `--warning-500`.

## Layouts
- `resources/views/layouts/app.blade.php`: layout principal con navbar superior (logo, breadcrumbs opcionales, usuario con dropdown) y sidebar fijo en desktop. Contenido limitado a `max-w-7xl` con fondo gris muy claro.
- `resources/views/layouts/guest.blade.php`: layout centrado para login/register (Breeze), card con borde gris y botón/acento menta.
- Mensajes flash gestionados con `<x-alert>`.

## Components
Componentes Blade reutilizables en `resources/views/components`:
- `<x-card>`: borde gris-200, esquinas redondeadas, sombra suave y soporte de título/subtítulo/acciones.
- `<x-kpi>`: card compacta con borde superior menta, valor grande y pista secundaria.
- `<x-badge>`: variantes `mint|gray|danger|warning`, estilo píldora.
- `<x-button>`: variantes `primary|secondary|ghost|danger`, tamaños `sm|md`, soporta `href`.
- `<x-table>`: tabla estándar con encabezado gris, hover gris-50 y estado vacío.
- `<x-alert>`: barra con borde izquierdo por tipo (`success|error|info|warning`).
- `<x-input>`, `<x-select>`, `<x-textarea>`: campos con borde gris y focus ring menta; soporte de label y error.
- `<x-empty>`: estado vacío con icono y acción opcional.
- `<x-inline-actions>`: helper para renderizar conjuntos de botones compactos.

## Sidebar/Nav
- Sidebar en `app.blade.php` con enlaces: Dashboard, Tutores, Pacientes, Agenda, Hospitalización 24/7, Dispensación, Ventas, Caja, Reportes.
- Item activo resalta con borde menta y fondo menta muy claro (`sidebar-link-active`).

## Updated Dashboards
- `dashboards/admin.blade.php`: KPIs en `<x-kpi>`, secciones en `<x-card>`, badges para estados y estados vacíos claros.
- `dashboards/medico.blade.php`: agenda, hospitalización y alertas con cards/badges; acciones rápidas en botones menta/gris.
- `dashboards/contador.blade.php`: filtros con inputs menta, KPIs financieros, cierre de caja en `<x-table>` y badges por estado.

## Captura mental del diseño
Menta se usa solo como acento: borde superior de KPIs, resaltado de sidebar activo, fondos muy claros en badges/botones primarios y contenedores de iconos. El fondo general permanece blanco/gris para mantener el espacio en blanco y la sensación clínica.

## Módulo de Hospitalización

1. Ejecuta migraciones y seeders de demo:
   ```bash
   php artisan migrate
   php artisan db:seed --class=HospitalDemoSeeder
   ```
2. Navega a `/vet/hospital` para ver el tablero de hospitalización.
3. Funcionalidades clave:
   - Admisión rápida de pacientes con generación automática de Day 1.
   - Tabs por día con órdenes médicas, aplicaciones, signos vitales y notas de evolución.
   - Registro de cargos y generación de factura integrando la tabla `sales`.
4. Suposiciones: se usa la tabla `products` para inventario cuando está disponible y se mantienen órdenes manuales sin depender de stock.

## Módulo de Peluquería Veterinaria

1. Ejecuta migraciones y seeders demo:
   ```bash
   php artisan migrate
   php artisan db:seed --class=GroomingDemoSeeder
   ```
2. Rutas clave:
   - `/peluqueria`: tablero kanban (agendado / en proceso / finalizado).
   - `/peluqueria/crear`: agendamiento con domicilio, desparasitación y servicio opcional.
   - `/peluqueria/{id}`: detalle con acciones de iniciar, cancelar, informe y cobro opcional.
3. Flujo de estados: Agendado → En proceso → Finalizado (guardar informe finaliza automáticamente). Cancelación disponible hasta antes del cierre.
4. Informe de baño con flags (pulgas, garrapatas, piel, oído) y observaciones/recomendaciones.
5. Cobro opcional: si hay módulo de ventas y un producto de servicio asociado, el botón “Cobrar” crea el ítem con `ref_entity=grooming`.

## Módulo de Facturación POS (Colombia)

1. Ejecuta las migraciones del tenant:
   ```bash
   php artisan migrate --path=database/migrations/tenant
   ```
2. Rutas clave:
   - `/invoices/pos`: pantalla POS.
   - `/invoices`: listado y filtros.
   - `/invoices/{invoice}`: detalle.
   - `/invoices/{invoice}/print`: ticket imprimible.
3. APIs de búsqueda (para autocomplete):
   - `/api/items/search?q=`
   - `/api/owners/search?q=`
4. Configuración rápida:
   - `config/billing.php` define prefijo POS, IVA/comisión por defecto y moneda.
5. Inventario:
   - Se descuenta automáticamente si `items.track_inventory = true` y `items.type = 'product'`.
6. Preparación DIAN:
   - Campos electrónicos en `invoices` y tabla `dian_resolutions` lista para usar.

## Módulo de Inventario

1. Rutas principales:
   - `/items`: listado con filtros y panel de detalle.
   - `/items/create`: creación de ítems.
   - `/items/{item}`: detalle y movimientos.
2. Movimientos de inventario:
   - Entradas: botón “Entrada” (panel derecho o detalle).
   - Salidas: botón “Salida”.
   - Ajustes: botón “Ajuste”.
   - Historial completo: `/items/{item}/movements`.
3. Reglas clave:
   - `sale_price` y `cost_price` son fuente principal; se sincronizan con `valor` y `costo`.
   - `cantidad` es el stock mínimo (alerta) y `stock` el stock actual.
   - No se permite stock negativo si el ítem es inventariable o controla inventario.

## Módulo de Reportes (rápidos y avanzados)

1. Rutas principales:
   - `/reports/quick`: dashboard de KPIs rápidos.
   - `/reports`: menú de reportes administrativos (solo admin).
   - `/reports/sales`, `/reports/payments`, `/reports/expenses`, `/reports/cash`, `/reports/operations`, `/reports/grooming`, `/reports/inventory`.
2. Exportables:
   - `/reports/export?report=sales&format=csv&from=YYYY-MM-DD&to=YYYY-MM-DD`
3. Tablas asumidas:
   - `invoices`, `invoice_lines`, `invoice_payments`, `inventory_movements`, `items`, `owners`.
   - `expenses` y `cash_closures` se crean si no existen.
4. Configuración:
   - `config/reporting.php` permite mapear tipos/áreas de servicio y peluquería.
5. Multitenancy:
   - Si las tablas tienen `tenant_id`, las consultas filtran por el tenant del usuario autenticado.
