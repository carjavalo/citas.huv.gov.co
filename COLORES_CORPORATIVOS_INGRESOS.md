# Aplicación de Colores Corporativos - Vista de Ingresos

## Color Corporativo Principal
**#2e3a75** - Azul oscuro corporativo

## Colores Aplicados en la Vista

### 1. Header (Encabezado)
- **Título principal**: `color: #2e3a75`
- **Fondo del header**: Gradiente `linear-gradient(135deg, #2e3a75 0%, #3d4d8f 100%)`
- **Texto del header**: Blanco con opacidad 80% para descripción

### 2. Breadcrumb (Navegación)
- **Hover en enlaces**: `color: #2e3a75`
- **Texto activo**: `color: #2e3a75`

### 3. Filtros
- **Labels**: `color: #2e3a75`
- **Focus en inputs**: 
  - Border: `#2e3a75`
  - Box-shadow: `rgba(46, 58, 117, 0.1)`

### 4. Estadísticas Rápidas
- **Card "Total Registros"**:
  - Fondo: `rgba(46, 58, 117, 0.1)` (10% opacidad)
  - Border: `rgba(46, 58, 117, 0.3)` (30% opacidad)
  - Texto label: `rgba(46, 58, 117, 0.7)` (70% opacidad)
  - Texto valor: `#2e3a75`

- **Botón "Aplicar Filtros"**:
  - Fondo: `#2e3a75`
  - Hover: Opacidad 90%

### 5. Tabla
- **Encabezados**:
  - Fondo: `rgba(46, 58, 117, 0.05)` (5% opacidad)
  - Border inferior: `#2e3a75` (2px)
  - Texto: `#2e3a75`

- **ID de Usuario**:
  - Color: `#2e3a75`

- **Avatares**:
  - Usuario 1: `#2e3a75`
  - Usuario 2: `#3d4d8f` (variante más clara)
  - Usuario 3: `#4a5ba3` (variante aún más clara)

- **Badge "Edición de Perfil"**:
  - Fondo: `rgba(46, 58, 117, 0.8)` (80% opacidad)
  - Border: `#2e3a75`
  - Texto: Blanco

- **Botones de acción**:
  - Hover: `color: #2e3a75`

### 6. Paginación
- **Página activa**:
  - Fondo: `#2e3a75`
  - Texto: Blanco

- **Hover en páginas**:
  - Fondo: `rgba(46, 58, 117, 0.1)`
  - Texto: `#2e3a75`

## Variantes del Color Corporativo

Para crear profundidad y jerarquía visual:

| Variante | Código | Uso |
|----------|--------|-----|
| Principal | `#2e3a75` | Elementos principales, textos importantes |
| Claro 1 | `#3d4d8f` | Avatares, elementos secundarios |
| Claro 2 | `#4a5ba3` | Avatares, variaciones |
| Opacidad 5% | `rgba(46, 58, 117, 0.05)` | Fondos sutiles |
| Opacidad 10% | `rgba(46, 58, 117, 0.1)` | Cards, hover states |
| Opacidad 30% | `rgba(46, 58, 117, 0.3)` | Bordes |
| Opacidad 70% | `rgba(46, 58, 117, 0.7)` | Textos secundarios |
| Opacidad 80% | `rgba(46, 58, 117, 0.8)` | Badges, elementos destacados |

## Estilos CSS Personalizados

### Hover Effects
```css
.hover-primary:hover {
    background-color: rgba(46, 58, 117, 0.1) !important;
    color: #2e3a75 !important;
}
```

### Focus States
```css
input:focus, select:focus {
    outline: none;
    border-color: #2e3a75 !important;
    box-shadow: 0 0 0 3px rgba(46, 58, 117, 0.1) !important;
}
```

### Hover para Iconos
```css
button:hover .material-symbols-outlined {
    color: #2e3a75;
}
```

## Colores Complementarios Mantenidos

Para mantener la legibilidad y jerarquía visual:

- **Verde** (Nuevo Registro): `bg-green-100`, `text-green-700`, `border-green-200`
- **Amarillo** (Solicitud de Cita): `bg-yellow-100`, `text-yellow-700`, `border-yellow-200`
- **Gris** (Textos secundarios): `text-gray-600`, `text-gray-900`

## Gradiente del Header

```css
background: linear-gradient(135deg, #2e3a75 0%, #3d4d8f 100%);
```

Este gradiente crea un efecto visual atractivo que va del color corporativo principal a una variante más clara.

## Comparación Antes/Después

### Antes (Azul genérico)
- Header: Fondo blanco
- Botones: `bg-blue-600` (#2563eb)
- IDs: `text-blue-600`
- Paginación: `bg-blue-600`

### Después (Color corporativo)
- Header: Gradiente `#2e3a75` → `#3d4d8f`
- Botones: `#2e3a75`
- IDs: `#2e3a75`
- Paginación: `#2e3a75`

## Consistencia con el Resto de la Aplicación

El color `#2e3a75` ya se usa en:
- ✅ Menú de navegación principal
- ✅ Enlaces y botones
- ✅ Elementos de la interfaz
- ✅ Otros reportes y vistas

Ahora la vista de Ingresos está completamente alineada con la identidad visual corporativa.

## Accesibilidad

Los colores aplicados mantienen un contraste adecuado:
- **Texto blanco sobre #2e3a75**: Ratio de contraste > 4.5:1 ✅
- **#2e3a75 sobre blanco**: Ratio de contraste > 4.5:1 ✅
- **Opacidades**: Suficiente contraste para legibilidad ✅

## Verificación

Para verificar los cambios:
1. Acceder a: `http://192.168.2.200:8000/reporte/ingresos`
2. Verificar que todos los elementos usan el color corporativo
3. Probar hover states en botones y enlaces
4. Verificar focus states en inputs

## Comandos Ejecutados

```bash
php artisan view:clear
```

✅ Caché limpiada
✅ Colores corporativos aplicados
✅ Vista lista para usar
