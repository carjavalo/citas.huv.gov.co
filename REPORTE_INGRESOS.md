# Reporte de Ingresos y Actividad de Usuarios

## Descripción
Se ha creado una nueva opción en el menú "Reportes" llamada "Ingresos" que permite visualizar y rastrear toda la actividad de los usuarios en la plataforma.

## Ubicación en el Menú
**Menú Principal** → **Reportes** → **Ingresos**

## URL de Acceso
```
http://192.168.2.200:8000/reporte/ingresos
```

## Funcionalidad

### Propósito
Esta vista está diseñada para registrar y mostrar:
- ✅ Todos los ingresos de usuarios a la plataforma
- ✅ Servicios a los que acceden los usuarios
- ✅ Actividades que realizan en la plataforma
- ✅ Nuevos registros de usuarios (primera vez)
- ✅ Usuarios que se registran posteriormente

### Características Implementadas

#### 1. Filtros de Búsqueda
- **Fecha Inicio**: Seleccionar fecha de inicio del rango
- **Fecha Fin**: Seleccionar fecha final del rango
- **Tipo de Servicio**: Filtrar por tipo de actividad
  - Todos los Servicios
  - Nuevo Registro
  - Solicitud de Cita
  - Consulta
  - Edición de Perfil

#### 2. Estadísticas Rápidas
- **Total Registros**: Contador de todos los registros de actividad
- **Nuevos Usuarios**: Contador de usuarios registrados por primera vez
- **Botón Aplicar Filtros**: Para ejecutar la búsqueda

#### 3. Tabla de Registros
Muestra la siguiente información:
- **ID Usuario**: Identificador único del usuario
- **Nombre de Usuario**: Nombre completo con avatar
- **Fecha de Acceso**: Fecha en que se realizó la actividad
- **Hora (UTC)**: Hora exacta del acceso
- **Servicio Accedido**: Tipo de actividad realizada con badge de color
- **Acciones**: Menú de opciones adicionales

#### 4. Tipos de Servicios con Colores
- 🟢 **Verde**: Nuevo Registro
- 🟡 **Amarillo**: Solicitud de Cita
- 🔵 **Azul**: Edición de Perfil
- 🟠 **Naranja**: Otros servicios

#### 5. Paginación
- Navegación entre páginas de resultados
- Indicador de registros mostrados
- Botones de página anterior/siguiente

## Archivos Modificados/Creados

### 1. Menú de Navegación
**Archivo**: `resources/views/navigation-menu.blade.php`
```php
<a href="{{ route('reporte.ingresos') }}" class="block px-4 py-2 text-sm hover:bg-gray-100" style="color: #2c4370;">
   {{ __('Ingresos') }}
</a>
```

### 2. Ruta
**Archivo**: `routes/web.php`
```php
Route::get('/reporte/ingresos', function() { 
    return view('reporte.ingresos'); 
})->name('reporte.ingresos');
```

### 3. Vista
**Archivo**: `resources/views/reporte/ingresos.blade.php`
- Vista completa con diseño adaptado al estilo de la aplicación
- Usa el layout `x-app-layout` de Jetstream
- Incluye Material Symbols Icons
- Diseño responsive

## Estructura de la Vista

### Header
```
- Breadcrumb de navegación
- Título principal
- Descripción del reporte
```

### Sección de Filtros
```
- Campos de fecha (inicio/fin)
- Selector de tipo de servicio
- Estadísticas rápidas
- Botón de aplicar filtros
```

### Tabla de Datos
```
- Encabezados de columnas
- Filas de datos con hover effect
- Badges de colores por tipo de servicio
- Avatares de usuarios
```

### Paginación
```
- Información de registros mostrados
- Controles de navegación
- Números de página
```

## Próximos Pasos (Implementación Dinámica)

Para hacer la vista completamente funcional, se necesita:

### 1. Crear Modelo de Registro de Actividad
```php
// app/Models/ActividadUsuario.php
class ActividadUsuario extends Model
{
    protected $table = 'actividad_usuarios';
    protected $fillable = [
        'user_id',
        'tipo_servicio',
        'fecha_acceso',
        'hora_acceso',
        'ip_address',
        'user_agent'
    ];
}
```

### 2. Crear Migración
```bash
php artisan make:migration create_actividad_usuarios_table
```

