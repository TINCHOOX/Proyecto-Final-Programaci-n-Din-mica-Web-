@extends('layouts.app')

@section('titulo', 'Cuenta · DinoMaps')

@section('contenido')
<main class="cuenta-pagina">
    <div class="cuenta-cabecera">
        <a class="cuenta-volver" href="{{ route('inicio') }}" aria-label="Volver al mapa">&larr;</a>
        <h1>Cuenta</h1>
    </div>
    @include('shop.mensajes')
    <div class="cuenta-grilla">
        <section>
            <h2>Mi perfil</h2>
            <div class="bloque-cuenta">
                <div class="identidad-cuenta">
                    <img src="{{ asset($equipped?->image_path ?? 'images/dinomaps/dino-3.webp') }}" alt="">
                    <div>
                        <strong>{{ $user->name }}</strong>
                        <p>{{ $equipped?->name ?? 'Dino explorador' }}</p>
                    </div>
                </div>
                <div class="opcion-cuenta opcion-saldo">
                    <div><strong>Puntos disponibles</strong><small>Canjealos por dinosaurios en la tienda.</small></div>
                    <strong class="cuenta-dato">{{ $user->points }}</strong>
                </div>
            </div>
        </section>
        <section>
            <h2>Accesibilidad</h2>
            <div class="bloque-cuenta">
                @include('cuenta.modos')
                <small class="cuenta-nota">Los modos de color se aplican al mapa.</small>
            </div>
        </section>
        <section>
            <h2>Recompensas</h2>
            <div class="bloque-cuenta">
                <a class="opcion-cuenta opcion-destacada" href="{{ route('shop.index') }}"><div><strong>Tienda</strong><small>Canjeá tus puntos por nuevos dinosaurios.</small></div><span>Abrir ›</span></a>
                <a class="opcion-cuenta opcion-destacada" href="{{ route('shop.collection') }}"><div><strong>Mi colección</strong><small>Elegí el dinosaurio para tu perfil.</small></div><span>Abrir ›</span></a>
            </div>
        </section>
        <section>
            <h2>Actividad y sesión</h2>
            <div class="bloque-cuenta">
                <a class="opcion-cuenta" href="{{ route('reports.mine') }}">
                    <div><strong>Reportes</strong><small>Consultá tus avisos de tránsito.</small></div>
                    <strong>{{ $reportCount }} <span aria-hidden="true">›</span></strong>
                </a>
                @can('gestionar-catalogo')
                    <a class="opcion-cuenta" href="{{ route('admin.lugares.index') }}"><strong>Gestionar lugares</strong><span aria-hidden="true">›</span></a>
                @endcan
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button class="opcion-cuenta opcion-sesion" type="submit"><div><strong>Cerrar sesión</strong><small>Volvé a explorar como visitante.</small></div><span>›</span></button>
                </form>
            </div>
        </section>
    </div>
</main>
@endsection
