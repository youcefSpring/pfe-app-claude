@extends('layouts.pfe-app')

@section('title', __('app.subjects'))

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">{{ __('app.subjects') }}</h1>
            @if(auth()->user()?->role === 'teacher')
                <a href="{{ route('subjects.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> {{ __('app.create') }} {{ __('app.subject') }}
                </a>
            @endif
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('subjects.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="grade" class="form-label">{{ __('app.grade') }}</label>
                        <select class="form-select" id="grade" name="grade">
                            {{-- <option value="">All Grades</option> --}}
                            <option value="master" {{ request('grade') === 'master' ? 'selected' : '' }}>Master 2</option>

                            {{-- <option value="license" {{ request('grade') === 'license' ? 'selected' : '' }}>L3</option> --}}
                            {{-- <option value="m1" {{ request('grade') === 'master 1' ? 'selected' : '' }}>Master 1</option> --}}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">{{ __('app.status') }}</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">{{ __('app.all_statuses') }}</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>{{ __('app.draft') }}</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('app.pending') }}</option>
                            <option value="validated" {{ request('status') === 'validated' ? 'selected' : '' }}>{{ __('app.validated') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="search" class="form-label">{{ __('app.search') }}</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}" placeholder="{{ __('app.search') }} {{ __('app.subjects') }}...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-primary">{{ __('app.filter') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Info -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="text-muted">
                Showing {{ $subjects->firstItem() ?? 0 }} to {{ $subjects->lastItem() ?? 0 }}
                of {{ $subjects->total() }} results
            </div>
            @if(request()->hasAny(['search', 'grade', 'status']))
                <a href="{{ route('subjects.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-circle"></i> Clear Filters
                </a>
            @endif
        </div>

        <!-- Subjects Table -->
        <div class="card">
            <div class="card-body">
                @if($subjects->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('app.title') }}</th>
                                    <th>{{ __('app.teacher') }}</th>
                                    <th>{{ __('app.grade') }}</th>
                                    <th>{{ __('app.status') }}</th>
                                    <th>{{ __('app.type') }}</th>
                                    <th>{{ __('app.created') }}</th>
                                    <th>{{ __('app.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subjects as $subject)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $subject->title }}</strong>
                                                <div class="text-muted small">{{ Str::limit($subject->description, 80) }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-nowrap">{{ $subject->teacher->name ?? __('app.tbd') }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ strtoupper($subject->target_grade ?? __('app.na')) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $subject->status === 'validated' ? 'success' : ($subject->status === 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($subject->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($subject->is_external)
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-building"></i> {{ __('app.external') }}
                                                </span>
                                            @else
                                                <span class="badge bg-primary">
                                                    <i class="bi bi-house"></i> {{ __('app.internal') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            {{ $subject->created_at->format('M d, Y') }}
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('subjects.show', $subject) }}"
                                                   class="btn btn-outline-primary btn-sm"
                                                   title="{{ __('app.view_details') }}">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if(auth()->user()?->id === $subject->teacher_id)
                                                    <a href="{{ route('subjects.edit', $subject) }}"
                                                       class="btn btn-outline-warning btn-sm"
                                                       title="{{ __('app.edit') }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('subjects.destroy', $subject) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-outline-danger btn-sm"
                                                                title="{{ __('app.delete') }}"
                                                                onclick="return confirm('{{ __('app.confirm_delete_subject') }}')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-journal-x text-muted" style="font-size: 4rem;"></i>
                        <h4 class="mt-3">No Subjects Found</h4>
                        <p class="text-muted">
                            @if(request()->hasAny(['search', 'grade', 'status']))
                                No subjects match your current filters. Try adjusting your search criteria.
                            @else
                                There are no subjects available at the moment.
                            @endif
                        </p>
                        @if(auth()->user()?->role === 'teacher')
                            <a href="{{ route('subjects.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus"></i> Create First Subject
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Pagination -->
        @if($subjects->hasPages())
            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Subjects pagination">
                    {{ $subjects->appends(request()->query())->links('pagination::bootstrap-4') }}
                </nav>
            </div>
        @endif
    </div>
@endsection
