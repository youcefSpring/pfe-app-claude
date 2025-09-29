@php
    $paginator = $paginator ?? $data ?? null;

    $lang = app()->getLocale();
    $labels = [
        'en' => ['prev' => 'Previous', 'next' => 'Next'],
        'fr' => ['prev' => 'Précédent', 'next' => 'Suivant'],
        'ar' => ['prev' => 'السابق', 'next' => 'التالي'],
    ];
    $currentLabels = $labels[$lang] ?? $labels['en'];
@endphp

@if ($paginator && $paginator->hasPages())
    <nav>
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">{{ $currentLabels['prev'] }}</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}">{{ $currentLabels['prev'] }}</a>
                </li>
            @endif

            {{-- Pagination Links --}}
            @foreach ($paginator->links()->elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach


            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}">{{ $currentLabels['next'] }}</a>
                </li>
            @else
                <li class="page-item disabled"><span class="page-link">{{ $currentLabels['next'] }}</span></li>
            @endif
        </ul>
    </nav>
@endif
