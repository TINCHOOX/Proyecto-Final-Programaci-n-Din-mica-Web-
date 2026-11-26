<details class="cuenta" name="panel-dinomaps">
    <summary><img src="{{ asset('images/dinomaps/configuracion.svg') }}" alt=""> Configuración</summary>
    <section class="panel-cuenta panel-preferencias" aria-label="Configuración">
        <div class="panel-cuenta-cabecera">
            <button class="cuenta-volver" data-cerrar-panel type="button" aria-label="Cerrar Configuración">&larr;</button>
            <h2>Configuración</h2>
        </div>

        <div>
            <h3 class="cuenta-apartado">Mapa</h3>
            <div class="bloque-cuenta">
                @include('cuenta.tema')
                @include('cuenta.preferencias')
                <small class="estado-configuracion" id="estado-configuracion" role="status"></small>
            </div>
        </div>

        <a class="opcion-cuenta opcion-mas" href="{{ route('cuenta.configuracion') }}">
            <strong>Ver más</strong><span aria-hidden="true">›</span>
        </a>
    </section>
</details>
