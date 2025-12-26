@extends('layouts.pfe-app')

@section('title', 'Review Project')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Review Project</h1>
            <h6 class="text-muted">{{ $project->subject->title ?? 'Project' }}</h6>
        </div>
        <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Project
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Project Review Form</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('projects.submit-review', $project) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="overall_rating" class="form-label">Overall Rating <span class="text-danger">*</span></label>
                            <div class="rating-stars mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="star" data-rating="{{ $i }}" role="button">
                                        <i class="bi bi-star"></i>
                                    </span>
                                @endfor
                            </div>
                            <input type="hidden" id="overall_rating" name="overall_rating" value="{{ old('overall_rating') }}" required>
                            @error('overall_rating')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="technical_score" class="form-label">Technical Implementation (0-20)</label>
                                <input type="number" class="form-control @error('technical_score') is-invalid @enderror"
                                       id="technical_score" name="technical_score"
                                       value="{{ old('technical_score') }}" min="0" max="20" step="0.5">
                                @error('technical_score')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="documentation_score" class="form-label">Documentation Quality (0-20)</label>
                                <input type="number" class="form-control @error('documentation_score') is-invalid @enderror"
                                       id="documentation_score" name="documentation_score"
                                       value="{{ old('documentation_score') }}" min="0" max="20" step="0.5">
                                @error('documentation_score')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="innovation_score" class="form-label">Innovation & Creativity (0-20)</label>
                                <input type="number" class="form-control @error('innovation_score') is-invalid @enderror"
                                       id="innovation_score" name="innovation_score"
                                       value="{{ old('innovation_score') }}" min="0" max="20" step="0.5">
                                @error('innovation_score')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="methodology_score" class="form-label">Methodology & Process (0-20)</label>
                                <input type="number" class="form-control @error('methodology_score') is-invalid @enderror"
                                       id="methodology_score" name="methodology_score"
                                       value="{{ old('methodology_score') }}" min="0" max="20" step="0.5">
                                @error('methodology_score')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="comments" class="form-label">Detailed Comments <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('comments') is-invalid @enderror"
                                      id="comments" name="comments" rows="6" required
                                      placeholder="Provide detailed feedback on the project progress, quality, areas for improvement, and recommendations...">{{ old('comments') }}</textarea>
                            @error('comments')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Minimum 50 characters required</div>
                        </div>

                        <div class="mb-3">
                            <label for="strengths" class="form-label">Project Strengths</label>
                            <textarea class="form-control @error('strengths') is-invalid @enderror"
                                      id="strengths" name="strengths" rows="3"
                                      placeholder="What are the key strengths and positive aspects of this project?">{{ old('strengths') }}</textarea>
                            @error('strengths')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="areas_for_improvement" class="form-label">Areas for Improvement</label>
                            <textarea class="form-control @error('areas_for_improvement') is-invalid @enderror"
                                      id="areas_for_improvement" name="areas_for_improvement" rows="3"
                                      placeholder="What aspects need improvement and how can the team enhance their work?">{{ old('areas_for_improvement') }}</textarea>
                            @error('areas_for_improvement')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="next_steps" class="form-label">Recommended Next Steps</label>
                            <textarea class="form-control @error('next_steps') is-invalid @enderror"
                                      id="next_steps" name="next_steps" rows="3"
                                      placeholder="What should the team focus on next? Any specific milestones or deliverables?">{{ old('next_steps') }}</textarea>
                            @error('next_steps')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="ready_for_defense" class="form-label">Defense Readiness</label>
                            <select class="form-select @error('ready_for_defense') is-invalid @enderror"
                                    id="ready_for_defense" name="ready_for_defense">
                                <option value="">Select readiness level</option>
                                <option value="ready" {{ old('ready_for_defense') == 'ready' ? 'selected' : '' }}>
                                    Ready for Defense
                                </option>
                                <option value="needs_minor_work" {{ old('ready_for_defense') == 'needs_minor_work' ? 'selected' : '' }}>
                                    Needs Minor Work
                                </option>
                                <option value="needs_major_work" {{ old('ready_for_defense') == 'needs_major_work' ? 'selected' : '' }}>
                                    Needs Major Work
                                </option>
                                <option value="not_ready" {{ old('ready_for_defense') == 'not_ready' ? 'selected' : '' }}>
                                    Not Ready
                                </option>
                            </select>
                            @error('ready_for_defense')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-lg"></i> Submit Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Project Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Project Information</h5>
                </div>
                <div class="card-body">
                    <h6>{{ $project->subject->title }}</h6>
                    <p class="text-muted mb-2">{{ $project->team->name }}</p>

                    <div class="small">
                        <p class="mb-1"><strong>Status:</strong>
                            <span class="badge bg-{{ $project->status === 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($project->status) }}
                            </span>
                        </p>
                        <p class="mb-1"><strong>Started:</strong> {{ $project->start_date ? $project->start_date->format('M d, Y') : 'N/A' }}</p>
                        <p class="mb-1"><strong>Due:</strong> {{ $project->end_date ? $project->end_date->format('M d, Y') : 'N/A' }}</p>
                        <p class="mb-0"><strong>Submissions:</strong> {{ $project->submissions->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Team Members -->
            @if($project->team && $project->team->members->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Team Members</h5>
                    </div>
                    <div class="card-body">
                        @foreach($project->team->members as $member)
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar-sm bg-{{ $member->is_leader ? 'primary' : 'secondary' }} text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                    {{ substr($member->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <h6 class="mb-0 small">{{ $member->user->name }}</h6>
                                    <small class="text-muted">{{ $member->user->email }}</small>
                                    @if($member->is_leader)
                                        <br><span class="badge bg-primary">Leader</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Recent Activity -->
            @if($project->submissions->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Submissions</h5>
                    </div>
                    <div class="card-body">
                        @foreach($project->submissions->sortByDesc('created_at')->take(3) as $submission)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="mb-0 small">{{ $submission->title }}</h6>
                                    <small class="text-muted">{{ $submission->created_at->diffForHumans() }}</small>
                                </div>
                                <span class="badge bg-{{ $submission->status === 'approved' ? 'success' : ($submission->status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($submission->status) }}
                                </span>
                            </div>
                            @if(!$loop->last)<hr class="my-2">@endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Review Guidelines -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Review Guidelines</h5>
                </div>
                <div class="card-body">
                    <ul class="small mb-0">
                        <li>Evaluate technical implementation and code quality</li>
                        <li>Assess documentation completeness and clarity</li>
                        <li>Consider innovation and creative problem-solving</li>
                        <li>Review methodology and development process</li>
                        <li>Provide constructive feedback for improvement</li>
                        <li>Suggest specific next steps and milestones</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 12px;
    font-weight: 600;
}

.rating-stars {
    font-size: 24px;
}

.star {
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s;
}

.star:hover,
.star.active {
    color: #ffc107;
}

.star.active ~ .star {
    color: #ddd;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('overall_rating');

    stars.forEach(function(star, index) {
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            ratingInput.value = rating;

            // Update star display
            stars.forEach(function(s, i) {
                if (i < rating) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
        });

        star.addEventListener('mouseover', function() {
            const rating = this.getAttribute('data-rating');
            stars.forEach(function(s, i) {
                if (i < rating) {
                    s.style.color = '#ffc107';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });
    });

    // Reset stars on mouse leave
    document.querySelector('.rating-stars').addEventListener('mouseleave', function() {
        const currentRating = ratingInput.value;
        stars.forEach(function(s, i) {
            if (i < currentRating) {
                s.style.color = '#ffc107';
            } else {
                s.style.color = '#ddd';
            }
        });
    });

    // Character counter for comments
    const commentsTextarea = document.getElementById('comments');
    const minChars = 50;

    commentsTextarea.addEventListener('input', function() {
        const currentLength = this.value.length;
        const formText = this.nextElementSibling;

        if (currentLength < minChars) {
            formText.textContent = `${minChars - currentLength} more characters needed`;
            formText.className = 'form-text text-warning';
        } else {
            formText.textContent = `${currentLength} characters`;
            formText.className = 'form-text text-success';
        }
    });
});
</script>
@endsection