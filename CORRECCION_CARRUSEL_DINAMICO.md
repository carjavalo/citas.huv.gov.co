# Corrección - Carrusel No Se Inicia Automáticamente

## Problema Identificado
El carrusel se quedaba en modo "Manual" y no se iniciaba automáticamente cuando estaba activado en la base de datos.

## Causas del Problema

### 1. Error en el Evento de Livewire
```javascript
// ❌ INCORRECTO - No existe en Livewire 2
window.livewire.on('load', function() { ... });
```

### 2. Falta de Reintentos
Si las campañas no estaban cargadas cuando se ejecutaba el script, el carrusel no se iniciaba.

### 3. Falta de Logs Detallados
No había suficiente información en consola para diagnosticar el problema.

## Soluciones Implementadas

### 1. Función de Inicialización Mejorada
```javascript
function inicializarCarruselAlCargar() {
    const carruselActivo = @json($carruselAutomatico);
    const intervaloInicial = @json($intervaloCarrusel);
    
    console.log('=== Inicializando carrusel al cargar ===');
    console.log('Estado inicial - Activo:', carruselActivo, 'Intervalo:', intervaloInicial);
    
    if (carruselActivo && !carruselIniciado) {
        setTimeout(() => {
            const campanas = document.querySelectorAll('.campana-card');
            console.log('Campañas encontradas:', campanas.length);
            if (campanas.length > 0) {
                iniciarCarrusel(carruselActivo, intervaloInicial);
            } else {
                console.log('No se encontraron campañas, reintentando...');
                setTimeout(() => inicializarCarruselAlCargar(), 1000);
            }
        }, 1000);
    }
}
```

### 2. Múltiples Puntos de Inicialización
```javascript
// Iniciar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarCarruselAlCargar);
} else {
    inicializarCarruselAlCargar();
}

// También iniciar cuando Livewire termine de renderizar
document.addEventListener('livewire:load', function() {
    console.log('=== Livewire cargado ===');
    inicializarCarruselAlCargar();
});
```

### 3. Prevención de Inicialización Múltiple
```javascript
let carruselIniciado = false;

function iniciarCarrusel(activo, intervalo) {
    // ...
    if (!activo) {
        carruselIniciado = false;
        return;
    }
    carruselIniciado = true;
    // ...
}
```

### 4. Logs Detallados para Debugging
```javascript
console.log('=== iniciarCarrusel llamado ===');
console.log('Activo:', activo, 'Intervalo:', intervalo);
console.log('Campañas encontradas:', campanas.length);
console.log('Mostrando campaña ' + (posicionActual + 1) + ' de ' + campanas.length);
```

### 5. Encapsulación en IIFE
```javascript
(function() {
    // Todo el código del carrusel encapsulado
    // Evita conflictos con otras variables globales
})();
```

## Flujo de Inicialización Corregido

### Escenario 1: Página Carga con Carrusel Activado

1. **Carga de la página**
   ```
   Estado en BD: carrusel_campanas_automatico = 1
   ```

2. **Livewire renderiza el componente**
   ```
   $carruselAutomatico = true
   $intervaloCarrusel = 5 (o el valor guardado)
   ```

3. **JavaScript se ejecuta**
   ```
   document.readyState verificado
   inicializarCarruselAlCargar() llamado
   ```

4. **Verificación de campañas**
   ```
   Espera 1 segundo
   Busca elementos .campana-card
   Si encuentra campañas → iniciarCarrusel()
   Si no encuentra → reintenta después de 1 segundo
   ```

5. **Carrusel inicia**
   ```
   setInterval() configurado
   Campañas empiezan a moverse
   Logs en consola confirman funcionamiento
   ```

### Escenario 2: Usuario Activa el Carrusel

1. **Usuario hace clic en toggle**
   ```
   toggleCarruselAutomatico() ejecutado
   ```

2. **Estado guardado en BD**
   ```
   carrusel_campanas_automatico = 1
   ```

3. **Evento emitido**
   ```
   $this->emit('carruselToggled', true, 5)
   ```