```php
Schema::create('actividad_usuarios', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('tipo_servicio');
    $table->date('fecha_acceso');
    $table->time('hora_acceso');
    $table->string('ip_address')->nullable();
    $table->text('user_agent')->nullable();
    $table->timestamps();
});
```

### 3. Crear Componente Livewire
```bash
php artisan make:livewire Reporte/Ingresos
```

```php
// app/Http/Livewire/Reporte/Ingresos.php
class Ingresos extends Component
{
    public $fechaInicio;
    public $fechaFin;
    public $tipoServicio = '';
    
    public function mount()
    {
        $this->fechaInicio = now()->subDays(7)->format('Y-m-d');
        $this->fechaFin = now()->format('Y-m-d');
    }
    
    public function aplicarFiltros()
    {
        // Lógica de filtrado
    }
    
    public function render()
    {
        $actividades = ActividadUsuario::with('user')
            ->whereBetween('fecha_acceso', [$this->fechaInicio, $this->fechaFin])
            ->when($this->tipoServicio, function($query) {
                $query->where('tipo_servicio', $this->tipoServicio);
            })
            ->orderBy('fecha_acceso', 'desc')
            ->orderBy('hora_acceso', 'desc')
            ->paginate(10);
            
        return view('livewire.reporte.ingresos', [
            'actividades' => $actividades
        ]);
    }
}
```

### 4. Middleware para Registrar Actividad
```php
// app/Http/Middleware/RegistrarActividad.php
class RegistrarActividad
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            ActividadUsuario::create([
                'user_id' => Auth::id(),
                'tipo_servicio' => $this->determinarTipoServicio($request),
                'fecha_acceso' => now()->toDateString(),
                'hora_acceso' => now()->toTimeString(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }
        
        return $next($request);
    }
}
```

### 5. Registrar Middleware
```php
// app/Http/Kernel.php
protected $middlewareGroups = [
    'web' => [
        // ... otros middleware
        \App\Http\Middleware\RegistrarActividad::class,
    ],
];
```

## Permisos

La vista está protegida por el middleware `auth:sanctum` y `verified`.
Para acceder se requiere:
- ✅ Usuario autenticado
- ✅ Email verificado
- ✅ Permiso `admin.reporte.agentes` (para ver el menú de Reportes)

## Diseño y Estilo

### Colores Principales
- **Azul Primario**: `#2c4370` (color del tema)
- **Azul Botones**: `#3b82f6` (Tailwind blue-600)
- **Gris Fondo**: `#f9fafb` (Tailwind gray-50)

### Componentes UI
- **Badges**: Colores según tipo de servicio
- **Avatares**: Círculos con iniciales
- **Hover Effects**: Transiciones suaves
- **Responsive**: Adaptable a móviles y tablets

### Iconos
Usa **Material Symbols Outlined** de Google Fonts:
- `chevron_right` - Navegación
- `filter_alt` - Filtros
- `more_vert` - Menú de acciones

## Pruebas

### Acceder a la Vista
1. Iniciar sesión en la aplicación
2. Ir al menú superior "Reportes"
3. Hacer clic en "Ingresos"
4. Verificar que la vista se carga correctamente

### Verificar Elementos
- ✅ Breadcrumb funcional
- ✅ Filtros de fecha visibles
- ✅ Selector de servicio funcional
- ✅ Tabla con datos de ejemplo
- ✅ Paginación visible
- ✅ Diseño responsive

## Estado Actual

✅ **Menú creado** - Opción "Ingresos" agregada
✅ **Ruta configurada** - `/reporte/ingresos`
✅ **Vista creada** - Diseño completo y responsive
✅ **Estilos aplicados** - Consistente con el tema
✅ **Datos de ejemplo** - 3 filas de muestra
⏳ **Datos dinámicos** - Pendiente (requiere modelo y Livewire)
⏳ **Filtros funcionales** - Pendiente (requiere Livewire)
⏳ **Registro automático** - Pendiente (requiere middleware)

## Comandos Ejecutados

```bash
# Limpiar caché de vistas
php artisan view:clear

# Limpiar caché de rutas
php artisan route:clear
```

## Acceso Directo

```
http://192.168.2.200:8000/reporte/ingresos
```

La vista está lista para usar con datos de ejemplo. Para implementar la funcionalidad completa con datos reales, seguir los pasos de "Próximos Pasos" descritos arriba.
