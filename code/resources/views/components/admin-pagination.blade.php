@props(['paginator', 'showInfo' => true, 'size' => 'default'])

@php
$sizeClass = match($size) {
    'sm' => 'pagination-sm',
    'lg' => 'pagination-lg',
    default => ''
};
@endphp

@if($paginator->hasPages())
    <div class="pagination-wrapper">
        @if($showInfo)
            <div class="pagination-info">
                <span class="text-muted">
                    {{ __('app.showing') }} {{ $paginator->firstItem() }} {{ __('app.to') }} {{ $paginator->lastItem() }}
                    {{ __('app.of') }} {{ $paginator->total() }} {{ __('app.results') }}
                </span>
            </div>
        @endif

        <nav aria-label="{{ __('app.pagination_navigation') }}" class="pagination-nav">
            <ul class="pagination justify-content-center mb-0 {{ $sizeClass }}">
                {{-- Previous Page Link --}}
                @if($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">
                            <i class="bi bi-chevron-left"></i>
                            <span class="d-none d-md-inline ms-1">{{ __('app.previous') }}</span>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                            <i class="bi bi-chevron-left"></i>
                            <span class="d-none d-md-inline ms-1">{{ __('app.previous') }}</span>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @php
                    $start = max($paginator->currentPage() - 2, 1);
                    $end = min($start + 4, $paginator->lastPage());
                    $start = max($end - 4, 1);
                @endphp

                {{-- First Page --}}
                @if($start > 1)
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
                    </li>
                    @if($start > 2)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                @endif

                {{-- Current Page Range --}}
                @for($page = $start; $page <= $end; $page++)
                    @if($page == $paginator->currentPage())
                        <li class="page-item active" aria-current="page">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                        </li>
                    @endif
                @endfor

                {{-- Last Page --}}
                @if($end < $paginator->lastPage())
                    @if($end < $paginator->lastPage() - 1)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                            <span class="d-none d-md-inline me-1">{{ __('app.next') }}</span>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">
                            <span class="d-none d-md-inline me-1">{{ __('app.next') }}</span>
                            <i class="bi bi-chevron-right"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>

    <style>
        .pagination-wrapper {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid var(--bs-border-color);
        }

        .pagination-info {
            font-size: 0.875rem;
        }

        .pagination-nav .pagination {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .pagination-nav .page-link {
            border: none;
            padding: 0.5rem 0.75rem;
            color: #6c757d;
            font-weight: 500;
            transition: all 0.2s ease;
            background: white;
        }

        .pagination-nav .page-link:hover {
            background-color: #f8f9fa;
            color: #0d6efd;
            transform: translateY(-1px);
        }

        .pagination-nav .page-item.active .page-link {
            background-color: #0d6efd;
            color: white;
            box-shadow: 0 2px 4px rgba(13, 110, 253, 0.3);
        }

        .pagination-nav .page-item.disabled .page-link {
            color: #adb5bd;
            background-color: #f8f9fa;
        }

        .pagination-nav .page-item:first-child .page-link {
            border-top-left-radius: 0.5rem;
            border-bottom-left-radius: 0.5rem;
        }

        .pagination-nav .page-item:last-child .page-link {
            border-top-right-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
        }

        @media (max-width: 576px) {
            .pagination-wrapper {
                gap: 0.5rem;
            }

            .pagination-nav .page-link {
                padding: 0.375rem 0.5rem;
                font-size: 0.875rem;
            }
        }
    </style>
@endif