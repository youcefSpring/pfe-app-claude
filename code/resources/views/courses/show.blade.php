@extends('layouts.app')

@section('title', $course->title)

@section('content')
<!-- Course Header -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb text-white-50">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('courses.index') }}" class="text-white-50">Courses</a></li>
                        <li class="breadcrumb-item active text-white">{{ $course->title }}</li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold mb-3">{{ $course->title }}</h1>
                <p class="lead mb-3">{{ $course->description }}</p>

                <div class="d-flex flex-wrap gap-2 mb-3">
                    @if($course->status === 'active')
                        <span class="badge bg-success fs-6">Currently Active</span>
                    @elseif($course->status === 'upcoming')
                        <span class="badge bg-warning fs-6">Upcoming</span>
                    @else
                        <span class="badge bg-secondary fs-6">Completed</span>
                    @endif

                    @if($course->level)
                        <span class="badge bg-info fs-6">{{ ucfirst($course->level) }} Level</span>
                    @endif
                </div>

                @if($course->syllabus_file_path)
                    <a href="{{ route('courses.syllabus', $course->slug) }}" class="btn btn-light btn-lg" target="_blank">
                        <i class="bi bi-file-earmark-pdf me-2"></i>Download Syllabus
                    </a>
                @endif
            </div>
            <div class="col-lg-4 text-center">
                <i class="bi bi-book" style="font-size: 6rem; opacity: 0.7;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Course Details -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Course Content -->
                @if($course->content)
                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <h2 class="h4 mb-3">
                                <i class="bi bi-journal-text text-primary me-2"></i>
                                Course Overview
                            </h2>
                            <div class="course-content">
                                {!! nl2br(e($course->content)) !!}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Learning Objectives -->
                @if($course->learning_objectives)
                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <h2 class="h4 mb-3">
                                <i class="bi bi-target text-primary me-2"></i>
                                Learning Objectives
                            </h2>
                            <div class="objectives-content">
                                {!! nl2br(e($course->learning_objectives)) !!}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Prerequisites -->
                @if($course->prerequisites)
                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <h2 class="h4 mb-3">
                                <i class="bi bi-list-check text-primary me-2"></i>
                                Prerequisites
                            </h2>
                            <div class="prerequisites-content">
                                {!! nl2br(e($course->prerequisites)) !!}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Assessment Methods -->
                @if($course->assessment_methods)
                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <h2 class="h4 mb-3">
                                <i class="bi bi-clipboard-check text-primary me-2"></i>
                                Assessment Methods
                            </h2>
                            <div class="assessment-content">
                                {!! nl2br(e($course->assessment_methods)) !!}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Resources -->
                @if($course->resources)
                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <h2 class="h4 mb-3">
                                <i class="bi bi-collection text-primary me-2"></i>
                                Course Resources
                            </h2>
                            <div class="resources-content">
                                {!! nl2br(e($course->resources)) !!}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Contact & Office Hours -->
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 mb-3">
                            <i class="bi bi-person-lines-fill text-primary me-2"></i>
                            Contact & Support
                        </h2>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="mb-2">
                                        <i class="bi bi-envelope text-muted me-2"></i>
                                        Get Help
                                    </h6>
                                    <p class="text-muted mb-2 small">
                                        Have questions about this course? Need clarification on assignments?
                                    </p>
                                    <a href="{{ route('contact.show') }}" class="btn btn-outline-primary btn-sm">
                                        Contact Me
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="mb-2">
                                        <i class="bi bi-clock text-muted me-2"></i>
                                        Office Hours
                                    </h6>
                                    <p class="text-muted mb-2 small">
                                        Available for student consultations and academic support.
                                    </p>
                                    <small class="text-muted">By appointment - contact me to schedule</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Course Info -->
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-info-circle text-primary me-2"></i>
                            Course Information
                        </h5>

                        @if($course->start_date)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Start Date:</span>
                                <span class="fw-medium">{{ $course->start_date->format('M j, Y') }}</span>
                            </div>
                        @endif

                        @if($course->end_date)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">End Date:</span>
                                <span class="fw-medium">{{ $course->end_date->format('M j, Y') }}</span>
                            </div>
                        @endif

                        @if($course->credits)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Credits:</span>
                                <span class="fw-medium">{{ $course->credits }}</span>
                            </div>
                        @endif

                        @if($course->level)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Level:</span>
                                <span class="fw-medium">{{ ucfirst($course->level) }}</span>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Status:</span>
                            @if($course->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($course->status === 'upcoming')
                                <span class="badge bg-warning">Upcoming</span>
                            @else
                                <span class="badge bg-secondary">Completed</span>
                            @endif
                        </div>

                        @if($course->syllabus_file_path)
                            <hr class="my-3">
                            <div class="d-grid">
                                <a href="{{ route('courses.syllabus', $course->slug) }}"
                                   class="btn btn-primary"
                                   target="_blank">
                                    <i class="bi bi-file-earmark-pdf me-1"></i>
                                    Download Syllabus
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Instructor Info -->
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-person-badge text-primary me-2"></i>
                            Instructor
                        </h5>

                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-person-circle text-muted me-3" style="font-size: 2rem;"></i>
                            <div>
                                <h6 class="mb-0">{{ $course->user->name ?? 'Instructor' }}</h6>
                                <small class="text-muted">Course Instructor</small>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('contact.show') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-envelope me-1"></i>Contact
                            </a>
                            <a href="{{ route('about') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-person me-1"></i>About Me
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Related Courses -->
                @if($relatedCourses && $relatedCourses->count() > 0)
                    <div class="card">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3">
                                <i class="bi bi-collection text-primary me-2"></i>
                                Related Courses
                            </h5>

                            @foreach($relatedCourses as $relatedCourse)
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <a href="{{ route('courses.show', $relatedCourse->slug) }}"
                                               class="text-decoration-none">
                                                {{ $relatedCourse->title }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            @if($relatedCourse->start_date)
                                                {{ $relatedCourse->start_date->format('M Y') }}
                                            @endif
                                        </small>
                                    </div>
                                    @if($relatedCourse->status === 'active')
                                        <span class="badge bg-success ms-2">Active</span>
                                    @endif
                                </div>
                            @endforeach

                            <div class="text-center mt-3">
                                <a href="{{ route('courses.index') }}" class="btn btn-outline-primary btn-sm">
                                    View All Courses <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
    .course-content,
    .objectives-content,
    .prerequisites-content,
    .assessment-content,
    .resources-content {
        line-height: 1.7;
        font-size: 1.05rem;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        color: rgba(255, 255, 255, 0.5);
    }

    .breadcrumb-item a {
        text-decoration: none;
    }

    .breadcrumb-item a:hover {
        text-decoration: underline;
    }

    .card {
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .border {
        border-color: #e5e7eb !important;
    }
</style>
@endsection