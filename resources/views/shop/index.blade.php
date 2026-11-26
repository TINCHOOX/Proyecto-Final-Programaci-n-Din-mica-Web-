@extends('layouts.app')

@section('titulo', 'Mini tienda · DinoMaps')

@push('head')
    <link rel="stylesheet" href="{{ asset('css/tienda.css') }}">
    <script type="module" src="{{ asset('js/tienda.js') }}"></script>
@endpush

@section('contenido')
<main class="contenido-catalogo tienda">
    <nav class="tienda-nav" aria-label="Recompensas">
        <a class="boton boton-claro" href="{{ route('inicio') }}">Volver al mapa</a>
        <a class="boton" href="{{ route('shop.collection') }}">Mi colección</a>
    </nav>
    <div class="tienda-titulo">
        <div><h1>Mini tienda</h1><p>Personalizá tu perfil con puntos de la comunidad.</p></div>
        <p class="saldo-tienda">Saldo disponible <strong>{{ $points }}</strong> puntos</p>
    </div>
    @include('shop.mensajes')
    @php
        $canjeSolicitado = request()->query('canjear');
        $dinosaurParaConfirmar = is_string($canjeSolicitado)
            ? $dinosaurs->firstWhere('slug', $canjeSolicitado)
            : null;
    @endphp
    @if ($dinosaurParaConfirmar && !in_array($dinosaurParaConfirmar->id, $ownedIds, true) && $points >= $dinosaurParaConfirmar->price)
        <section id="confirmar-canje" class="tienda-canje-alternativo" aria-labelledby="titulo-canje-alternativo" tabindex="-1">
            @include('shop.confirmar', ['producto' => $dinosaurParaConfirmar, 'esDialogo' => false, 'tituloCanje' => 'titulo-canje-alternativo'])
        </section>
    @endif
    <div class="tienda-grilla">
        @foreach ($dinosaurs as $dinosaur)
            @include('shop.tarjeta', ['coleccion' => false])
        @endforeach
    </div>
    <dialog class="tienda-canje-dialogo" id="dialogo-canje" aria-labelledby="titulo-canje-dialogo">
        @include('shop.confirmar', ['producto' => null, 'esDialogo' => true, 'tituloCanje' => 'titulo-canje-dialogo'])
    </dialog>
</main>
@endsection
