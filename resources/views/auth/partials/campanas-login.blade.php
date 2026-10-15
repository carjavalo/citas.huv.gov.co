{{-- Campañas, noticias y servicios configurados en Configuración > Campañas, mostrados en el recuadro del login.
     Si no hay campañas vigentes (o la sección está deshabilitada) se sigue mostrando el logo animado. --}}
@php
    $campanasLogin = collect();
    $carruselAutomatico = false;
    $intervaloCarrusel = 5;

    try {
        if (\App\Models\ConfiguracionSistema::estaHabilitado('seccion_novedades_campanas_habilitada')) {
            $campanasLogin = \App\Models\Campana::vigentes()
                ->orderBy('orden')
                ->orderByDesc('created_at')
                ->take(6)
                ->get();
            $carruselAutomatico = \App\Models\ConfiguracionSistema::estaHabilitado('carrusel_campanas_automatico');
            $intervaloCarrusel = min(max((int) \App\Models\ConfiguracionSistema::obtener('carrusel_campanas_intervalo', 5), 3), 30);
        }
    } catch (\Throwable $e) {
        // El login nunca debe fallar por las campañas: ante cualquier error se muestra el logo
        report($e);
        $campanasLogin = collect();
    }

    $categoriasLogin = ['campana' => 'Campaña', 'noticia' => 'Noticia', 'urgente' => 'Urgente', 'servicio' => 'Servicio'];
    $tiposVideoLogin = ['mp4' => 'video/mp4', 'm4v' => 'video/mp4', 'webm' => 'video/webm', 'ogg' => 'video/ogg', 'ogv' => 'video/ogg'];
    $fuentesLogin = ['Inter', 'Arial', 'Georgia', 'Times New Roman', 'Verdana', 'Courier New'];
    $variasCampanas = $campanasLogin->count() > 1;
    // Con carrusel automático los videos pasan a la siguiente campaña al terminar; si no, se repiten
    $repetirVideos = !($carruselAutomatico && $variasCampanas);
@endphp

@if($campanasLogin->isEmpty())
    <img src="{{ asset('img/logoinicio.gif') }}" class="max-w-md w-full" style="max-height: 480px; object-fit: contain;" alt="Logo HUV">
