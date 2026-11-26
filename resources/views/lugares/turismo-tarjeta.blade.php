<article class="tarjeta tarjeta-turismo">
    @include('lugares.turismo-fotos', ['soloPortadaTurismo' => true])
    <small>{{ $lugar->actividad?->etiqueta() ?? $lugar->tipo }}</small>
    <div class="titulo-lugar">
        <h2>{{ $lugar->nombre }}</h2>
        @include('lugares.calificacion')
    </div>
    <p>{{ $lugar->direccion }}</p>
    @include('lugares.turismo-indicadores')
    <div class="acciones">
        <a href="{{ route('lugares.show', $lugar) }}" data-detalle-turismo="detalle-turismo-{{ $lugar->id }}">Ver detalle</a>
        @include('lugares.turismo-en-mapa')
    </div>
    @include('lugares.turismo-detalle')
</article>
