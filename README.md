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
