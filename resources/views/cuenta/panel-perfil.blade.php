<details class="cuenta" name="panel-dinomaps">
    <summary><img src="{{ asset('images/dinomaps/cuenta.svg') }}" alt=""> Cuenta</summary>
    <section class="panel-cuenta" aria-label="Cuenta">
        <div class="panel-cuenta-cabecera">
            <button class="cuenta-volver" data-cerrar-panel type="button" aria-label="Cerrar Cuenta">&larr;</button>
            <h2>Cuenta</h2>
        </div>

        @auth
            <div>
                <h3 class="cuenta-apartado">Cuenta</h3>
                <div class="perfil">
                    <div class="perfil-identidad">
                        @php($dinoEquipado = auth()->user()->equippedDinosaur)
                        <img src="{{ asset($dinoEquipado?->image_path ?? 'images/dinomaps/dino-3.webp') }}" alt="">
                        <div>
                            <strong>{{ auth()->user()->name }}</strong>
                            <p>{{ $dinoEquipado?->name ?? 'Dino explorador' }}</p>
                        </div>
                    </div>
                    <p class="opcion-cuenta opcion-puntos"><strong>Puntos</strong><span>{{ auth()->user()->points }}</span></p>
                    <a class="opcion-cuenta opcion-destacada" href="{{ route('shop.index') }}">
                        <strong>Tienda</strong><span>Abrir <span aria-hidden="true">›</span></span>
                    </a>
                    <a class="opcion-cuenta opcion-destacada" href="{{ route('shop.collection') }}">
                        <strong>Mi colección</strong><span>Abrir <span aria-hidden="true">›</span></span>
                    </a>
                    <a class="opcion-cuenta" href="{{ route('reports.mine') }}">
                        <strong>Reportes</strong><span aria-hidden="true">›</span>
                    </a>
                    @can('gestionar-catalogo')
                        <a class="opcion-cuenta" href="{{ route('admin.lugares.index') }}"><strong>Gestionar lugares</strong><span aria-hidden="true">›</span></a>
                    @endcan
                    <form method="post" action="{{ route('logout') }}">
                        @csrf
                        <button class="opcion-sesion" type="submit">Cerrar sesión</button>
                    </form>
                </div>
            </div>

            <div>
                <h3 class="cuenta-apartado">Accesibilidad</h3>
                <div class="bloque-cuenta">
                    @include('cuenta.modos')
                </div>
            </div>

            <a class="opcion-cuenta opcion-mas" href="{{ route('cuenta.index') }}">
                <strong>Ver más</strong><span aria-hidden="true">›</span>
            </a>
        @else
            <p>Iniciá sesión para publicar reseñas, dar avisos de tránsito y canjear puntos.</p>
            <div class="presentacion-prelogin">
                <img class="dino-prelogin" src="{{ asset('images/dinomaps/dino-guia-urbano.webp') }}" alt="">
                <p><q>El mundo es demasiado bonito como para viajar solo por internet</q></p>
            </div>
            <a class="boton boton-claro" href="{{ route('register') }}">Crear cuenta</a>
            <a class="boton boton-claro" href="{{ route('login') }}">Iniciar sesión</a>
        @endauth
    </section>
</details>
