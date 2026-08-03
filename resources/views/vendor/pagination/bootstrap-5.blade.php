@if ($paginator->hasPages())
    <nav class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-3 border-top mt-2" role="navigation">
        <div class="d-flex align-items-center">
            <p class="small text-muted mb-0">
                Menampilkan <span class="fw-bold text-dark">{{ $paginator->firstItem() }}</span> sampai <span class="fw-bold text-dark">{{ $paginator->lastItem() }}</span> dari <span class="fw-bold text-dark">{{ $paginator->total() }}</span> data
            </p>
        </div>

        <div class="d-flex align-items-center">
            <ul class="pagination pagination-sm mb-0 align-items-center gap-1">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                        <span class="page-link" aria-hidden="true"><i class="bx bx-chevron-left"></i></span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')"><i class="bx bx-chevron-left"></i></a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')"><i class="bx bx-chevron-right"></i></a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                        <span class="page-link" aria-hidden="true"><i class="bx bx-chevron-right"></i></span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@elseif($paginator->total() > 0)
    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-2">
        <p class="small text-muted mb-0">
            Menampilkan total <span class="fw-bold text-dark">{{ $paginator->total() }}</span> data
        </p>
    </div>
@endif
