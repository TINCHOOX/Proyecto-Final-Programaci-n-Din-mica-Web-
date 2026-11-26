@php
    $filtrosTurismo = \Illuminate\Support\Arr::only($filtros, ['categoria', 'q']);
    $actividadActual = $filtros['actividad'] ?? null;
@endphp

<nav class="actividades-turismo" aria-label="Actividades de Turismo">
    <a class="boton boton-claro" href="{{ route($rutaActividades, $filtrosTurismo) }}"
        @if ($actividadActual === null) aria-current="page" @endif>Todas</a>
    @foreach (\App\Enums\ActividadTurismo::cases() as $actividad)
        <a class="boton boton-claro" href="{{ route($rutaActividades, $filtrosTurismo + ['actividad' => $actividad->value]) }}"
            @if ($actividadActual === $actividad->value) aria-current="page" @endif>{{ $actividad->etiqueta() }}</a>
    @endforeach
</nav>
