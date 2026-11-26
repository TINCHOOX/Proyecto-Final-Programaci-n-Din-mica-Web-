@extends('layouts.app')

@section('titulo', 'Mis reportes · DinoMaps')

@section('contenido')
<main class="cuenta-pagina">
    <a class="regreso" href="{{ route('cuenta.index') }}">← Volver a Cuenta</a>
    <h1>Mis reportes</h1>
    @if (session('status')) <p class="mensaje-cuenta" role="status">{{ session('status') }}</p> @endif
    <div class="bloque-cuenta">
        @forelse ($reports as $report)
            <article class="opcion-cuenta">
                <div>
                    <strong>{{ $labels[$report->type] ?? $report->type }}</strong>
                    <p>{{ $report->description }}</p>
                    <small>{{ $report->votes_count }} confirmaciones · vence {{ $report->expires_at->setTimezone('America/Argentina/Buenos_Aires')->format('d/m H:i') }}</small>
                </div>
                <details>
                    <summary>Eliminar</summary>
                    <form method="post" action="{{ route('reports.destroy', $report) }}">
                        @csrf
                        @method('DELETE')
                        <button class="boton" type="submit">Confirmar eliminación</button>
                    </form>
                </details>
            </article>
        @empty
            <p>No tenés avisos activos.</p>
        @endforelse
        {{ $reports->links('lugares.paginas') }}
    </div>
</main>
@endsection
