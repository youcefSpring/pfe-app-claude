@extends('layouts.app')

@section('title', 'Courses')

@section('content')
<!-- Header Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold">Courses</h1>
                <p class="lead mb-0">Explore the courses I teach, complete with syllabi and learning resources.</p>
            </div>
            <div class="col-lg-4 text-center">
                <i class="bi bi-book" style="font-size: 6rem; opacity: 0.7;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Courses Content -->
<section class="py-5">
    <div class="container">
        <!-- Search and Filter -->
        <div class="row mb-4">
            <div class="col-md-8">
                <form method="GET" action="{{ route('courses.index') }}" class="d-flex gap-2">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search courses by title or description..."
                           value="{{ request('search') }}">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </form>
            </div>
            <div class="col-md-4 text-md-end">
                <span class="text-muted">{{ $courses->total() }} course(s) found</span>
            </div>
        </div>

        @if(request('search'))
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Showing results for "<strong>{{ request('search') }}</strong>"
            </div>
        @endif

        <!-- Courses Grid -->
        @if($courses->count() > 0)
            <div class="row g-4">
                @foreach($courses as $course)
                    <div class="col-lg-6 col-xl-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title mb-0">
                                        <a href="{{ route('courses.show', $course->slug) }}" class="text-decoration-none">
                                            {{ $course->title }}
                                        </a>
                                    </h5>
                                    @if($course->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($course->status === 'upcoming')
                                        <span class="badge bg-warning">Upcoming</span>
                                    @else
                                        <span class="badge bg-secondary">Completed</span>
                                    @endif
                                </div>

                                <p class="card-text text-muted">
                                    {{ Str::limit($course->description, 120) }}
                                </p>

                                <div class="mb-3">
                                    @if($course->start_date && $course->end_date)
                                        <small class="text-muted">
                                            <i class="bi bi-calendar-range me-1"></i>
                                            {{ $course->start_date->format('M j, Y') }} - {{ $course->end_date->format('M j, Y') }}
                                        </small>
                                    @elseif($course->start_date)
                                        <small class="text-muted">
                                            <i class="bi bi-calendar me-1"></i>
                                            Starts {{ $course->start_date->format('M j, Y') }}
                                        </small>
                                    @endif
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-eye me-1"></i>View Details
                                    </a>

                                    @if($course->syllabus_file_path)
                                        <a href="{{ route('courses.syllabus', $course->slug) }}"
                                           class="btn btn-outline-secondary btn-sm"
                                           target="_blank">
                                            <i class="bi bi-file-earmark-pdf me-1"></i>Syllabus
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($courses->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    {{ $courses->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <i class="bi bi-book text-muted mb-3" style="font-size: 4rem;"></i>

                @if(request('search'))
                    <h3 class="h4 text-muted mb-3">No courses found</h3>
                    <p class="text-muted mb-4">
                        No courses match your search criteria. Try adjusting your search terms.
                    </p>
                    <a href="{{ route('courses.index') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-1"></i>View All Courses
                    </a>
                @else
                    <h3 class="h4 text-muted mb-3">No courses available yet</h3>
                    <p class="text-muted">
                        Course information will be available here once courses are added.
                    </p>
                @endif
            </div>
        @endif

        <!-- Course Categories Info -->
        @if($courses->count() > 0)
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-info-circle text-primary me-2"></i>
                                About My Courses
                            </h5>
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                        <div>
                                            <h6 class="mb-1">Comprehensive Syllabi</h6>
                                            <small class="text-muted">Detailed course outlines with learning objectives and schedules</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                        <div>
                                            <h6 class="mb-1">Interactive Learning</h6>
                                            <small class="text-muted">Engaging teaching methods and hands-on projects</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                        <div>
                                            <h6 class="mb-1">Student Support</h6>
                                            <small class="text-muted">Office hours and personalized guidance for all students</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection

@section('styles')
<style>
    .card {
        border: 1px solid #e5e7eb;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .badge {
        font-size: 0.75rem;
    }

    .btn-sm {
        font-size: 0.8rem;
    }
</style>
@endsection