<!-- Search Bar Component -->
<div class="search-bar">
    <form method="GET" action="{{ $action ?? request()->url() }}" class="d-flex">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search text-muted"></i>
            </span>
            <input type="text"
                   class="form-control border-start-0 ps-0"
                   name="search"
                   placeholder="{{ $placeholder ?? 'Search...' }}"
                   value="{{ request('search') }}"
                   aria-label="Search">

            @if(isset($filters) && count($filters) > 0)
                @foreach($filters as $filter)
                    <input type="hidden" name="{{ $filter['name'] }}" value="{{ request($filter['name']) }}">
                @endforeach
            @endif

            <button class="btn btn-primary" type="submit">
                <i class="bi bi-search me-1"></i>Search
            </button>

            @if(request()->hasAny(['search', 'filter', 'sort']))
                <a href="{{ $clearUrl ?? request()->url() }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg me-1"></i>Clear
                </a>
            @endif
        </div>
    </form>

    @if(request('search'))
        <div class="mt-2">
            <small class="text-muted">
                @if(isset($resultsCount))
                    {{ $resultsCount }} result{{ $resultsCount !== 1 ? 's' : '' }} found for
                @endif
                "<strong>{{ request('search') }}</strong>"
            </small>
        </div>
    @endif
</div>