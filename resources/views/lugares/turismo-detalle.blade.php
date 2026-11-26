<dialog class="ficha dialogo-turismo" id="detalle-turismo-{{ $lugar->id }}"
    aria-labelledby="titulo-turismo-{{ $lugar->id }}">
    <div class="dialogo-turismo-cabecera">
        <div>
            <p>{{ $lugar->actividad?->etiqueta() ?? $lugar->tipo }}</p>
            <h2 id="titulo-turismo-{{ $lugar->id }}">{{ $lugar->nombre }}</h2>
        </div>
        <form method="dialog">
            <button class="boton boton-claro" autofocus>Cerrar</button>
        </form>
    </div>
    @include('lugares.turismo-fotos')
    <p>{{ $lugar->direccion }}</p>
    @if ($lugar->descripcion)
        <p>{{ $lugar->descripcion }}</p>
    @endif
    @include('lugares.turismo-indicadores')
    <div class="acciones">
        <a class="boton" href="{{ route('lugares.show', $lugar) }}">Ficha y reseñas</a>
        @include('lugares.turismo-en-mapa')
    </div>
</dialog>
