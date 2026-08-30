@if ($paginator->hasPages())
    <nav class="pagination no-print" role="navigation" aria-label="{{ __('pagination.navigation') }}">
        @if ($paginator->onFirstPage())
            <span class="disabled" aria-disabled="true">{!! __('pagination.previous') !!}</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev">{!! __('pagination.previous') !!}</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="disabled">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="current" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next">{!! __('pagination.next') !!}</a>
        @else
            <span class="disabled" aria-disabled="true">{!! __('pagination.next') !!}</span>
        @endif
    </nav>
@endif
