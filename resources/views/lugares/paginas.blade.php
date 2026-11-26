@if ($paginator->hasPages())
    <nav class="paginas" aria-label="Páginas de resultados">
        @if ($paginator->previousPageUrl())
            <a href="{{ $paginator->previousPageUrl() }}">Anterior</a>
        @endif
        <span>Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}</span>
        @if ($paginator->nextPageUrl())
            <a href="{{ $paginator->nextPageUrl() }}">Siguiente</a>
        @endif
    </nav>
@endif
