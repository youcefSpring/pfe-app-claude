@extends('layouts.pfe')

@section('title', 'My Subjects - PFE Platform')
@section('contentheader', 'My Subjects')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('pfe.teacher.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">My Subjects</li>
@endsection

@section('content')

<!-- Action Buttons -->
<div class="row mb-3">
    <div class="col-12">
        <div class="float-right">
            <a href="{{ route('pfe.teacher.subjects.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Subject
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['total'] ?? 0 }}</h3>
                <p>Total Subjects</p>
            </div>
            <div class="icon">
                <i class="fas fa-book"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['published'] ?? 0 }}</h3>
                <p>Published</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['pending'] ?? 0 }}</h3>
                <p>Pending Approval</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['interested_teams'] ?? 0 }}</h3>
                <p>Teams Interested</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter mr-2"></i>
            Filter Subjects
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('pfe.teacher.subjects.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="domain">Domain</label>
                        <select class="form-control" id="domain" name="domain">
                            <option value="">All Domains</option>
                            <option value="computer_science" {{ request('domain') === 'computer_science' ? 'selected' : '' }}>Computer Science</option>
                            <option value="software_engineering" {{ request('domain') === 'software_engineering' ? 'selected' : '' }}>Software Engineering</option>
                            <option value="data_science" {{ request('domain') === 'data_science' ? 'selected' : '' }}>Data Science</option>
                            <option value="ai_ml" {{ request('domain') === 'ai_ml' ? 'selected' : '' }}>AI & Machine Learning</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="search">Search</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}" placeholder="Search by title or keywords...">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Subjects List -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list mr-2"></i>
            Subjects ({{ $subjects->total() ?? 0 }})
        </h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Domain</th>
                        <th>Interest</th>
                        <th>Created</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $subject)
                    <tr>
                        <td>
                            <div>
                                <strong>{{ Str::limit($subject->title, 50) }}</strong>
                                @if($subject->is_urgent ?? false)
                                    <span class="badge badge-warning ml-1">Urgent</span>
                                @endif
                            </div>
                            <small class="text-muted">{{ Str::limit($subject->description, 100) }}</small>
                        </td>
                        <td>
                            <span class="badge badge-{{
                                $subject->status === 'published' ? 'success' :
                                ($subject->status === 'approved' ? 'primary' :
                                ($subject->status === 'submitted' ? 'warning' :
                                ($subject->status === 'rejected' ? 'danger' : 'secondary')))
                            }}">
                                {{ ucfirst($subject->status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-info">
                                {{ ucfirst(str_replace('_', ' ', $subject->domain ?? 'general')) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-users text-primary mr-1"></i>
                                <span>{{ $subject->interested_teams ?? 0 }} teams</span>
                                @if(($subject->interested_teams ?? 0) > 5)
                                    <i class="fas fa-fire text-danger ml-1" title="High interest"></i>
                                @endif
                            </div>
                        </td>
                        <td>
                            <small>{{ $subject->created_at->format('M j, Y') }}</small>
                        </td>
                        <td>
                            <small>{{ $subject->updated_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('pfe.teacher.subjects.show', $subject->id) }}"
                                   class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('pfe.teacher.subjects.edit', $subject->id) }}"
                                   class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($subject->status === 'draft')
                                    <button type="button" class="btn btn-sm btn-outline-success"
                                            onclick="submitSubject({{ $subject->id }})" title="Submit for approval">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                @endif
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-info dropdown-toggle"
                                            data-toggle="dropdown" title="More actions">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('pfe.teacher.subjects.clone', $subject->id) }}">
                                            <i class="fas fa-copy mr-2"></i>Clone
                                        </a>
                                        <a class="dropdown-item" href="{{ route('pfe.teacher.subjects.interest', $subject->id) }}">
                                            <i class="fas fa-chart-line mr-2"></i>View Interest
                                        </a>
                                        @if($subject->status !== 'published')
                                            <div class="dropdown-divider"></div>
                                            <button class="dropdown-item text-warning" onclick="archiveSubject({{ $subject->id }})">
                                                <i class="fas fa-archive mr-2"></i>Archive
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-book fa-3x mb-3"></i>
                                <p>No subjects found</p>
                                <a href="{{ route('pfe.teacher.subjects.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create Your First Subject
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($subjects->hasPages())
    <div class="card-footer">
        {{ $subjects->withQueryString()->links() }}
    </div>
    @endif
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt mr-2"></i>
                    Quick Actions
                </h3>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('pfe.teacher.subjects.create') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-plus text-primary mr-2"></i>
                        Create New Subject
                    </a>
                    <a href="#" class="list-group-item list-group-item-action" onclick="submitAllDrafts()">
                        <i class="fas fa-paper-plane text-success mr-2"></i>
                        Submit All Drafts for Approval
                    </a>
                    <a href="{{ route('pfe.subjects.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-eye text-info mr-2"></i>
                        Browse All Published Subjects
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-lightbulb mr-2"></i>
                    Tips & Guidelines
                </h3>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        Provide clear and detailed subject descriptions
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        Specify required skills and technologies
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        Include realistic project objectives
                    </li>
                    <li class="mb-0">
                        <i class="fas fa-check text-success mr-2"></i>
                        Submit subjects early to allow review time
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function submitSubject(subjectId) {
    if (confirm('Are you sure you want to submit this subject for approval? You will not be able to edit it afterward until reviewed.')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('pfe.teacher.subjects.submit', '') }}/${subjectId}`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function archiveSubject(subjectId) {
    if (confirm('Are you sure you want to archive this subject? It will no longer be visible to students.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('pfe.teacher.subjects.archive', '') }}/${subjectId}`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function submitAllDrafts() {
    if (confirm('Are you sure you want to submit all draft subjects for approval?')) {
        // This would need to be implemented as a bulk action
        fetch('{{ route("pfe.teacher.subjects.bulk-submit") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
}
</script>
@endpush