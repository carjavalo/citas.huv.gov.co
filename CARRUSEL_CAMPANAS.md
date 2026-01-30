# Carrusel Automático de Campañas

## Descripción
Se ha implementado un carrusel automático en la vista de gestión de campañas que permite visualizar las campañas de forma dinámica, desplazándose automáticamente de izquierda a derecha.

## Ubicación
**URL:** `http://192.168.2.200:8000/configuracion/campanas/consulta`

## Características

### 1. Activación Manual
- El carrusel se activa **solo cuando el usuario lo solicita**
- Existe un toggle (interruptor) en la parte superior de la página
- Estados:
  - **Automático**: Las campañas se desplazan automáticamente
  - **Manual**: El carrusel está detenido

### 2. Selector de Tiempo Personalizado
- **Rango**: De 3 a 30 segundos
- **Control deslizante**: Permite ajustar el intervalo en tiempo real
- **Visualización**: Muestra el tiempo seleccionado en segundos
- **Actualización dinámica**: Si el carrusel está activo, el cambio se aplica inmediatamente

### 3. Funcionalidad del Carrusel
- **Intervalo**: Configurable por el usuario (3-30 segundos)
- **Dirección**: De izquierda a derecha (scroll vertical suave)
- **Efecto visual**: 
  - Resaltado de la campaña activa
  - Escala aumentada (1.02x)
  - Sombra azul brillante
  - Borde azul temporal
  - Centrado automático de la campaña activa

### 4. Controles Visuales
- **Panel de Control Superior**: 
  - Icono de play/pause según el estado
  - Descripción del estado actual con tiempo configurado
  - Toggle para activar/desactivar
  - Control deslizante para ajustar el intervalo
  - Indicador visual del tiempo seleccionado

### 4. Animaciones
- Transición suave entre campañas
- Efecto de entrada con deslizamiento
- Centrado automático de la campaña activa
- Limpieza de efectos al cambiar de campaña

## Componentes Modificados

### Backend (Livewire)
**Archivo:** `app/Http/Livewire/Campanas/Consulta.php`
- Nueva propiedad: `$carruselAutomatico` (boolean)
- Nueva propiedad: `$intervaloCarrusel` (integer, 3-30 segundos)
- Nuevo método: `toggleCarruselAutomatico()` - Activa/desactiva el carrusel
- Nuevo método: `updatedIntervaloCarrusel()` - Actualiza el intervalo en tiempo real
- Evento Livewire 2: `$this->emit('carruselToggled', ...)` - Comunica cambios al frontend

### Frontend (Blade)
**Archivo:** `resources/views/livewire/campanas/consulta.blade.php`
- Panel de control del carrusel
- Estilos CSS para animaciones
- Script JavaScript para el carrusel automático

## Uso

1. Acceder a la vista de campañas: `http://192.168.2.200:8000/configuracion/campanas/consulta`
2. Localizar el panel "Carrusel Automático de Campañas"
3. **Ajustar el intervalo** usando el control deslizante (3-30 segundos)
4. **Activar el carrusel** haciendo clic en el toggle
5. Las campañas comenzarán a desplazarse automáticamente con el intervalo seleccionado
6. Para **cambiar el tiempo** mientras está activo, simplemente mueve el control deslizante
7. Para **detener**, hacer clic nuevamente en el toggle

## Personalización

### Cambiar el Rango de Tiempo Permitido
En el archivo `consulta.blade.php`, en el input range:
```html
<input type="range" 
       wire:model.live="intervaloCarrusel" 
       min="3"    <!-- Tiempo mínimo en segundos -->
       max="30"   <!-- Tiempo máximo en segundos -->
       step="1">
```

Y en el archivo `Consulta.php`, en las reglas de validación:
```php
'intervaloCarrusel' => 'nullable|integer|min:3|max:30',
```

### Modificar Efectos Visuales
En la sección de estilos CSS del mismo archivo:
```css
.campana-card {
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
```

## Notas Técnicas
- Compatible con Livewire 2.x
- Usa `$this->emit()` para eventos (Livewire 2)
- Escucha eventos con `window.addEventListener('carruselToggled')`
- Se reinicia automáticamente al cambiar el intervalo
- Se limpia el intervalo al salir de la página
- Funciona con paginación de campañas
- El intervalo se actualiza dinámicamente sin necesidad de recargar
- Logs en consola para debugging (pueden removerse en producción)
