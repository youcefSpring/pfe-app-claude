@extends('layouts.pfe')

@section('title', 'Browse Subjects - PFE Platform')
@section('contentheader', 'Browse Available Subjects')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('pfe.student.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Browse Subjects</li>
@endsection

@section('content')

<!-- Filters and Search -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-search mr-2"></i>
            Filter Subjects
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('pfe.student.subjects.browse') }}">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="search">Search</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}" placeholder="Search by title, keywords...">
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
                            <option value="cybersecurity" {{ request('domain') === 'cybersecurity' ? 'selected' : '' }}>Cybersecurity</option>
                            <option value="web_development" {{ request('domain') === 'web_development' ? 'selected' : '' }}>Web Development</option>
                            <option value="mobile_development" {{ request('domain') === 'mobile_development' ? 'selected' : '' }}>Mobile Development</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="difficulty">Difficulty</label>
                        <select class="form-control" id="difficulty" name="difficulty">
                            <option value="">All Levels</option>
                            <option value="beginner" {{ request('difficulty') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="intermediate" {{ request('difficulty') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="advanced" {{ request('difficulty') === 'advanced' ? 'selected' : '' }}>Advanced</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="sort">Sort By</label>
                        <select class="form-control" id="sort" name="sort">
                            <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Most Popular</option>
                            <option value="alphabetical" {{ request('sort') === 'alphabetical' ? 'selected' : '' }}>Alphabetical</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Subjects Grid -->
<div class="row">
    @forelse($subjects as $subject)
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    {{ Str::limit($subject->title, 50) }}
                    @if($subject->is_favorite ?? false)
                        <i class="fas fa-heart text-danger ml-2"></i>
                    @endif
                </h5>
            </div>
            <div class="card-body d-flex flex-column">
                <div class="mb-3">
                    <span class="badge badge-{{ $subject->difficulty === 'beginner' ? 'success' : ($subject->difficulty === 'intermediate' ? 'warning' : 'danger') }}">
                        {{ ucfirst($subject->difficulty ?? 'intermediate') }}
                    </span>
                    <span class="badge badge-info ml-1">
                        {{ ucfirst(str_replace('_', ' ', $subject->domain ?? 'general')) }}
                    </span>
                </div>

                <p class="card-text flex-grow-1">
                    {{ Str::limit($subject->description, 120) }}
                </p>

                <div class="mb-3">
                    <strong>Supervisor:</strong><br>
                    <span class="text-muted">{{ $subject->supervisor->first_name ?? 'TBD' }} {{ $subject->supervisor->last_name ?? '' }}</span>
                </div>

                <div class="mb-3">
                    <strong>Technologies:</strong><br>
                    @if($subject->required_skills)
                        @foreach(explode(',', $subject->required_skills) as $skill)
                            <span class="badge badge-secondary badge-sm">{{ trim($skill) }}</span>
                        @endforeach
                    @else
                        <span class="text-muted">Not specified</span>
                    @endif
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-users"></i> {{ $subject->interested_teams ?? 0 }} teams interested
                        </small>
                        <small class="text-muted">
                            <i class="fas fa-clock"></i> {{ $subject->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>

                <div class="mt-auto">
                    <div class="btn-group btn-block" role="group">
                        <a href="{{ route('pfe.student.subjects.show', $subject->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="toggleFavorite({{ $subject->id }})">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-search fa-4x text-muted mb-4"></i>
                <h4>No subjects found</h4>
                <p class="text-muted mb-4">
                    No subjects match your current search criteria. Try adjusting your filters or search terms.
                </p>
                <a href="{{ route('pfe.student.subjects.browse') }}" class="btn btn-primary">
                    <i class="fas fa-refresh"></i> Clear Filters
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($subjects->hasPages())
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-center">
            {{ $subjects->withQueryString()->links() }}
        </div>
    </div>
</div>
@endif

<!-- Subject Interest Stats -->
@if($subjects->count() > 0)
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Most Popular Subjects
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm">
                    <tbody>
                        @foreach($popularSubjects ?? [] as $popular)
                        <tr>
                            <td>{{ Str::limit($popular['title'], 40) }}</td>
                            <td>
                                <span class="badge badge-primary">{{ $popular['interested_teams'] ?? 0 }} teams</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-lightbulb mr-2"></i>
                    Tips for Choosing
                </h3>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        Choose subjects aligned with your career goals
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        Consider your team's collective skills
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        Review supervisor availability and expertise
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        Set preferences early to increase chances
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
function toggleFavorite(subjectId) {
    fetch(`{{ route('pfe.student.subjects.toggle-favorite', '') }}/${subjectId}`, {
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
            // Toggle heart icon
            const heartIcon = event.target.closest('button').querySelector('i');
            heartIcon.classList.toggle('text-danger');

            // Update card header heart if present
            const cardTitle = event.target.closest('.card').querySelector('.card-title i.fa-heart');
            if (data.is_favorite) {
                if (!cardTitle) {
                    const newHeart = document.createElement('i');
                    newHeart.className = 'fas fa-heart text-danger ml-2';
                    event.target.closest('.card').querySelector('.card-title').appendChild(newHeart);
                }
            } else if (cardTitle) {
                cardTitle.remove();
            }

            // Show toast notification
            toastr.success(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('An error occurred while updating favorites');
    });
}
</script>
@endpush