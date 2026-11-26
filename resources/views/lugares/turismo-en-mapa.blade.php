@if ($lugar->latitud !== null && $lugar->longitud !== null)
    <a href="{{ route('inicio', $filtros ?? array_filter(['categoria' => 'turismo', 'actividad' => $lugar->actividad?->value])) }}#lugar={{ $lugar->slug }}"
        data-turismo-en-mapa>Ver en el mapa</a>
@else
    <span class="sin-mapa-turismo">Sin coordenadas en el mapa</span>
@endif
