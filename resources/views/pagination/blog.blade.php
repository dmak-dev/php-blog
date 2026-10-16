@if ($paginator->hasPages())
    <nav class="pagination">
        @if ($paginator->onFirstPage())
            <span class="page-link disabled">Назад</span>
        @else
            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">Назад</a>
        @endif

        <span class="page-info">
            Страница {{ $paginator->currentPage() }} из {{ $paginator->lastPage() }}
        </span>

        @if ($paginator->hasMorePages())
            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Вперёд</a>
        @else
            <span class="page-link disabled">Вперёд</span>
        @endif
    </nav>
@endif
