<section id="panel-transito" class="lista panel-transito" data-avisos="{{ route('reports.index') }}"
    data-abrir="{{ request()->boolean('publicar') ? '1' : '0' }}">
    <h2>Tránsito</h2>
    <p>Consultá qué pasa en las calles</p>
    <small>Los avisos comunitarios vencen a las 36 horas.</small>
    <fieldset>
        <legend>Filtros combinables</legend>
        @foreach ($tiposAviso as $valor => $nombre)
            <label class="filtro-transito">
                <strong class="filtro-nombre">
                    @if ($valor === 'concurrencia')
                        <span class="muestra-concurrencia" aria-hidden="true"></span>
                    @else
                    <span class="aviso-marcador aviso-marcador--{{ $valor }}" aria-hidden="true">
                        @if (in_array($valor, ['radares', 'accidentes'], true))
                            <img src="{{ asset('images/dinomaps/'.($valor === 'radares' ? 'radar.svg' : 'accidente.svg')) }}" alt="">
                        @else
                            {{ $valor === 'obras' ? 'O' : 'A' }}
                        @endif
                    </span>
                    @endif
                    <span>{{ $nombre }}<small>{{ ['concurrencia' => 'Trazo discontinuo', 'obras' => 'Obras y cortes', 'radares' => 'Ícono de cámara', 'accidentes' => 'Ícono de alerta'][$valor] ?? '' }}</small></span>
                </strong>
                <input type="checkbox" value="{{ $valor }}" data-tipo="{{ $nombre }}">
                <span aria-hidden="true">No</span>
            </label>
        @endforeach
    </fieldset>
    @auth
        <button id="abrir-aviso" class="boton" type="button" disabled>Publicar un aviso</button>
        <div id="selector-aviso" class="selector-aviso" hidden>
            <p>Seleccioná el lugar en el mapa.</p>
            <button id="usar-centro-aviso" type="button">Usar el centro del mapa</button>
            <button id="cancelar-ubicacion-aviso" type="button">Cancelar</button>
        </div>
    @else
        <a class="boton" href="{{ route('login') }}">Iniciar sesión para publicar</a>
    @endauth
    <button class="boton boton-claro" id="recorrido-demo-transito" type="button" aria-pressed="false" disabled>Ver recorrido de ejemplo</button>
    @auth
        <a class="boton boton-claro" href="{{ route('reports.mine') }}">Mis reportes</a>
    @endauth
    <p id="estado-transito" role="status"></p>
    <a class="boton boton-claro" href="{{ route('inicio', ['categoria' => 'transporte']) }}">Volver a Transporte</a>
</section>
@auth
    <dialog id="dialogo-aviso" class="dialogo-transito" aria-labelledby="titulo-aviso">
        <header>
            <h2 id="titulo-aviso">Publicar un aviso</h2>
            <button class="boton boton-claro cerrar-dialogo" type="button">Cerrar</button>
        </header>
        <form id="form-aviso" method="post" action="{{ route('reports.store') }}">
            @csrf
            <label for="tipo-aviso">Tipo de aviso</label>
            <select id="tipo-aviso" name="type" required>
                <option value="corte">Corte de calle</option>
                @foreach ($tiposAviso as $valor => $nombre)
                    <option value="{{ $valor }}">{{ $nombre }}</option>
                @endforeach
            </select>
            <span id="etiqueta-ubicacion-aviso">Ubicación</span>
            <button id="seleccionar-ubicacion-aviso" class="campo-ubicacion" type="button"
                aria-describedby="etiqueta-ubicacion-aviso">Elegir en el mapa</button>
            <input type="hidden" name="latitude">
            <input type="hidden" name="longitude">
            <div class="fin-tramo" id="fin-tramo" hidden>
                <span id="etiqueta-fin-aviso">Fin del tramo</span>
                <button id="seleccionar-fin-aviso" class="campo-ubicacion" type="button"
                    aria-describedby="etiqueta-fin-aviso">Elegir fin en el mapa</button>
                <input type="hidden" name="end_latitude" disabled>
                <input type="hidden" name="end_longitude" disabled>
            </div>
            <label for="descripcion-aviso">Descripción</label>
            <textarea id="descripcion-aviso" name="description" rows="2" maxlength="500" required></textarea>
            <p id="error-aviso" class="error" role="alert" hidden></p>
            <button class="boton" type="submit">Publicar aviso</button>
        </form>
    </dialog>
@endauth
<dialog id="detalle-aviso" class="dialogo-transito" aria-labelledby="titulo-detalle-aviso">
    <header>
        <h2 id="titulo-detalle-aviso">Aviso de tránsito</h2>
        <button class="boton boton-claro cerrar-dialogo" type="button">Cerrar</button>
    </header>
    <p id="descripcion-detalle-aviso"></p>
    <p id="vencimiento-detalle-aviso"></p>
    <p id="votos-detalle-aviso"></p>
    @auth
        <button id="confirmar-aviso" class="boton" type="button" data-url="{{ route('reports.votes.store', ['report' => '__REPORT__']) }}"
            data-token="{{ csrf_token() }}">Confirmar que sigue vigente</button>
        <details id="opciones-eliminar-aviso" hidden>
            <summary>Eliminar mi aviso</summary>
            <button id="eliminar-aviso" class="boton" type="button" data-url="{{ route('reports.destroy', ['report' => '__REPORT__']) }}"
                data-token="{{ csrf_token() }}">Confirmar eliminación</button>
        </details>
    @else
        <a class="boton" href="{{ route('login') }}">Iniciar sesión para confirmar</a>
    @endauth
    <p id="estado-detalle-aviso" role="status"></p>
</dialog>
