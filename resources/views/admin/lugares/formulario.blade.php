@extends('layouts.app')

@section('titulo', ($lugar->exists ? 'Editar lugar' : 'Agregar lugar').' · DinoMaps')

@section('contenido')
<main class="contenido-catalogo admin-catalogo">
    <a class="regreso" href="{{ route('admin.lugares.index') }}">← Gestionar lugares</a>
    <div class="cabecera-catalogo">
        <h1>{{ $lugar->exists ? 'Editar lugar' : 'Agregar lugar' }}</h1>
    </div>
    @if ($errors->any())
        <ul class="error" role="alert">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
    <form class="ficha admin-formulario" method="post"
        action="{{ $lugar->exists ? route('admin.lugares.update', $lugar) : route('admin.lugares.store') }}">
        @csrf
        @if ($lugar->exists)
            @method('PATCH')
        @endif
        <label>Nombre
            <input name="nombre" value="{{ old('nombre', $lugar->nombre) }}" maxlength="255" required>
        </label>
        @unless ($lugar->exists)
            <label>Identificador para la URL
                <input name="slug" value="{{ old('slug') }}" maxlength="255" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" aria-describedby="ayuda-slug">
                <small id="ayuda-slug">Si lo dejás vacío, se genera a partir del nombre.</small>
            </label>
        @endunless
        <label>Categoría
            <select name="categoria_id" required>
                <option value="">Elegí una categoría</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}" @selected((string) old('categoria_id', $lugar->categoria_id) === (string) $categoria->id)>{{ $categoria->nombre }}</option>
                @endforeach
            </select>
        </label>
        <label>Tipo de lugar
            <input name="tipo" value="{{ old('tipo', $lugar->tipo) }}" maxlength="255" required>
        </label>
        <label>Dirección
            <input name="direccion" value="{{ old('direccion', $lugar->direccion) }}" maxlength="255" required>
        </label>
        <label>Actividad (solo Turismo)
            <select name="actividad">
                <option value="">Sin actividad</option>
                @foreach (\App\Enums\ActividadTurismo::cases() as $actividad)
                    <option value="{{ $actividad->value }}" @selected(old('actividad', $lugar->actividad?->value) === $actividad->value)>{{ $actividad->etiqueta() }}</option>
                @endforeach
            </select>
        </label>
        <label>Fuente del lugar
            <input type="url" name="fuente" value="{{ old('fuente', $lugar->fuente) }}" maxlength="2048" required>
        </label>
        <label>Descripción
            <textarea name="descripcion" rows="3" maxlength="2000">{{ old('descripcion', $lugar->descripcion) }}</textarea>
        </label>
        <fieldset>
            <legend>Ubicación en el mapa</legend>
            <p>Para mostrar un marcador, completá ambas coordenadas y su fuente.</p>
            <div class="admin-coordenadas">
                <label>Latitud
                    <input type="number" name="latitud" value="{{ old('latitud', $lugar->latitud) }}" min="-90" max="90" step="0.000001">
                </label>
                <label>Longitud
                    <input type="number" name="longitud" value="{{ old('longitud', $lugar->longitud) }}" min="-180" max="180" step="0.000001">
                </label>
            </div>
            <label>Fuente de coordenadas
                <input type="url" name="fuente_coordenadas" value="{{ old('fuente_coordenadas', $lugar->fuente_coordenadas) }}" maxlength="2048">
            </label>
        </fieldset>
        <div class="acciones">
            <button class="boton" type="submit">Guardar lugar</button>
            <a class="boton boton-claro" href="{{ route('admin.lugares.index') }}">Cancelar</a>
        </div>
    </form>
    @if ($lugar->exists)
        <details class="ficha admin-archivo">
            <summary>Archivar lugar</summary>
            <p>Se retira del mapa y del catálogo. Sus reseñas quedan guardadas.</p>
            <form method="post" action="{{ route('admin.lugares.destroy', $lugar) }}">
                @csrf
                @method('DELETE')
                <button class="boton" type="submit">Confirmar archivo</button>
            </form>
        </details>
    @endif
</main>
@endsection
