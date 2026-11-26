@extends('layouts.app')

@section('titulo', ($categoria?->nombre ?? 'Todos los lugares').' · DinoMaps')

@if ($categoria?->slug === 'turismo')
    @push('head')
    <script type="module" src="{{ asset('js/turismo.js') }}"></script>
    @endpush
@endif

@section('contenido')
<main class="catalogo">
    @include('lugares.categorias', ['rutaCategorias' => 'lugares.index'])
    <div class="contenido-catalogo">
        <a class="regreso" href="{{ route('inicio', $filtros) }}">← Volver al mapa</a>
        <div class="cabecera-catalogo">
            <h1>{{ $categoria?->nombre ?? 'Todos los lugares' }}</h1>
            @include('lugares.buscador', ['rutaCategorias' => 'lugares.index'])
        </div>
        @if ($categoria?->slug === 'turismo')
            @include('lugares.actividades', ['rutaActividades' => 'lugares.index'])
        @endif
        <div class="grilla @if ($categoria?->slug === 'turismo') grilla-turismo @endif">
            @forelse ($lugares as $lugar)
                @include($categoria?->slug === 'turismo' ? 'lugares.turismo-tarjeta' : 'lugares.tarjeta')
            @empty
                <p class="catalogo-vacio">{{ isset($filtros['actividad']) && ($filtros['q'] ?? '') === '' ? 'Todavía no hay lugares cargados para esta actividad.' : 'No encontramos lugares para esta búsqueda.' }}</p>
            @endforelse
        </div>
        {{ $lugares->links('lugares.paginas') }}
    </div>
</main>
@endsection
