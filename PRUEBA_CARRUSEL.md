# Guía de Prueba - Carrusel Automático

## Pasos para Probar

### 1. Acceder a la Vista
```
URL: http://192.168.2.200:8000/configuracion/campanas/consulta
```

### 2. Verificar Elementos Visuales
- [ ] Panel "Carrusel Automático de Campañas" visible
- [ ] Toggle (interruptor) presente
- [ ] Control deslizante de intervalo visible
- [ ] Indicador de tiempo (ej: "5s") visible

### 3. Probar Selector de Tiempo
1. Mover el control deslizante
2. Verificar que el número cambia (3s - 30s)
3. El carrusel debe estar DESACTIVADO aún

### 4. Activar Carrusel
1. Hacer clic en el toggle
2. Debe cambiar a color azul
3. Debe mostrar "Automático"
4. Debe aparecer mensaje flash: "Carrusel automático ACTIVADO..."
5. Las campañas deben empezar a desplazarse

### 5. Verificar Funcionamiento
- [ ] Las campañas se desplazan automáticamente
- [ ] El scroll es suave
- [ ] La campaña activa tiene:
  - Borde azul
  - Sombra azul brillante
  - Escala ligeramente mayor
- [ ] El intervalo respeta el tiempo configurado

### 6. Cambiar Intervalo en Tiempo Real
1. Con el carrusel ACTIVO
2. Mover el control deslizante a otro valor (ej: 10s)
3. El carrusel debe ajustarse al nuevo intervalo inmediatamente

### 7. Desactivar Carrusel
1. Hacer clic nuevamente en el toggle
2. Debe cambiar a color gris
3. Debe mostrar "Manual"
4. Las campañas deben dejar de moverse
5. Los efectos visuales deben desaparecer

## Verificación en Consola del Navegador

Abrir las herramientas de desarrollo (F12) y verificar:

### Al Activar:
```
Evento carruselToggled recibido: [true, 5]
Carrusel iniciado con intervalo de 5 segundos
Mostrando campaña 1 de X
Mostrando campaña 2 de X
...
```

### Al Desactivar:
```
Evento carruselToggled recibido: [false, 5]
Carrusel detenido
```

### Al Cambiar Intervalo:
```
Evento carruselToggled recibido: [true, 10]
Carrusel iniciado con intervalo de 10 segundos
```

## Problemas Comunes y Soluciones

### El toggle no responde
- Verificar que Livewire esté cargado correctamente
- Revisar la consola del navegador por errores JavaScript
- Verificar que el método `toggleCarruselAutomatico()` existe en el componente

### El carrusel no se mueve
- Verificar que hay campañas en la lista
- Revisar la consola: debe mostrar "Carrusel iniciado..."
- Verificar que el evento `carruselToggled` se está disparando

### El intervalo no cambia
- Verificar que `wire:model.live` está en el input range
- Revisar que el método `updatedIntervaloCarrusel()` existe
- Verificar en la consola que el evento se dispara con el nuevo intervalo

### Los efectos visuales no se aplican
- Verificar que las campañas tienen la clase `campana-card`
- Revisar los estilos CSS en el navegador
- Verificar que el selector `.campana-card` encuentra elementos

## Comandos de Depuración

### Limpiar caché de Livewire
```bash
php artisan livewire:discover
php artisan view:clear
php artisan cache:clear
```

### Verificar logs de Laravel
```bash
tail -f storage/logs/laravel.log
```

## Checklist Final

- [ ] El toggle funciona correctamente
- [ ] El selector de tiempo responde (3-30s)
- [ ] El carrusel se activa al hacer clic en el toggle
- [ ] Las campañas se desplazan con el intervalo correcto
- [ ] Los efectos visuales se aplican correctamente
- [ ] El intervalo se puede cambiar en tiempo real
- [ ] El carrusel se desactiva correctamente
- [ ] No hay errores en la consola del navegador
- [ ] Los mensajes flash aparecen correctamente
