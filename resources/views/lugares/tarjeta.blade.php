@php($fotos = config('dinomaps.lugares.'.$lugar->slug, []))
@php($enTurismo = ($filtros['categoria'] ?? null) === 'turismo')
<article class="tarjeta">
    <small>{{ $enTurismo ? ($lugar->actividad?->etiqueta() ?? $lugar->tipo) : $lugar->tipo }}</small>
    <div class="titulo-lugar">
        <h2>{{ $lugar->nombre }}</h2>
        @include('lugares.calificacion')
    </div>
    <div class="tarjeta-cuerpo @if (count($fotos) > 1) tarjeta-con-galeria @endif">
        @if ($fotos)
            <div class="tarjeta-fotos">
                @foreach ($fotos as $foto)
                    <img src="{{ asset('images/dinomaps/lugares/'.$foto.'.webp') }}" alt="" loading="lazy">
                @endforeach
                @if (in_array($lugar->slug, config('dinomaps.fotos_de_referencia'), true))
                    <span class="foto-referencia">Foto de referencia</span>
                @endif
            </div>
        @endif
        <div class="tarjeta-direccion">
            <strong>{{ $lugar->direccion }}</strong>
            @if (count($fotos) < 2) <p>{{ $lugar->tipo }}</p> @endif
        </div>
        <div class="tarjeta-resenas">
            @if ($lugar->reviews_count)
                <strong>{{ $lugar->reviews_count }}</strong>
                <span>{{ $lugar->reviews_count === 1 ? 'reseña' : 'reseñas' }}</span>
            @else
                <span>Sin reseñas</span>
            @endif
        </div>
    </div>
    <div class="acciones">
        <a href="https://www.google.com/maps/dir/?api=1&amp;destination={{ urlencode($lugar->nombre.' '.$lugar->direccion.' Neuquén') }}" target="_blank" rel="noopener noreferrer">Cómo llegar</a>
        <a href="{{ route('lugares.show', $lugar) }}"
            @if ($enTurismo) data-detalle-turismo="detalle-turismo-{{ $lugar->id }}" @endif>Más información</a>
        @if ($enTurismo)
            @include('lugares.turismo-en-mapa')
        @endif
    </div>
    @if ($enTurismo)
        @include('lugares.turismo-detalle')
    @endif
</article>
