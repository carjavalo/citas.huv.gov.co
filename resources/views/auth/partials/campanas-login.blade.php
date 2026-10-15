{{-- Carrusel con todo el material (videos, imágenes y PDF) de las campañas, noticias y servicios configurados
     en Configuración > Campañas, mostrado en el recuadro del login. Cada material es una diapositiva.
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
                ->take(10)
                ->get();
            // "Carrusel Automático de Campañas" e "Intervalo de cambio" de Configuración > Campañas
            $carruselAutomatico = \App\Models\ConfiguracionSistema::estaHabilitado('carrusel_campanas_automatico');
            $intervaloCarrusel =min(max((int) \App\Models\ConfiguracionSistema::obtener('carrusel_campanas_intervalo', 5), 3), 30);
        }
    } catch (\Throwable $e) {
        // El login nunca debe fallar por las campañas: ante cualquier error se muestra el logo
        report($e);
        $campanasLogin = collect();
    }

    $categoriasLogin = ['campana' => 'Campaña', 'noticia' => 'Noticia', 'urgente' => 'Urgente', 'servicio' => 'Servicio'];
    $tiposVideoLogin = ['mp4' => 'video/mp4', 'm4v' => 'video/mp4', 'webm' => 'video/webm', 'ogg' => 'video/ogg', 'ogv' => 'video/ogg'];
    $fuentesLogin = ['Inter', 'Arial', 'Georgia', 'Times New Roman', 'Verdana', 'Courier New'];

    // Una diapositiva por cada material de la campaña: video, imagen y PDF (o solo el texto si no tiene ninguno)
    $diapositivasLogin = [];
    foreach ($campanasLogin as $campana) {
        // Estilos del texto configurados en la campaña (se validan porque van dentro de style="")
        $colorTexto = preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', (string) $campana->color_texto) ? $campana->color_texto : '#1f2937';
        $hex = ltrim($colorTexto, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        $estilo = [
            'color' => $colorTexto,
            // Un texto claro (p. ej. blanco) se pone sobre fondo oscuro para que se pueda leer
            'claro' => (0.299 * hexdec(substr($hex, 0, 2)) + 0.587 * hexdec(substr($hex, 2, 2)) + 0.114 * hexdec(substr($hex, 4, 2))) / 255 > 0.6,
            'fuente' => in_array($campana->fuente_texto, $fuentesLogin, true) ? $campana->fuente_texto : 'Inter',
            'tamano' => min(max((int) $campana->tamano_texto ?: 16, 10), 28),
        ];

        $materiales = array_keys(array_filter([
            'video' => $campana->video,
            'imagen' => $campana->imagen,
            'pdf' => $campana->archivo_pdf,
        ])) ?: ['texto'];

        foreach ($materiales as $material) {
            $diapositivasLogin[] = ['campana' => $campana, 'material' => $material, 'estilo' => $estilo];
        }
    }
    $totalDiapositivas = count($diapositivasLogin);
@endphp

@if($totalDiapositivas === 0)
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
        .cl-media > a { display: block; width: 100%; height: 100%; }
        /* Copia desenfocada de la imagen para rellenar los lados de los afiches verticales */
        .cl-fondo { position: absolute; inset: -20px; background-size: cover; background-position: center; filter: blur(16px) brightness(.55); }
        .cl-pdf-marco { width: 100%; height: 100%; }
        .cl-pdf-marco > a { display: block; width: 100%; height: 100%; padding: 10px; }
        .cl-pdf-marco canvas { display: block; width: 100%; height: 100%; object-fit: contain; }
        .cl-pdf-alterno { height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px; color: #fff; font-weight: 600; background: linear-gradient(135deg, #ef4444, #991b1b); }
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
        .cl-pdf-alterno .cl-pdf { margin-top: 0; background: #fff; color: #b91c1c; }
        .cl-nav { position: absolute; top: 0; left: 0; right: 0; aspect-ratio: 4 / 3; pointer-events: none; }
        .cl-flecha { pointer-events: auto; position: absolute; top: 50%; transform: translateY(-50%); width: 34px; height: 34px; border: 0; border-radius: 9999px; background: rgba(255, 255, 255, .85); color: #2c4370; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 6px rgba(0, 0, 0, .25); }
        .cl-flecha:hover { background: #fff; }
        .cl-prev { left: 10px; }
        .cl-next { right: 10px; }
        .cl-dots { pointer-events: auto; position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%); display: flex; align-items: center; gap: 6px; padding: 5px 8px; border-radius: 9999px; background: rgba(0, 0, 0, .35); color: #fff; font-size: 12px; font-weight: 600; }
        .cl-dot { width: 9px; height: 9px; padding: 0; border: 0; border-radius: 9999px; background: rgba(255, 255, 255, .55); cursor: pointer; transition: width .3s; }
        .cl-dot.is-active { width: 22px; background: #fff; }
        @media (prefers-reduced-motion: reduce) { .cl-slide, .cl-dot { transition: none; } }
    </style>

    <div id="campanas-login" class="cl-card" aria-roledescription="carrusel" aria-label="Novedades y campañas"
         data-auto="{{ $carruselAutomatico ? 1 : 0 }}" data-intervalo="{{ $intervaloCarrusel }}">
        <div class="cl-slides">
            @foreach($diapositivasLogin as $diapositiva)
                @php
                    $campana = $diapositiva['campana'];
                    $estilo = $diapositiva['estilo'];
                    $urlVideo = $campana->video ? asset('storage/' . $campana->video) : null;
                    $urlImagen = $campana->imagen ? asset('storage/' . $campana->imagen) : null;
                    $urlPdf = $campana->archivo_pdf ? asset('storage/' . $campana->archivo_pdf) : null;
                    $tipoVideo = $tiposVideoLogin[strtolower(pathinfo((string) $campana->video, PATHINFO_EXTENSION))] ?? null;
                @endphp
                {{-- Solo la primera diapositiva trae sus archivos de entrada; las demás se cargan al acercarse (data-*) --}}
                <article class="cl-slide {{ $loop->first ? 'is-active' : '' }}" aria-hidden="{{ $loop->first ? 'false' : 'true' }}"
                         aria-label="{{ $loop->iteration }} de {{ $loop->count }}">
                    <div class="cl-media cl-cat-{{ $campana->categoria }}">
                        @switch($diapositiva['material'])
                            @case('video')
                                {{-- Se repite mientras está en pantalla; el carrusel lo cambia con el intervalo configurado --}}
                                <video muted playsinline loop {{ $loop->first ? 'autoplay' : '' }}
                                       preload="{{ $loop->first ? 'auto' : 'none' }}"
                                       @if($urlImagen) {{ $loop->first ? 'poster' : 'data-poster' }}="{{ $urlImagen }}" @endif>
                                    <source src="{{ $urlVideo }}" @if($tipoVideo) type="{{ $tipoVideo }}" @endif>
                                </video>
                                @break
                            @case('imagen')
                                <div class="cl-fondo" @if($loop->first) style="background-image: url('{{ $urlImagen }}');" @else data-fondo="{{ $urlImagen }}" @endif></div>
                                <a href="{{ $urlImagen }}" target="_blank" rel="noopener" title="Ver imagen completa">
                                    <img {{ $loop->first ? 'src' : 'data-src' }}="{{ $urlImagen }}" alt="{{ $campana->titulo }}">
                                </a>
                                @break
                            @case('pdf')
                                {{-- Al mostrar la diapositiva se dibuja la primera página con pdf.js; si no se puede, queda el botón --}}
                                <div class="cl-pdf-marco" data-pdf="{{ $urlPdf }}">
                                    <div class="cl-pdf-alterno">
                                        <svg width="56" height="56" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                                        </svg>
                                        Documento PDF
                                        <a href="{{ $urlPdf }}" target="_blank" rel="noopener" class="cl-pdf">Abrir PDF</a>
                                    </div>
                                </div>
                                @break
                            @default
                                <div class="cl-placeholder">
                                    <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                                    </svg>
                                </div>
                        @endswitch
                        <span class="cl-badge cl-badge-{{ $campana->categoria }}">{{ $categoriasLogin[$campana->categoria] ?? ucfirst($campana->categoria) }}</span>
                    </div>
                    <div class="cl-caption {{ $estilo['claro'] ? 'is-dark' : '' }}">
                        <h3 style="color: {{ $estilo['color'] }}; font-family: '{{ $estilo['fuente'] }}', sans-serif; font-size: {{ $estilo['tamano'] }}px;">
                            {{ $campana->titulo }}
                        </h3>
                        @if($campana->descripcion)
                            <p style="color: {{ $estilo['color'] }}; font-family: '{{ $estilo['fuente'] }}', sans-serif; font-size: {{ max($estilo['tamano'] - 2, 12) }}px;">{{ $campana->descripcion }}</p>
                        @endif
                        @if($urlPdf)
                            <a href="{{ $urlPdf }}" target="_blank" rel="noopener" class="cl-pdf">
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

        @if($totalDiapositivas > 1)
            <div class="cl-nav">
                <button type="button" class="cl-flecha cl-prev" data-paso="-1" aria-label="Anterior">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button type="button" class="cl-flecha cl-next" data-paso="1" aria-label="Siguiente">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </button>
                <div class="cl-dots">
                    @if($totalDiapositivas <= 12)
                        @foreach($diapositivasLogin as $diapositiva)
                            <button type="button" class="cl-dot {{ $loop->first ? 'is-active' : '' }}" data-indice="{{ $loop->index }}" aria-label="Ver {{ $loop->iteration }}"></button>
                        @endforeach
                    @else
                        <span class="cl-contador">1 / {{ $totalDiapositivas }}</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <script>
        (function () {
            var raiz = document.getElementById('campanas-login');
            var slides = raiz.querySelectorAll('.cl-slide');
            var puntos = raiz.querySelectorAll('.cl-dot');
            var contador = raiz.querySelector('.cl-contador');
            var automatico = raiz.dataset.auto === '1';
            var intervalo = (parseInt(raiz.dataset.intervalo, 10) || 5) * 1000;
            var actual = 0;
            var temporizador = null;
            var pausado = false;
            var promesaPdfJs = null;

            // pdf.js se descarga solo si hay que mostrar algún PDF
            function obtenerPdfJs() {
                if (!promesaPdfJs) {
                    promesaPdfJs = new Promise(function (resolver, rechazar) {
                        var script = document.createElement('script');
                        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
                        script.integrity = 'sha512-q+4liFwdPC/bNdhUpZx6aXDx/h77yEQtn4I1slHydcbZK34nLaR3cAeYSJshoxIOq3mjEf7xJE8YWIUHMn+oCQ==';
                        script.crossOrigin = 'anonymous';
                        script.onload = function () {
                            window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                            resolver(window.pdfjsLib);
                        };
                        script.onerror = rechazar;
                        document.head.appendChild(script);
                    });
                }
                return promesaPdfJs;
            }

            // Dibuja la primera página del PDF; al hacer clic se abre el documento completo
            function dibujarPdf(marco) {
                var url = marco.dataset.pdf;
                marco.removeAttribute('data-pdf');
                obtenerPdfJs()
                    .then(function (pdfjsLib) { return pdfjsLib.getDocument(url).promise; })
                    .then(function (pdf) { return pdf.getPage(1); })
                    .then(function (pagina) {
                        var ancho = (marco.clientWidth || 448) * (window.devicePixelRatio || 1);
                        var vista = pagina.getViewport({ scale: ancho / pagina.getViewport({ scale: 1 }).width });
                        var lienzo = document.createElement('canvas');
                        lienzo.width = Math.floor(vista.width);
                        lienzo.height = Math.floor(vista.height);
                        return pagina.render({ canvasContext: lienzo.getContext('2d'), viewport: vista }).promise.then(function () {
                            var enlace = document.createElement('a');
                            enlace.href = url;
                            enlace.target = '_blank';
                            enlace.rel = 'noopener';
                            enlace.title = 'Ver PDF completo';
                            enlace.appendChild(lienzo);
                            marco.innerHTML = '';
                            marco.appendChild(enlace);
                        });
                    })
                    .catch(function () {}); // Si falla, queda el botón "Abrir PDF"
            }

            // Carga los archivos de una diapositiva solo cuando se va a mostrar
            function cargar(indice) {
                var slide = slides[(indice + slides.length) % slides.length];
                slide.querySelectorAll('[data-src]').forEach(function (el) { el.src = el.dataset.src; el.removeAttribute('data-src'); });
                slide.querySelectorAll('[data-poster]').forEach(function (el) { el.poster = el.dataset.poster; el.removeAttribute('data-poster'); });
                slide.querySelectorAll('[data-fondo]').forEach(function (el) {
                    el.style.backgroundImage = "url('" + el.dataset.fondo + "')";
                    el.removeAttribute('data-fondo');
                });
                slide.querySelectorAll('[data-pdf]').forEach(dibujarPdf);
            }

            // Con el carrusel automático, cada diapositiva (video, imagen o PDF) cambia con el intervalo configurado;
            // en modo manual solo se cambia con las flechas, los puntos o deslizando el dedo
            function programarSiguiente() {
                clearTimeout(temporizador);
                if (!automatico || pausado || slides.length < 2) return;
                temporizador = setTimeout(function () { mostrar(actual + 1); }, intervalo);
            }

            function mostrar(indice) {
                var videoAnterior = slides[actual].querySelector('video');
                if (videoAnterior) videoAnterior.pause();

                actual = (indice + slides.length) % slides.length;
                cargar(actual);
                cargar(actual + 1);
                slides.forEach(function (slide, i) {
                    slide.classList.toggle('is-active', i === actual);
                    slide.setAttribute('aria-hidden', i === actual ? 'false' : 'true');
                });
                puntos.forEach(function (punto, i) { punto.classList.toggle('is-active', i === actual); });
                if (contador) contador.textContent = (actual + 1) + ' / ' + slides.length;

                // El video sigue desde donde quedó, así en cada vuelta se ve una parte nueva
                var video = slides[actual].querySelector('video');
                if (video) {
                    video.preload = 'auto';
                    var reproduccion = video.play();
                    if (reproduccion && reproduccion.catch) reproduccion.catch(function () {});
                }
                programarSiguiente();
            }

            raiz.querySelectorAll('.cl-flecha').forEach(function (boton) {
                boton.addEventListener('click', function () { mostrar(actual + parseInt(boton.dataset.paso, 10)); });
            });
            puntos.forEach(function (punto) {
                punto.addEventListener('click', function () { mostrar(parseInt(punto.dataset.indice, 10)); });
            });

            // Se detiene mientras el mouse está encima, para poder leer con calma
            raiz.addEventListener('pointerenter', function (e) {
                if (e.pointerType === 'mouse') { pausado = true; clearTimeout(temporizador); }
            });
            raiz.addEventListener('pointerleave', function (e) {
                if (e.pointerType === 'mouse') { pausado = false; programarSiguiente(); }
            });

            // En celulares se puede deslizar el dedo para cambiar
            var inicioX = null;
            raiz.addEventListener('touchstart', function (e) { inicioX = e.touches[0].clientX; }, { passive: true });
            raiz.addEventListener('touchend', function (e) {
                if (inicioX === null || slides.length < 2) return;
                var desplazamiento = e.changedTouches[0].clientX - inicioX;
                inicioX = null;
                if (Math.abs(desplazamiento) > 40) mostrar(actual + (desplazamiento < 0 ? 1 : -1));
            });

            cargar(0);
            if (slides.length > 1) cargar(1);
            programarSiguiente();
        })();
    </script>
@endif
