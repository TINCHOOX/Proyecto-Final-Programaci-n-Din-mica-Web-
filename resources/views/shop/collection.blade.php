@extends('layouts.app')

@section('titulo', 'Mi colección · DinoMaps')


@section('contenido')
<main class="contenido-catalogo tienda">
    <nav class="tienda-nav" aria-label="Colección">
        <a class="boton boton-claro" href="{{ route('shop.index') }}">Volver a la tienda</a>
        <a class="boton boton-claro" href="{{ route('inicio') }}">Volver al mapa</a>
    </nav>
    <div class="tienda-titulo">
        <div>
            <h1>Mi colección</h1>
            @if ($dinosaurActivo = $dinosaurs->firstWhere('id', $equippedId))
                <p>En uso <strong>{{ $dinosaurActivo->name }}</strong></p>
            @endif
        </div>
    </div>
    @include('shop.mensajes')
    <div class="tienda-grilla">
        @foreach ($dinosaurs as $dinosaur)
            @include('shop.tarjeta', ['coleccion' => true])
        @endforeach
    </div>
</main>
@endsection
