@if ($paginator->hasPages())
    <nav class="pager" role="navigation" aria-label="Paginacion">
        <div class="pager-summary">
            Mostrando
            <strong>{{ $paginator->firstItem() }}</strong>
            a
            <strong>{{ $paginator->lastItem() }}</strong>
            de
            <strong>{{ $paginator->total() }}</strong>
            registros
        </div>

        <div class="pager-links">
            @if ($paginator->onFirstPage())
                <span class="pager-link disabled">Anterior</span>
            @else
                <a class="pager-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">Anterior</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pager-link disabled">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="pager-link active">{{ $page }}</span>
                        @else
                            <a class="pager-link" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a class="pager-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Siguiente</a>
            @else
                <span class="pager-link disabled">Siguiente</span>
            @endif
        </div>
    </nav>
@endif
