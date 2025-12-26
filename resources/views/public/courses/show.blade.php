@extends('layouts.app')

@section('title', $course->title ?? 'Course Details')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}" class="text-white text-decoration-none">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('courses.index') }}" class="text-white text-decoration-none">Courses</a>
                        </li>
                        <li class="breadcrumb-item active text-white-50" aria-current="page">
                            {{ $course->course_code ?? 'Course' }}
                        </li>
                    </ol>
                </nav>

                <!-- Course Meta -->
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge bg-light text-dark">{{ $course->course_code ?? 'COURSE 101' }}</span>
                    @if($course->level)
                        <span class="badge bg-warning text-dark">{{ ucfirst($course->level) }}</span>
                    @endif
                    @if($course->status === 'active')
                        <span class="badge bg-success">Currently Teaching</span>
                    @elseif($course->status === 'upcoming')
                        <span class="badge bg-info">Upcoming</span>
                    @else
                        <span class="badge bg-secondary">Previously Taught</span>
                    @endif
                </div>

                <!-- Course Title -->
                <h1 class="display-5 fw-bold mb-4">{{ $course->title ?? 'Course Title' }}</h1>

                <!-- Course Summary -->
                @if($course->summary ?? false)
                    <p class="lead mb-4">{{ $course->summary }}</p>
                @endif

                <!-- Quick Info -->
                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-white-50">
                            <i class="bi bi-award" style="font-size: 1.5rem;"></i>
                            <div class="mt-2">
                                <strong class="text-white d-block">{{ $course->credits ?? 3 }}</strong>
                                <small>Credits</small>
                            </div>
                        </div>
                    </div>
                    @if($course->semester && $course->year)
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-white-50">
                            <i class="bi bi-calendar" style="font-size: 1.5rem;"></i>
                            <div class="mt-2">
                                <strong class="text-white d-block">{{ ucfirst($course->semester) }}</strong>
                                <small>{{ $course->year }}</small>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($course->enrollment_count)
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-white-50">
                            <i class="bi bi-people" style="font-size: 1.5rem;"></i>
                            <div class="mt-2">
                                <strong class="text-white d-block">{{ $course->enrollment_count }}</strong>
                                <small>Students</small>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($course->meeting_times)
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-white-50">
                            <i class="bi bi-clock" style="font-size: 1.5rem;"></i>
                            <div class="mt-2">
                                <strong class="text-white d-block">{{ $course->meeting_times }}</strong>
                                <small>Meeting Times</small>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Course Content -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Course Image -->
                @if($course->image ?? false)
                <div class="mb-5">
                    <img src="{{ Storage::url($course->image) }}"
                         class="img-fluid rounded shadow-sm" alt="{{ $course->title }}"
                         style="width: 100%; height: 300px; object-fit: cover;">
                </div>
                @endif

                <!-- Course Description -->
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-info-circle me-2"></i>Course Description</h4>
                    </div>
                    <div class="card-body p-4">
                        @if($course->description ?? false)
                            <div class="course-description">
                                {!! nl2br(e($course->description)) !!}
                            </div>
                        @else
                            <p>This course provides comprehensive coverage of key concepts and practical applications in the field. Students will engage with both theoretical foundations and hands-on learning experiences designed to prepare them for advanced study and professional practice.</p>

                            <p>Through a combination of lectures, discussions, projects, and assessments, students will develop critical thinking skills and deep understanding of the subject matter. The course emphasizes both individual learning and collaborative work.</p>
                        @endif
                    </div>
                </div>

                <!-- Learning Objectives -->
                @if($course->learning_objectives ?? false)
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="bi bi-bullseye me-2"></i>Learning Objectives</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="learning-objectives">
                            {!! $course->learning_objectives !!}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Course Content/Syllabus -->
                @if($course->syllabus ?? false)
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0"><i class="bi bi-list-ol me-2"></i>Course Content</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="course-syllabus">
                            {!! $course->syllabus !!}
                        </div>
                    </div>
                </div>
                @else
                <!-- Sample Course Modules -->
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0"><i class="bi bi-list-ol me-2"></i>Course Modules</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <span class="badge bg-primary me-3">1</span>
                                    <div>
                                        <h6>Introduction & Foundations</h6>
                                        <small class="text-muted">Basic concepts and theoretical framework</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <span class="badge bg-primary me-3">2</span>
                                    <div>
                                        <h6>Core Concepts</h6>
                                        <small class="text-muted">Essential theories and principles</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <span class="badge bg-primary me-3">3</span>
                                    <div>
                                        <h6>Practical Applications</h6>
                                        <small class="text-muted">Real-world case studies and examples</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <span class="badge bg-primary me-3">4</span>
                                    <div>
                                        <h6>Advanced Topics</h6>
                                        <small class="text-muted">Specialized areas and current research</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Assessment Methods -->
                @if($course->assessment_methods ?? false)
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-header bg-warning text-white">
                        <h4 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Assessment Methods</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="assessment-methods">
                            {!! $course->assessment_methods !!}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Resources -->
                @if($course->required_textbooks || $course->recommended_readings)
                <div class="card shadow-sm border-0 mb-5">
                    <div class="card-header bg-secondary text-white">
                        <h4 class="mb-0"><i class="bi bi-book me-2"></i>Course Resources</h4>
                    </div>
                    <div class="card-body p-4">
                        @if($course->required_textbooks)
                            <h6>Required Textbooks</h6>
                            <div class="mb-3">
                                {!! nl2br(e($course->required_textbooks)) !!}
                            </div>
                        @endif

                        @if($course->recommended_readings)
                            <h6>Recommended Readings</h6>
                            <div>
                                {!! nl2br(e($course->recommended_readings)) !!}
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Course Information -->
                <div class="card shadow-sm border-0 mb-4 sticky-top" style="top: 2rem;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Course Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Course Code:</strong><br>
                            <span class="text-muted">{{ $course->course_code ?? 'COURSE 101' }}</span>
                        </div>

                        <div class="mb-3">
                            <strong>Credits:</strong><br>
                            <span class="text-muted">{{ $course->credits ?? 3 }} credit hours</span>
                        </div>

                        @if($course->level)
                        <div class="mb-3">
                            <strong>Level:</strong><br>
                            <span class="text-muted">{{ ucfirst($course->level) }}</span>
                        </div>
                        @endif

                        @if($course->semester && $course->year)
                        <div class="mb-3">
                            <strong>Semester:</strong><br>
                            <span class="text-muted">{{ ucfirst($course->semester) }} {{ $course->year }}</span>
                        </div>
                        @endif

                        @if($course->meeting_times)
                        <div class="mb-3">
                            <strong>Meeting Times:</strong><br>
                            <span class="text-muted">{{ $course->meeting_times }}</span>
                        </div>
                        @endif

                        @if($course->location)
                        <div class="mb-3">
                            <strong>Location:</strong><br>
                            <span class="text-muted">{{ $course->location }}</span>
                        </div>
                        @endif

                        @if($course->enrollment_limit)
                        <div class="mb-3">
                            <strong>Enrollment Limit:</strong><br>
                            <span class="text-muted">{{ $course->enrollment_limit }} students</span>
                        </div>
                        @endif

                        @if($course->prerequisites)
                        <div class="mb-3">
                            <strong>Prerequisites:</strong><br>
                            <span class="text-muted">{{ $course->prerequisites }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Instructor Information -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-person me-2"></i>Instructor</h5>
                    </div>
                    <div class="card-body text-center">
                        @if($teacher->avatar ?? false)
                            <img src="{{ Storage::url($teacher->avatar) }}"
                                 alt="{{ $teacher->name ?? 'Instructor' }}"
                                 class="rounded-circle mb-3"
                                 style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                 style="width: 80px; height: 80px;">
                                <i class="bi bi-person-circle text-muted" style="font-size: 2.5rem;"></i>
                            </div>
                        @endif

                        <h6 class="mb-2">{{ $teacher->name ?? 'Dr. [Your Name]' }}</h6>
                        <p class="text-muted mb-3">{{ $teacher->title ?? 'Professor' }}</p>

                        <div class="d-flex gap-2 justify-content-center">
                            <a href="{{ route('about') }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-person me-1"></i>Profile
                            </a>
                            <a href="{{ route('contact.show') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-envelope me-1"></i>Contact
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Course Materials -->
                @if($course->syllabus_file || $course->course_materials)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-download me-2"></i>Downloads</h5>
                    </div>
                    <div class="card-body">
                        @if($course->syllabus_file)
                            <div class="mb-2">
                                <a href="{{ Storage::url($course->syllabus_file) }}"
                                   class="text-decoration-none" target="_blank">
                                    <i class="bi bi-file-pdf text-danger me-2"></i>Course Syllabus
                                </a>
                            </div>
                        @endif

                        @if($course->course_materials)
                            @foreach(json_decode($course->course_materials, true) ?? [] as $material)
                                <div class="mb-2">
                                    <a href="{{ Storage::url($material['file']) }}"
                                       class="text-decoration-none" target="_blank">
                                        <i class="bi bi-file-earmark text-primary me-2"></i>{{ $material['name'] }}
                                    </a>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                @endif

                <!-- Related Courses -->
                @if(isset($relatedCourses) && $relatedCourses->count() > 0)
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0"><i class="bi bi-collection me-2"></i>Related Courses</h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach($relatedCourses->take(3) as $relatedCourse)
                            <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <h6 class="mb-2">
                                    <a href="{{ route('courses.show', $relatedCourse->slug) }}"
                                       class="text-decoration-none">
                                        {{ $relatedCourse->course_code }} - {{ $relatedCourse->title }}
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    {{ $relatedCourse->credits }} Credits • {{ ucfirst($relatedCourse->level) }}
                                </small>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Navigation -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row">
            @if(isset($previousCourse))
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0">
                        <div class="card-body">
                            <small class="text-muted">Previous Course</small>
                            <h6 class="mt-2">
                                <a href="{{ route('courses.show', $previousCourse->slug) }}"
                                   class="text-decoration-none">
                                    <i class="bi bi-arrow-left me-1"></i>{{ $previousCourse->course_code }} - {{ $previousCourse->title }}
                                </a>
                            </h6>
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($nextCourse))
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0">
                        <div class="card-body text-end">
                            <small class="text-muted">Next Course</small>
                            <h6 class="mt-2">
                                <a href="{{ route('courses.show', $nextCourse->slug) }}"
                                   class="text-decoration-none">
                                    {{ $nextCourse->course_code }} - {{ $nextCourse->title }}<i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </h6>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('courses.index') }}" class="btn btn-primary">
                <i class="bi bi-grid me-2"></i>View All Courses
            </a>
        </div>
    </div>
</section>
@endsection