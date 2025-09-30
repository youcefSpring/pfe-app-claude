<!-- Filter Sidebar Component -->
<div class="filter-sidebar">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
                <i class="bi bi-funnel me-2"></i>Filters
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ $action ?? request()->url() }}" id="filterForm">
                <!-- Preserve search query -->
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                @isset($filters)
                    @foreach($filters as $filter)
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ $filter['label'] }}</label>

                            @if($filter['type'] === 'select')
                                <select name="{{ $filter['name'] }}" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">All {{ $filter['label'] }}</option>
                                    @foreach($filter['options'] as $value => $label)
                                        <option value="{{ $value }}" {{ request($filter['name']) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>

                            @elseif($filter['type'] === 'checkbox')
                                @foreach($filter['options'] as $value => $label)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="{{ $filter['name'] }}[]" value="{{ $value }}"
                                               id="{{ $filter['name'] }}_{{ $value }}"
                                               {{ in_array($value, (array) request($filter['name'], [])) ? 'checked' : '' }}
                                               onchange="document.getElementById('filterForm').submit()">
                                        <label class="form-check-label" for="{{ $filter['name'] }}_{{ $value }}">
                                            {{ $label }}
                                        </label>
                                    </div>
                                @endforeach

                            @elseif($filter['type'] === 'range')
                                <div class="row">
                                    <div class="col-6">
                                        <label class="form-label small">From</label>
                                        <input type="number" name="{{ $filter['name'] }}_min"
                                               class="form-control form-control-sm"
                                               value="{{ request($filter['name'] . '_min') }}"
                                               placeholder="Min"
                                               onchange="document.getElementById('filterForm').submit()">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small">To</label>
                                        <input type="number" name="{{ $filter['name'] }}_max"
                                               class="form-control form-control-sm"
                                               value="{{ request($filter['name'] . '_max') }}"
                                               placeholder="Max"
                                               onchange="document.getElementById('filterForm').submit()">
                                    </div>
                                </div>

                            @elseif($filter['type'] === 'date_range')
                                <div class="mb-2">
                                    <label class="form-label small">From</label>
                                    <input type="date" name="{{ $filter['name'] }}_start"
                                           class="form-control form-control-sm"
                                           value="{{ request($filter['name'] . '_start') }}"
                                           onchange="document.getElementById('filterForm').submit()">
                                </div>
                                <div>
                                    <label class="form-label small">To</label>
                                    <input type="date" name="{{ $filter['name'] }}_end"
                                           class="form-control form-control-sm"
                                           value="{{ request($filter['name'] . '_end') }}"
                                           onchange="document.getElementById('filterForm').submit()">
                                </div>
                            @endif
                        </div>

                        @if(!$loop->last)
                            <hr>
                        @endif
                    @endforeach
                @endisset

                <!-- Sort Options -->
                @isset($sortOptions)
                    <hr>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sort By</label>
                        <select name="sort" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                            @foreach($sortOptions as $value => $label)
                                <option value="{{ $value }}" {{ request('sort') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Order</label>
                        <select name="direction" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                            <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descending</option>
                            <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                        </select>
                    </div>
                @endisset

                <!-- Clear Filters -->
                @if(request()->hasAny(['search']) ||
                    (isset($filters) && collect($filters)->pluck('name')->some(fn($name) => request()->has($name))) ||
                    request()->has(['sort', 'direction']))
                    <hr>
                    <div class="d-grid">
                        <a href="{{ $clearUrl ?? request()->url() }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-circle me-1"></i>Clear All Filters
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Active Filters Display -->
    @php
        $activeFilters = [];
        if(request('search')) $activeFilters[] = ['label' => 'Search', 'value' => request('search')];
        if(isset($filters)) {
            foreach($filters as $filter) {
                if(request($filter['name'])) {
                    $value = request($filter['name']);
                    if(is_array($value)) {
                        foreach($value as $v) {
                            $activeFilters[] = ['label' => $filter['label'], 'value' => $v];
                        }
                    } else {
                        $activeFilters[] = ['label' => $filter['label'], 'value' => $value];
                    }
                }
            }
        }
    @endphp

    @if(count($activeFilters) > 0)
        <div class="mt-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title mb-3">Active Filters</h6>
                    @foreach($activeFilters as $filter)
                        <span class="badge bg-primary me-1 mb-1">
                            {{ $filter['label'] }}: {{ $filter['value'] }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>