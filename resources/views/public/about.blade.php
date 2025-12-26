@extends('layouts.app')

@section('title', 'About Me')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-4 text-center mb-4 mb-lg-0">
                @if($teacher->avatar ?? false)
                    <img src="{{ Storage::url($teacher->avatar) }}"
                         alt="{{ $teacher->name ?? 'Teacher' }}"
                         class="img-fluid rounded-circle shadow-lg"
                         style="max-width: 300px; max-height: 300px; object-fit: cover;">
                @else
                    <div class="bg-white rounded-circle mx-auto shadow-lg d-flex align-items-center justify-content-center"
                         style="width: 250px; height: 250px;">
                        <i class="bi bi-person-circle text-primary" style="font-size: 8rem;"></i>
                    </div>
                @endif
            </div>
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">
                    {{ $teacher->name ?? 'Dr. [Your Name]' }}
                </h1>
                @if($teacher->title ?? false)
                    <h4 class="mb-3">{{ $teacher->title }}</h4>
                @endif
                @if($teacher->department ?? false)
                    <p class="lead mb-4">
                        <i class="bi bi-building me-2"></i>{{ $teacher->department }}
                    </p>
                @endif
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('contact.show') }}" class="btn btn-light btn-lg">
                        <i class="bi bi-envelope me-2"></i>Contact Me
                    </a>
                    <a href="{{ route('download-cv') }}" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-download me-2"></i>Download CV
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Content -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-5">
                        <h2 class="mb-4">About Me</h2>
                        @if($teacher->bio ?? false)
                            <div class="lead mb-4">
                                {!! nl2br(e($teacher->bio)) !!}
                            </div>
                        @else
                            <div class="lead mb-4">
                                Welcome to my academic portfolio! I am passionate about education, research, and knowledge sharing.
                                Through my work, I strive to make complex concepts accessible and engaging for students while
                                contributing to the advancement of knowledge in my field.
                            </div>
                        @endif

                        @if($teacher->specializations ?? false)
                            <h4 class="mt-5 mb-3">Areas of Expertise</h4>
                            <div class="row">
                                @php
                                    $specializations = explode("\n", $teacher->specializations);
                                @endphp
                                @foreach($specializations as $specialization)
                                    @if(trim($specialization))
                                        <div class="col-md-6 mb-2">
                                            <i class="bi bi-check-circle text-primary me-2"></i>{{ trim($specialization) }}
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Contact Information -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Contact Information</h5>
                    </div>
                    <div class="card-body">
                        @if($teacher->email ?? false)
                            <div class="mb-3">
                                <strong><i class="bi bi-envelope text-primary me-2"></i>Email:</strong><br>
                                <a href="mailto:{{ $teacher->email }}" class="text-decoration-none">
                                    {{ $teacher->email }}
                                </a>
                            </div>
                        @endif

                        @if($teacher->phone ?? false)
                            <div class="mb-3">
                                <strong><i class="bi bi-telephone text-primary me-2"></i>Phone:</strong><br>
                                <a href="tel:{{ $teacher->phone }}" class="text-decoration-none">
                                    {{ $teacher->phone }}
                                </a>
                            </div>
                        @endif

                        @if($teacher->office_location ?? false)
                            <div class="mb-3">
                                <strong><i class="bi bi-geo-alt text-primary me-2"></i>Office:</strong><br>
                                {{ $teacher->office_location }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Links -->
                @if(($teacher->website ?? false) || ($teacher->linkedin_url ?? false) || ($teacher->orcid_id ?? false) || ($teacher->google_scholar_url ?? false))
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-link-45deg me-2"></i>Academic Profiles</h5>
                    </div>
                    <div class="card-body">
                        @if($teacher->website ?? false)
                            <div class="mb-2">
                                <a href="{{ $teacher->website }}" target="_blank" rel="noopener" class="text-decoration-none">
                                    <i class="bi bi-globe text-primary me-2"></i>Personal Website
                                </a>
                            </div>
                        @endif

                        @if($teacher->linkedin_url ?? false)
                            <div class="mb-2">
                                <a href="{{ $teacher->linkedin_url }}" target="_blank" rel="noopener" class="text-decoration-none">
                                    <i class="bi bi-linkedin text-primary me-2"></i>LinkedIn Profile
                                </a>
                            </div>
                        @endif

                        @if($teacher->orcid_id ?? false)
                            <div class="mb-2">
                                <a href="https://orcid.org/{{ $teacher->orcid_id }}" target="_blank" rel="noopener" class="text-decoration-none">
                                    <i class="bi bi-person-badge text-primary me-2"></i>ORCID iD
                                </a>
                            </div>
                        @endif

                        @if($teacher->google_scholar_url ?? false)
                            <div class="mb-2">
                                <a href="{{ $teacher->google_scholar_url }}" target="_blank" rel="noopener" class="text-decoration-none">
                                    <i class="bi bi-mortarboard text-primary me-2"></i>Google Scholar
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Teaching Philosophy -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Teaching Philosophy</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">
                            I believe in creating an inclusive and engaging learning environment where students
                            are encouraged to think critically, ask questions, and explore new ideas. My goal is
                            to inspire lifelong learning and help students develop the skills they need to succeed
                            in their academic and professional endeavors.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Academic Background -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center mb-5">Academic Journey</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-4">
                        <h4 class="card-title">
                            <i class="bi bi-mortarboard text-primary me-2"></i>Education
                        </h4>
                        <div class="timeline">
                            <!-- Add education entries here - these would come from a database in a real app -->
                            <div class="mb-3">
                                <h6 class="mb-1">Ph.D. in [Field]</h6>
                                <p class="text-muted mb-1">[University Name] • [Year]</p>
                                <small class="text-muted">Dissertation: [Title]</small>
                            </div>
                            <div class="mb-3">
                                <h6 class="mb-1">M.A./M.S. in [Field]</h6>
                                <p class="text-muted mb-1">[University Name] • [Year]</p>
                            </div>
                            <div class="mb-3">
                                <h6 class="mb-1">B.A./B.S. in [Field]</h6>
                                <p class="text-muted mb-1">[University Name] • [Year]</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-4">
                        <h4 class="card-title">
                            <i class="bi bi-briefcase text-success me-2"></i>Experience
                        </h4>
                        <div class="timeline">
                            <!-- Add experience entries here -->
                            <div class="mb-3">
                                <h6 class="mb-1">[Current Position]</h6>
                                <p class="text-muted mb-1">[Institution] • [Year] - Present</p>
                                <small class="text-muted">Brief description of responsibilities</small>
                            </div>
                            <div class="mb-3">
                                <h6 class="mb-1">[Previous Position]</h6>
                                <p class="text-muted mb-1">[Institution] • [Year Range]</p>
                                <small class="text-muted">Brief description of responsibilities</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Research Interests -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h2 class="mb-5">Research Interests</h2>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-5 text-center">
                        <p class="lead mb-4">
                            My research focuses on [describe your main research areas]. I am particularly interested in
                            [specific topics or methodologies] and their applications in [relevant fields or contexts].
                        </p>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-arrow-right text-primary me-3"></i>
                                    <span>[Research Area 1]</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-arrow-right text-primary me-3"></i>
                                    <span>[Research Area 2]</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-arrow-right text-primary me-3"></i>
                                    <span>[Research Area 3]</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-arrow-right text-primary me-3"></i>
                                    <span>[Research Area 4]</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('publications.index') }}" class="btn btn-primary me-2">
                                <i class="bi bi-journal-text me-2"></i>View Publications
                            </a>
                            <a href="{{ route('projects.index') }}" class="btn btn-outline-primary">
                                <i class="bi bi-code-slash me-2"></i>Research Projects
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection