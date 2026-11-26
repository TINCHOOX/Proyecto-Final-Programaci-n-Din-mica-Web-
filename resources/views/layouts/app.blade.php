<!doctype html>
<html lang="es-AR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'DinoMaps')</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Istok+Web:wght@400;700&family=Jaro&family=Karantina&display=swap">
    <link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cuenta.css') }}">
    <script type="module" src="{{ asset('js/configuracion.js') }}"></script>
    <script type="module" src="{{ asset('js/color-mapa.js') }}"></script>
    @stack('head')
    <link rel="stylesheet" href="{{ asset('css/accesibilidad.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tema.css') }}">
</head>
<body data-color-mode="{{ auth()->user()?->color_mode ?? 'estandar' }}" @auth data-color-cuenta @endauth>
    @unless (request()->routeIs('cuenta.index', 'cuenta.configuracion'))
        <header class="barra">
            <a class="marca" href="{{ route('inicio') }}" aria-label="DinoMaps">
                <img class="marca-montana" src="{{ asset('images/dinomaps/montana.svg') }}" alt="">
                <img class="marca-texto" src="{{ asset('images/dinomaps/marca-texto.svg') }}" alt="">
            </a>
            @if (request()->routeIs('shop.index', 'shop.collection'))
                <a class="header-etiqueta" href="{{ route('cuenta.index') }}">Cuenta / {{ request()->routeIs('shop.index') ? 'Recompensas' : 'Colección' }}</a>
            @else
                <div class="header-controles">
                    @include('cuenta.panel-configuracion')
                    @include('cuenta.panel-perfil')
                </div>
            @endif
        </header>
    @endunless
    @if ($errors->has('categoria') || $errors->has('q') || $errors->has('actividad'))
        <p class="error error-global" role="alert">{{ $errors->first('categoria') ?: $errors->first('q') ?: $errors->first('actividad') }} <a href="{{ route('inicio') }}">Volver al mapa</a></p>
    @endif
    @yield('contenido')
</body>
</html>