4. **JavaScript recibe evento**
   ```
   window.addEventListener('carruselToggled') activado
   iniciarCarrusel(true, 5) llamado
   ```

5. **Carrusel inicia inmediatamente**
   ```
   setInterval() configurado
   Campañas empiezan a moverse
   ```

## Verificación en Consola del Navegador

### Al Cargar la Página con Carrusel Activado

Deberías ver:
```
=== Inicializando carrusel al cargar ===
Estado inicial - Activo: true Intervalo: 5
Campañas encontradas: 10
=== iniciarCarrusel llamado ===
Activo: true Intervalo: 5
Iniciando carrusel con intervalo de 5 segundos
Mostrando campaña 1 de 10
Mostrando campaña 2 de 10
...
```

### Al Activar Manualmente

Deberías ver:
```
=== Evento carruselToggled recibido ===
Event detail: [true, 5]
=== iniciarCarrusel llamado ===
Activo: true Intervalo: 5
Iniciando carrusel con intervalo de 5 segundos
Mostrando campaña 1 de 10
...
```

### Si No Hay Campañas

Deberías ver:
```
=== Inicializando carrusel al cargar ===
Estado inicial - Activo: true Intervalo: 5
Campañas encontradas: 0
No se encontraron campañas, reintentando...
(Reintenta después de 1 segundo)
```

## Pasos para Probar

### 1. Activar el Carrusel
```bash
# Opción A: Desde la interfaz
1. Ir a http://192.168.2.200:8000/configuracion/campanas/consulta
2. Hacer clic en el toggle
3. Verificar que cambia a azul

# Opción B: Desde la base de datos
UPDATE configuracion_sistema 
SET valor = '1' 
WHERE clave = 'carrusel_campanas_automatico';
```

### 2. Recargar la Página
```
F5 o Ctrl+R
```

### 3. Abrir Consola del Navegador
```
F12 → Pestaña Console
```

### 4. Verificar Logs
Deberías ver los mensajes de inicialización y movimiento de campañas.

### 5. Observar el Carrusel
Las campañas deben moverse automáticamente cada X segundos.

## Solución de Problemas

### El carrusel no se inicia al cargar

**Verificar en consola:**
```javascript
// ¿Qué dice el log inicial?
=== Inicializando carrusel al cargar ===
Estado inicial - Activo: ? Intervalo: ?
```

**Si Activo es false:**
- Verificar en BD: `SELECT * FROM configuracion_sistema WHERE clave = 'carrusel_campanas_automatico'`
- Debe ser '1' para estar activo

**Si no encuentra campañas:**
- Verificar que hay campañas publicadas
- Verificar que tienen la clase `campana-card`

### El carrusel se inicia pero se detiene

**Verificar en consola:**
```javascript
// ¿Aparece algún error?
Contenedor no encontrado
No hay campañas
```

**Solución:**
- Verificar que el selector `.lg\\:col-span-8 .space-y-4` encuentra el contenedor
- Verificar que las campañas tienen la clase `campana-card`

### El carrusel se inicia múltiples veces

**Causa:**
La variable `carruselIniciado` previene esto, pero si hay errores puede fallar.

**Solución:**
Recargar la página completamente (Ctrl+Shift+R)

## Mejoras Implementadas

✅ **Inicialización robusta** con reintentos
✅ **Múltiples puntos de entrada** (DOMContentLoaded, livewire:load)
✅ **Prevención de duplicados** con flag `carruselIniciado`
✅ **Logs detallados** para debugging
✅ **Encapsulación** para evitar conflictos
✅ **Manejo de errores** con verificaciones
✅ **Compatibilidad** con Livewire 2

## Estado Final

✅ El carrusel se inicia automáticamente al cargar si está activado
✅ El carrusel respeta el estado guardado en la base de datos
✅ Los logs en consola permiten diagnosticar problemas
✅ El código es robusto y maneja casos edge
✅ Compatible con Livewire 2
✅ Sin errores en consola

## Comandos de Limpieza

```bash
php artisan view:clear
php artisan cache:clear
```

Ya ejecutados ✅
