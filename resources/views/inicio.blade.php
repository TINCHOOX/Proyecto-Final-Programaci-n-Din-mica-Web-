@extends('layouts.app')

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="anonymous">
<link rel="stylesheet" href="{{ asset('css/transito.css') }}">
<link rel="stylesheet" href="https://unpkg.com/maplibre-gl@6.3.0/dist/maplibre-gl.css">
<script type="importmap">
{
    "imports": {
        "leaflet": "https://unpkg.com/leaflet@1.9.4/dist/leaflet-src.esm.js",
        "maplibre-gl": "https://unpkg.com/maplibre-gl@6.3.0/dist/maplibre-gl.mjs",
        "maplibre-gl-leaflet": "https://unpkg.com/@maplibre/maplibre-gl-leaflet@0.1.4/dist/leaflet-maplibre-gl.mjs"
    }
}
</script>
<script type="module" src="{{ asset('js/mapa.js') }}"></script>
@if (($filtros['categoria'] ?? null) === 'turismo' && !isset($destino))
    <script type="module" src="{{ asset('js/turismo.js') }}"></script>
@endif
@endpush

@section('contenido')
<main class="explorar">
    <div class="controles-mapa">
        @include('lugares.categorias', ['rutaCategorias' => 'inicio'])
        <div class="herramientas">
            <a class="boton boton-claro enlace-movil" href="#resultados-mapa">{{ $esTransito ? 'Ir a filtros' : 'Ir a resultados' }}</a>
            @unless ($esTransito)
                <a class="boton boton-claro" href="{{ route('transito') }}">Tránsito</a>
                <a class="boton" href="{{ route('transito', ['publicar' => 1]) }}">Dar aviso</a>
                <button id="usuarios-demo" type="button" aria-pressed="false" data-mostrar="{{ empty($filtros) ? 'true' : 'false' }}" disabled>Usuarios · demo</button>
            @endunless
            <span id="estado-mapa" role="status"></span>
            @auth
                <button id="presencia-cercana" type="button" aria-pressed="false" title="Compartí tu ubicación aproximada para ver otros usuarios cercanos"
                    data-url="{{ route('presence.index') }}" data-token="{{ csrf_token() }}" disabled>Compartir ubicación</button>
                <span id="estado-presencia" role="status"></span>
            @endauth
        </div>
    </div>
    <aside id="resultados-mapa" class="resultados" aria-label="{{ $esTransito ? 'Tránsito' : 'Resultados' }}" tabindex="-1">
        <div class="resumen">
            <a class="enlace-movil regreso" href="#mapa">Volver al mapa</a>
            <h1>Explorá Neuquén <img class="dino-explorar" src="{{ asset('images/dinomaps/dino-8.webp') }}" alt=""></h1>
            <p>Encontrá lo que vuelve única a nuestra ciudad.</p>
            @unless ($esTransito)
                <div class="acciones-busqueda">
                    @include('lugares.buscador', ['rutaCategorias' => 'inicio'])
                    <a class="boton" href="{{ route('lugares.index', $filtros) }}">Ver todo →</a>
                </div>
                @if (($filtros['categoria'] ?? null) === 'turismo' && !isset($destino))
                    @include('lugares.actividades', ['rutaActividades' => 'inicio'])
                @endif
            @endunless
        </div>
        @if (isset($destino))
            <section class="lista panel-transito">
                <a class="regreso" href="{{ route('lugares.show', $destino) }}">← Volver a la ficha</a>
                <h2>Recorrido</h2>
                <strong>{{ $destino->nombre }}</strong>
                <p>{{ $destino->direccion }}</p>
                @if (config('services.geoapify.key'))
                    <p>Para calcular el recorrido, se enviará tu ubicación a Geoapify.</p>
                    <button class="boton" id="trazar-recorrido" type="button" data-url="{{ route('recorrido.calcular', $destino) }}"
                        data-token="{{ csrf_token() }}" disabled>Trazar desde mi ubicación</button>
                    <p id="estado-recorrido" role="status"></p>
                @endif
                <a class="boton boton-claro" href="https://www.google.com/maps/dir/?api=1&amp;destination={{ urlencode($destino->nombre.' '.$destino->direccion.' Neuquén') }}"
                    target="_blank" rel="noopener noreferrer">Cómo llegar en Google Maps</a>
            </section>
        @elseif ($esTransito)
            @include('transito.panel')
        @else
            <div class="lista">
                @forelse ($lugares as $lugar)
                    @include('lugares.tarjeta')
                @empty
                    <p>{{ isset($filtros['actividad']) && ($filtros['q'] ?? '') === '' ? 'Todavía no hay lugares cargados para esta actividad.' : 'No encontramos lugares para esta búsqueda.' }}</p>
                @endforelse
                {{ $lugares->links('lugares.paginas') }}
            </div>
        @endif
    </aside>
    <div class="accesos-mapa" aria-label="Accesos del mapa">
        <button id="mi-ubicacion" type="button" title="Mi ubicación" aria-label="Mi ubicación" disabled>
            <img src="{{ asset('images/dinomaps/centrar.svg') }}" alt="">
        </button>
        <details id="selector-mapa" class="selector-mapa">
            <summary title="Cambiar tipo de mapa" aria-label="Tipo de mapa · Estándar" aria-disabled="true">
                <img src="{{ asset('images/dinomaps/ubicacion.svg') }}" alt="">
            </summary>
            <div class="tipos-mapa" role="group" aria-label="Tipos de mapa">
                @foreach (['estandar' => 'Estándar', 'hibrido' => 'Híbrido', 'satelite' => 'Satélite'] as $valor => $nombre)
                    <button type="button" data-tipo-mapa="{{ $valor }}" aria-pressed="{{ $valor === 'estandar' ? 'true' : 'false' }}" disabled>
                        <span class="muestra-mapa muestra-mapa--{{ $valor }}" aria-hidden="true"></span>
                        <span>{{ $nombre }}</span>
                    </button>
                @endforeach
            </div>
        </details>
        <p id="estado-tipo-mapa" class="estado-tipo-mapa" role="status"></p>
    </div>
    <div id="mapa" class="mapa" aria-label="Mapa de Neuquén" data-iconos="{{ asset('images/dinomaps') }}"
        @unless ($esTransito)
            data-puntos="{{ route('mapa.puntos', $filtros) }}"
            @if (!isset($destino) && array_filter($filtros)) data-ajustar="true" @endif
        @endunless></div>
    <noscript><p class="aviso-mapa">Activá JavaScript para ver el mapa interactivo.</p></noscript>
</main>
@endsection
