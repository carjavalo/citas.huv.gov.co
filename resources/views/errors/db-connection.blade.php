@php
    $colores = [
        'fondo' => '#f5f6fa', // Fondo principal
        'primario' => '#005baa', // Azul corporativo
        'secundario' => '#f9a825', // Amarillo corporativo
        'texto' => '#222f3e', // Texto principal
        'boton' => '#005baa',
        'boton_texto' => '#fff',
    ];
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sin conexión a la base de datos</title>
    <style>
        body {
            background: {{ $colores['fondo'] }};
            color: {{ $colores['texto'] }};
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 48px 32px 32px 32px;
            text-align: center;
            max-width: 400px;
        }
        .logo {
            width: 120px;
            margin-bottom: 24px;
        }
        .mensaje {
            font-size: 1.3rem;
            margin-bottom: 32px;
            color: {{ $colores['primario'] }};
        }
        .btn-reintentar {
            background: {{ $colores['boton'] }};
            color: {{ $colores['boton_texto'] }};
            border: none;
            border-radius: 8px;
            padding: 12px 32px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-reintentar:hover {
            background: {{ $colores['secundario'] }};
            color: {{ $colores['texto'] }};
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="/img/logoinicio.gif" alt="Logo" class="logo">
        <div class="mensaje">
            <strong>No hay conexión con la base de datos.</strong>
        </div>
        <form method="GET">
            <button type="submit" class="btn-reintentar">Reintentar</button>
        </form>
    </div>
</body>
</html>
