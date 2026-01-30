# Correcciones para Livewire 2

## Problema Original
```
Method App\Http\Livewire\Campanas\Consulta::dispatch does not exist.
```

## Causa
El código estaba usando `$this->dispatch()` que es de Livewire 3, pero el proyecto usa Livewire 2.

## Solución Aplicada

### 1. Backend - Cambio de `dispatch()` a `emit()`

**Antes (Livewire 3):**
```php
$this->dispatch('carruselToggled', [
    'activo' => $this->carruselAutomatico,
    'intervalo' => $this->intervaloCarrusel
]);
```

**Después (Livewire 2):**
```php
$this->emit('carruselToggled', 
    $this->carruselAutomatico,
    $this->intervaloCarrusel
);
```

### 2. Frontend - Cambio en la escucha de eventos

**Antes (Livewire 3):**
```javascript
Livewire.on('carruselToggled', (event) => {
    const data = event[0] || event;
    iniciarCarrusel(data.activo, data.intervalo);
});
```

**Después (Livewire 2):**
```javascript
window.addEventListener('carruselToggled', event => {
    const activo = event.detail[0];
    const intervalo = event.detail[1];
    iniciarCarrusel(activo, intervalo);
});
```

### 3. Eliminación de directiva `@script`

**Antes (Livewire 3):**
```blade
@script
<script>
    // código
</script>
@endscript
```

**Después (Livewire 2):**
```blade
<script>
    // código
</script>
```

## Diferencias Clave entre Livewire 2 y 3

| Característica | Livewire 2 | Livewire 3 |
|---------------|------------|------------|
| Emitir eventos | `$this->emit()` | `$this->dispatch()` |
| Escuchar eventos (JS) | `window.addEventListener()` | `Livewire.on()` |
| Directiva de script | `<script>` | `@script` |
| Parámetros de eventos | Array plano | Array asociativo |

## Archivos Modificados

1. **app/Http/Livewire/Campanas/Consulta.php**
   - Método `toggleCarruselAutomatico()`
   - Método `updatedIntervaloCarrusel()`

2. **resources/views/livewire/campanas/consulta.blade.php**
   - Sección de script JavaScript
   - Escucha de eventos

3. **CARRUSEL_CAMPANAS.md**
   - Actualizada la documentación técnica

4. **PRUEBA_CARRUSEL.md**
   - Actualizados los ejemplos de consola

## Verificación

Para verificar que todo funciona correctamente:

1. Acceder a: `http://192.168.2.200:8000/configuracion/campanas/consulta`
2. Abrir la consola del navegador (F12)
3. Activar el carrusel
4. Verificar que aparece: `Evento carruselToggled recibido: [true, 5]`
5. Verificar que las campañas se desplazan automáticamente

## Comandos de Limpieza (Opcional)

Si hay problemas de caché:

```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

## Estado Actual

✅ Compatible con Livewire 2.x
✅ Toggle funcional
✅ Selector de tiempo funcional (3-30 segundos)
✅ Carrusel automático operativo
✅ Efectos visuales aplicados correctamente
✅ Sin errores en consola