@else
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">
    <style>
        .cl-card { position: relative; width: 100%; max-width: 28rem; border-radius: 14px; overflow: hidden; background: #fff; box-shadow: 0 20px 45px rgba(0, 0, 0, .35); }
        .cl-slides { display: grid; }
        .cl-slide { grid-area: 1 / 1; display: flex; flex-direction: column; opacity: 0; visibility: hidden; transition: opacity .6s ease, visibility .6s; }
        .cl-slide.is-active { opacity: 1; visibility: visible; }
        .cl-media { position: relative; aspect-ratio: 4 / 3; background: #0f1d36; overflow: hidden; }
        .cl-media img, .cl-media video { position: relative; display: block; width: 100%; height: 100%; object-fit: contain; }
        .cl-media a { display: block; width: 100%; height: 100%; }
        /* Copia desenfocada de la imagen para rellenar los lados de los afiches verticales */
        .cl-fondo { position: absolute; inset: -20px; background-size: cover; background-position: center; filter: blur(16px) brightness(.55); }
        .cl-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: rgba(255, 255, 255, .55); }
        .cl-cat-campana .cl-placeholder { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .cl-cat-noticia .cl-placeholder { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
        .cl-cat-servicio .cl-placeholder { background: linear-gradient(135deg, #22c55e, #15803d); }
        .cl-cat-urgente .cl-placeholder { background: linear-gradient(135deg, #ef4444, #b91c1c); }
        .cl-badge { position: absolute; top: 10px; left: 10px; padding: 2px 10px; border-radius: 9999px; font-size: 11px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #fff; background: #2563eb; }
        .cl-badge-noticia { background: #7c3aed; }
        .cl-badge-servicio { background: #16a34a; }
        .cl-badge-urgente { background: #dc2626; }
        .cl-caption { flex: 1; padding: 14px 18px 16px; background: #fff; }
        .cl-caption.is-dark { background: #1e2f4d; }
        .cl-caption h3 { margin: 0; font-weight: 700; line-height: 1.25; }
        .cl-caption p { margin: 6px 0 0; opacity: .85; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .cl-pdf { display: inline-flex; align-items: center; gap: 6px; margin-top: 10px; padding: 5px 12px; border-radius: 9999px; background: #dc2626; color: #fff; font-size: 12px; font-weight: 600; text-decoration: none; }
        .cl-pdf:hover { background: #b91c1c; }
        .cl-nav { position: absolute; top: 0; left: 0; right: 0; aspect-ratio: 4 / 3; pointer-events: none; }
        .cl-flecha { pointer-events: auto; position: absolute; top: 50%; transform: translateY(-50%); width: 34px; height: 34px; border: 0; border-radius: 9999px; background: rgba(255, 255, 255, .85); color: #2c4370; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 6px rgba(0, 0, 0, .25); }
        .cl-flecha:hover { background: #fff; }
        .cl-prev { left: 10px; }
        .cl-next { right: 10px; }
        .cl-dots { pointer-events: auto; position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%); display: flex; gap: 6px; padding: 5px 8px; border-radius: 9999px; background: rgba(0, 0, 0, .35); }
        .cl-dot { width: 9px; height: 9px; padding: 0; border: 0; border-radius: 9999px; background: rgba(255, 255, 255, .55); cursor: pointer; transition: width .3s; }
        .cl-dot.is-active { width: 22px; background: #fff; }
        @media (prefers-reduced-motion: reduce) { .cl-slide, .cl-dot { transition: none; } }
    </style>

    <div id="campanas-login" class="cl-card" aria-roledescription="carrusel" aria-label="Novedades y campañas"
         data-auto="{{ $carruselAutomatico && $variasCampanas ? 1 : 0 }}" data-intervalo="{{ $intervaloCarrusel }}">
        <div class="cl-slides">
            @foreach($campanasLogin as $campana)
                @php
                    // Estilos del texto configurados en la campaña (se validan porque van dentro de style="")
                    $colorTexto = preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', (string) $campana->color_texto) ? $campana->color_texto : '#1f2937';
                    $hex = ltrim($colorTexto, '#');
                    if (strlen($hex) === 3) {
                        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
                    }
                    // Un texto claro (p. ej. blanco) se pone sobre fondo oscuro para que se pueda leer
                    $textoClaro = (0.299 * hexdec(substr($hex, 0, 2)) + 0.587 * hexdec(substr($hex, 2, 2)) + 0.114 * hexdec(substr($hex, 4, 2))) / 255 > 0.6;
                    $fuenteTexto = in_array($campana->fuente_texto, $fuentesLogin, true) ? $campana->fuente_texto : 'Inter';
                    $tamanoTexto = min(max((int) $campana->tamano_texto ?: 16, 10), 28);
                    $tipoVideo = $tiposVideoLogin[strtolower(pathinfo((string) $campana->video, PATHINFO_EXTENSION))] ?? null;
                @endphp
                <article class="cl-slide {{ $loop->first ? 'is-active' : '' }}" aria-hidden="{{ $loop->first ? 'false' : 'true' }}"
                         aria-label="{{ $loop->iteration }} de {{ $loop->count }}">
                    <div class="cl-media cl-cat-{{ $campana->categoria }}">
                        @if($campana->video)
                            <video muted playsinline {{ $loop->first ? 'autoplay' : '' }} {{ $repetirVideos ? 'loop' : '' }}
                                   preload="{{ $loop->first ? 'auto' : 'none' }}"
                                   @if($campana->imagen) poster="{{ asset('storage/' . $campana->imagen) }}" @endif>
                                <source src="{{ asset('storage/' . $campana->video) }}" @if($tipoVideo) type="{{ $tipoVideo }}" @endif>
                            </video>
                        @elseif($campana->imagen)
                            <div class="cl-fondo" style="background-image: url('{{ asset('storage/' . $campana->imagen) }}');"></div>
                            <a href="{{ asset('storage/' . $campana->imagen) }}" target="_blank" rel="noopener" title="Ver imagen completa">
                                <img src="{{ asset('storage/' . $campana->imagen) }}" alt="{{ $campana->titulo }}">
                            </a>
                        @else
                            <div class="cl-placeholder">
                                <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                                </svg>
                            </div>
                        @endif
                        <span class="cl-badge cl-badge-{{ $campana->categoria }}">{{ $categoriasLogin[$campana->categoria] ?? ucfirst($campana->categoria) }}</span>
                    </div>
                    <div class="cl-caption {{ $textoClaro ? 'is-dark' : '' }}">
                        <h3 style="color: {{ $colorTexto }}; font-family: '{{ $fuenteTexto }}', sans-serif; font-size: {{ $tamanoTexto }}px;">
                            {{ $campana->titulo }}
                        </h3>
                        @if($campana->descripcion)
                            <p style="color: {{ $colorTexto }}; font-family: '{{ $fuenteTexto }}', sans-serif; font-size: {{ max($tamanoTexto - 2, 12) }}px;">{{ $campana->descripcion }}</p>
                        @endif
                        @if($campana->archivo_pdf)
                            <a href="{{ asset('storage/' . $campana->archivo_pdf) }}" target="_blank" rel="noopener" class="cl-pdf">
                                <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                                </svg>
                                Ver PDF
                            </a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        @if($variasCampanas)
            <div class="cl-nav">
                <button type="button" class="cl-flecha cl-prev" data-paso="-1" aria-label="Campaña anterior">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button type="button" class="cl-flecha cl-next" data-paso="1" aria-label="Campaña siguiente">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </button>
                <div class="cl-dots">
                    @foreach($campanasLogin as $campana)
                        <button type="button" class="cl-dot {{ $loop->first ? 'is-active' : '' }}" data-indice="{{ $loop->index }}" aria-label="Ver campaña {{ $loop->iteration }}"></button>
                    @endforeach
                </div>
            </div>

            <script>
                (function () {
                    var raiz = document.getElementById('campanas-login');
                    var slides = raiz.querySelectorAll('.cl-slide');
                    var puntos = raiz.querySelectorAll('.cl-dot');
                    var automatico = raiz.dataset.auto === '1';
                    var intervalo = (parseInt(raiz.dataset.intervalo, 10) || 5) * 1000;
                    var actual = 0;
                    var temporizador = null;

                    function programarSiguiente(forzarTiempo) {
                        clearTimeout(temporizador);
                        if (!automatico) return;
                        // Las imágenes cambian con el intervalo configurado; los videos, al terminar (evento "ended")
                        if (forzarTiempo || !slides[actual].querySelector('video')) {
                            temporizador = setTimeout(function () { mostrar(actual + 1); }, intervalo);
                        }
                    }

                    function mostrar(indice) {
                        var videoAnterior = slides[actual].querySelector('video');
                        if (videoAnterior) videoAnterior.pause();

                        actual = (indice + slides.length) % slides.length;
                        slides.forEach(function (slide, i) {
                            slide.classList.toggle('is-active', i === actual);
                            slide.setAttribute('aria-hidden', i === actual ? 'false' : 'true');
                        });
                        puntos.forEach(function (punto, i) { punto.classList.toggle('is-active', i === actual); });

                        var video = slides[actual].querySelector('video');
                        if (video) {
                            video.preload = 'auto';
                            try { video.currentTime = 0; } catch (e) {}
                            var reproduccion = video.play();
                            if (reproduccion && reproduccion.catch) reproduccion.catch(function () {});
                        }
                        programarSiguiente(false);
                    }

                    raiz.querySelectorAll('video').forEach(function (video) {
                        var slide = video.closest('.cl-slide');
                        video.addEventListener('ended', function () {
                            if (automatico && slides[actual] === slide) mostrar(actual + 1);
                        });
                        // Si el navegador no puede reproducir el video, que el carrusel no se quede detenido en él
                        var fuente = video.querySelector('source');
                        if (fuente) fuente.addEventListener('error', function () {
                            if (slides[actual] === slide) programarSiguiente(true);
                        });
                    });

                    raiz.querySelectorAll('.cl-flecha').forEach(function (boton) {
                        boton.addEventListener('click', function () { mostrar(actual + parseInt(boton.dataset.paso, 10)); });
                    });
                    puntos.forEach(function (punto) {
                        punto.addEventListener('click', function () { mostrar(parseInt(punto.dataset.indice, 10)); });
                    });

                    programarSiguiente(false);
                })();
            </script>
        @endif
    </div>
@endif
