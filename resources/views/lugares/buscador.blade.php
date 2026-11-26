<form method="get" class="buscador" action="{{ route($rutaCategorias) }}" role="search">
    @isset($filtros['categoria'])
        <input type="hidden" name="categoria" value="{{ $filtros['categoria'] }}">
    @endisset
    @isset($filtros['actividad'])
        <input type="hidden" name="actividad" value="{{ $filtros['actividad'] }}">
    @endisset
    <input aria-label="Buscar un lugar" name="q" type="search" maxlength="100" placeholder="Buscar un lugar" value="{{ $filtros['q'] ?? '' }}">
    <button type="submit" aria-label="Buscar"><img src="{{ asset('images/dinomaps/buscar.svg') }}" alt=""></button>
</form>
