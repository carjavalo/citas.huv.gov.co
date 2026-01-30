# Persistencia del Estado del Carrusel

## Problema Resuelto
El carrusel se activaba pero al recargar la página volvía a desactivarse porque el estado no se guardaba.

## Solución Implementada

### 1. Almacenamiento en Base de Datos
El estado del carrusel ahora se guarda en la tabla `configuracion_sistema` con dos claves:

- **`carrusel_campanas_automatico`**: Estado del carrusel (0=desactivado, 1=activado)
- **`carrusel_campanas_intervalo`**: Intervalo en segundos (3-30)

### 2. Migración Ejecutada
Se creó y ejecutó la migración:
```
2026_01_20_143553_add_carrusel_config_to_configuracion_sistema.php
```

Esta migración inserta los valores por defecto:
- Estado: Desactivado (0)
- Intervalo: 5 segundos

### 3. Modificaciones en el Componente

#### Método `mount()`
Ahora carga el estado guardado al iniciar:
```php
public function mount()
{
    $this->authorize('admin.servicios.consultar');
    $this->seccionHabilitada = ConfiguracionSistema::estaHabilitado('seccion_novedades_campanas_habilitada');
    
    // Cargar estado del carrusel desde la configuración
    $this->carruselAutomatico = ConfiguracionSistema::estaHabilitado('carrusel_campanas_automatico');
    $intervaloGuardado = ConfiguracionSistema::obtener('carrusel_campanas_intervalo');
    $this->intervaloCarrusel = $intervaloGuardado ? (int)$intervaloGuardado : 5;
}
```

#### Método `toggleCarruselAutomatico()`
Ahora guarda el estado al cambiar:
```php
public function toggleCarruselAutomatico()
{
    $this->carruselAutomatico = !$this->carruselAutomatico;
    
    // Guardar en la configuración del sistema
    ConfiguracionSistema::establecer(
        'carrusel_campanas_automatico',
        $this->carruselAutomatico ? '1' : '0',
        'Estado del carrusel automático de campañas'
    );
    
    // ... resto del código
}
```

#### Método `updatedIntervaloCarrusel()`
Ahora guarda el intervalo al cambiar:
```php
public function updatedIntervaloCarrusel()
{
    // Guardar el intervalo en la configuración del sistema
    ConfiguracionSistema::establecer(
        'carrusel_campanas_intervalo',
        (string)$this->intervaloCarrusel,
        'Intervalo en segundos del carrusel automático de campañas'
    );
    
    // ... resto del código
}
```

### 4. Mejoras en JavaScript
Se agregó un timeout para asegurar que el DOM esté completamente cargado:
```javascript
if (carruselActivo) {
    setTimeout(() => {
        iniciarCarrusel(carruselActivo, intervaloInicial);
    }, 500);
}
```

## Flujo de Funcionamiento

### Al Cargar la Página
1. El método `mount()` se ejecuta
2. Lee el estado desde `configuracion_sistema`
3. Carga `$carruselAutomatico` y `$intervaloCarrusel`
4. El JavaScript detecta estos valores
5. Si está activado, inicia el carrusel automáticamente

### Al Activar el Carrusel
1. Usuario hace clic en el toggle
2. `toggleCarruselAutomatico()` se ejecuta
3. Cambia el estado de `$carruselAutomatico`
4. **Guarda en la base de datos** con `ConfiguracionSistema::establecer()`
5. Emite evento a JavaScript
6. JavaScript inicia el carrusel

### Al Cambiar el Intervalo
1. Usuario mueve el control deslizante
2. `updatedIntervaloCarrusel()` se ejecuta
3. **Guarda el nuevo intervalo en la base de datos**
4. Si el carrusel está activo, emite evento
5. JavaScript reinicia el carrusel con el nuevo intervalo

### Al Recargar la Página
1. El método `mount()` lee los valores guardados
2. Si el carrusel estaba activado, se mantiene activado
3. El intervalo configurado se mantiene
4. El JavaScript inicia automáticamente si está activado

## Verificación en Base de Datos

Puedes verificar los valores guardados con:

```sql
SELECT * FROM configuracion_sistema 
WHERE clave IN ('carrusel_campanas_automatico', 'carrusel_campanas_intervalo');
```

Resultado esperado:
```
| clave                        | valor | descripcion                                              |
|------------------------------|-------|----------------------------------------------------------|
| carrusel_campanas_automatico | 0/1   | Estado del carrusel automático de campañas               |
| carrusel_campanas_intervalo  | 3-30  | Intervalo en segundos del carrusel automático de campañas|
```

## Beneficios

✅ **Persistencia**: El estado se mantiene entre sesiones
✅ **Multi-usuario**: Todos los usuarios ven el mismo estado
✅ **Configuración global**: Se aplica a toda la aplicación
✅ **Fácil de modificar**: Se puede cambiar desde la interfaz o directamente en la BD
✅ **Auditable**: Se puede ver quién y cuándo cambió la configuración (timestamps)

## Comandos Útiles

### Ver configuración actual
```bash
php artisan tinker
>>> App\Models\ConfiguracionSistema::obtener('carrusel_campanas_automatico');
>>> App\Models\ConfiguracionSistema::obtener('carrusel_campanas_intervalo');
```

### Cambiar configuración manualmente
```bash
php artisan tinker
>>> App\Models\ConfiguracionSistema::establecer('carrusel_campanas_automatico', '1');
>>> App\Models\ConfiguracionSistema::establecer('carrusel_campanas_intervalo', '10');
```

### Resetear a valores por defecto
```bash
php artisan migrate:rollback --step=1
php artisan migrate
```

## Archivos Modificados

1. **app/Http/Livewire/Campanas/Consulta.php**
   - Método `mount()` - Carga estado guardado
   - Método `toggleCarruselAutomatico()` - Guarda estado
   - Método `updatedIntervaloCarrusel()` - Guarda intervalo

2. **resources/views/livewire/campanas/consulta.blade.php**
   - JavaScript mejorado con timeout

3. **database/migrations/2026_01_20_143553_add_carrusel_config_to_configuracion_sistema.php**
   - Nueva migración para valores por defecto

## Prueba de Persistencia

1. Activar el carrusel
2. Cambiar el intervalo a 10 segundos
3. Recargar la página (F5)
4. ✅ El carrusel debe seguir activado
5. ✅ El intervalo debe ser 10 segundos
6. ✅ Las campañas deben desplazarse automáticamente
