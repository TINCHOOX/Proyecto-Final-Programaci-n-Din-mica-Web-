<nav class="categorias" aria-label="Categorías de lugares">
    <a href="{{ route($rutaCategorias, array_filter(['q' => $filtros['q'] ?? null], fn ($valor) => $valor !== null)) }}"
        @if (empty($filtros['categoria'])) aria-current="page" @endif>Todos</a>
    @foreach ($categorias as $item)
        <a href="{{ route($rutaCategorias, array_filter(['categoria' => $item->slug, 'q' => $filtros['q'] ?? null], fn ($valor) => $valor !== null)) }}"
            @if (($filtros['categoria'] ?? null) === $item->slug) aria-current="page" @endif>{{ $item->nombre }}</a>
    @endforeach
</nav>
