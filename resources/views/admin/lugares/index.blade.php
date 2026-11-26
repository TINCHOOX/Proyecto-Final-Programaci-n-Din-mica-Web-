@extends('layouts.app')

@section('titulo', 'Gestionar lugares · DinoMaps')

@section('contenido')
<main class="contenido-catalogo admin-catalogo">
    <a class="regreso" href="{{ route('cuenta.index') }}">← Volver a Cuenta</a>
    <div class="cabecera-catalogo">
        <h1>Gestionar lugares</h1>
        <a class="boton" href="{{ route('admin.lugares.create') }}">Agregar lugar</a>
    </div>
    @if (session('status'))
        <p class="mensaje-cuenta" role="status">{{ session('status') }}</p>
    @endif
    <ul class="admin-lugares">
        @forelse ($lugares as $lugar)
            <li class="tarjeta">
                <div>
                    <small>{{ $lugar->categoria->nombre }}</small>
                    <h2>{{ $lugar->nombre }}</h2>
                    <p>{{ $lugar->direccion }}</p>
                </div>
                <div class="acciones">
                    <a href="{{ route('lugares.show', $lugar) }}">Ver ficha</a>
                    <a href="{{ route('admin.lugares.edit', $lugar) }}">Editar</a>
                </div>
            </li>
        @empty
            <li>Todavía no hay lugares en el catálogo.</li>
        @endforelse
    </ul>
    {{ $lugares->links('lugares.paginas') }}
</main>
@endsection
