{{--
    Usuario relacionado con una fila del informe. Espera $fila.
    En las importadas es el usuario recién creado; en las que ya existían, el
    que tenía esa identificación o ese correo (la prueba de por qué no entró).
--}}
@if (!$fila->user_id)
    <span class="apagado">—</span>
@elseif ($fila->estado === \App\Models\ImportacionUsuarioDetalle::IMPORTADO)
    <span class="impu-pastilla impu-verde" title="Usuario creado por esta importación">#{{ $fila->user_id }}</span>
@else
    <span class="impu-pastilla impu-ambar" title="Usuario que ya tenía esa identificación o ese correo">ya existe #{{ $fila->user_id }}</span>
@endif
