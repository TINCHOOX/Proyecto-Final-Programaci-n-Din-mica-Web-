@extends('layouts.app')

@section('titulo', 'Configuración · DinoMaps')

@section('contenido')
<main class="cuenta-pagina configuracion">
    <div class="cuenta-cabecera">
        <a class="cuenta-volver" href="{{ route('inicio') }}" aria-label="Volver al mapa">&larr;</a>
        <h1>Configuración</h1>
    </div>
    <small class="estado-configuracion" id="estado-configuracion" role="status"></small>
    <div class="cuenta-grilla">
        <section>
            <h2>Mapa</h2>
            <div class="bloque-cuenta">
                @include('cuenta.preferencias')
                <label class="opcion-cuenta" for="preferencia-avisos"><strong>Avisos al abrir Tránsito</strong>
                    <select class="valor-cuenta" id="preferencia-avisos"><option value="manual">Elegir filtros</option><option value="todos">Mostrar todos</option></select>
                </label>
                <label class="opcion-cuenta" for="preferencia-lugares"><strong>Marcadores de lugares</strong>
                    <select class="valor-cuenta" id="preferencia-lugares"><option value="mostrar">Mostrar</option><option value="ocultar">Ocultar</option></select>
                </label>
                <a class="opcion-cuenta" href="{{ route('inicio') }}"><strong>Centrar mapa en Neuquén</strong><span>›</span></a>
            </div>
        </section>
        <section>
            <h2>Navegación</h2>
            <div class="bloque-cuenta">
                <label class="opcion-cuenta" for="preferencia-viaje"><strong>Modo de viaje</strong>
                    <select class="valor-cuenta" id="preferencia-viaje">
                        <option value="drive">Auto</option><option value="walk">A pie</option><option value="bicycle">Bicicleta</option>
                    </select>
                </label>
                <label class="opcion-cuenta" for="preferencia-unidades"><strong>Unidades de distancia</strong>
                    <select class="valor-cuenta" id="preferencia-unidades"><option value="km">km</option><option value="mi">mi</option></select>
                </label>
            </div>
        </section>
        <section>
            <h2>Accesibilidad</h2>
            <div class="bloque-cuenta">
                @include('cuenta.tema')
                <label class="opcion-cuenta" for="preferencia-texto"><strong>Tamaño de texto</strong>
                    <select class="valor-cuenta" id="preferencia-texto"><option value="normal">Normal</option><option value="grande">Grande</option></select>
                </label>
                @include('cuenta.modos')
            </div>
        </section>
        <section>
            <h2>Privacidad y sesión</h2>
            <div class="bloque-cuenta">
                <p class="opcion-cuenta">Tu ubicación aproximada se comparte al elegir Compartir ubicación en el mapa. Podés dejar de compartirla desde el mismo botón. El permiso del dispositivo se administra en el navegador.</p>
                @auth
                    <a class="opcion-cuenta" href="{{ route('cuenta.index') }}"><strong>Mi cuenta</strong><span>›</span></a>
                    <form method="post" action="{{ route('logout') }}">
                        @csrf
                        <button class="opcion-cuenta opcion-sesion" type="submit"><strong>Cerrar esta sesión</strong><span>›</span></button>
                    </form>
                @else
                    <a class="opcion-cuenta" href="{{ route('login') }}"><strong>Iniciar sesión</strong><span>›</span></a>
                @endauth
            </div>
        </section>
    </div>
</main>
@endsection
