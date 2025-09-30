@extends('layouts.app')

@section('title', 'About Me')

@section('content')
<!-- Header Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold">About Me</h1>
                <p class="lead mb-0">Learn more about my background, expertise, and passion for teaching and research.</p>
            </div>
            <div class="col-lg-4 text-center">
                <i class="bi bi-person-circle" style="font-size: 6rem; opacity: 0.7;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Biography -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 mb-3">
                            <i class="bi bi-person-badge text-primary me-2"></i>
                            Biography
                        </h2>

                        @if($user->bio ?? null)
                            <div class="bio-content">
                                {!! nl2br(e($user->bio)) !!}
                            </div>
                        @else
                            <p class="text-muted">
                                I am passionate about education, research, and technology. Through my work,
                                I strive to bridge the gap between theoretical knowledge and practical application,
                                inspiring students and contributing to the academic community.
                            </p>
                            <p class="text-muted">
                                My research interests span across multiple domains, and I enjoy collaborating
                                with students and colleagues on innovative projects that push the boundaries
                                of our understanding.
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Education & Credentials -->
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 mb-3">
                            <i class="bi bi-mortarboard text-primary me-2"></i>
                            Education & Credentials
                        </h2>

                        @if($credentials && $credentials->count() > 0)
                            <div class="row g-3">
                                @foreach($credentials as $credential)
                                    <div class="col-12">
                                        <div class="card border-start border-primary border-3">
                                            <div class="card-body">
                                                <h5 class="card-title mb-2">{{ $credential->title }}</h5>
                                                <h6 class="card-subtitle mb-2 text-muted">{{ $credential->issuing_organization }}</h6>
                                                <p class="text-muted mb-0">
                                                    <i class="bi bi-calendar me-1"></i>
                                                    {{ $credential->issue_date->format('F Y') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-mortarboard text-muted mb-3" style="font-size: 3rem;"></i>
                                <p class="text-muted">Educational background and credentials will be available soon.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Research Interests -->
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 mb-3">
                            <i class="bi bi-lightbulb text-primary me-2"></i>
                            Research Interests
                        </h2>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                    <div>
                                        <h6 class="mb-1">Educational Technology</h6>
                                        <small class="text-muted">Integration of technology in learning environments</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                    <div>
                                        <h6 class="mb-1">Software Development</h6>
                                        <small class="text-muted">Modern web applications and systems</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                    <div>
                                        <h6 class="mb-1">Data Science</h6>
                                        <small class="text-muted">Analytics and machine learning applications</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                    <div>
                                        <h6 class="mb-1">Digital Innovation</h6>
                                        <small class="text-muted">Emerging technologies and their impact</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Contact Information -->
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-person-lines-fill text-primary me-2"></i>
                            Contact Information
                        </h5>

                        @if($user->contact_info ?? null)
                            @if($user->contact_info['email'] ?? null)
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-envelope text-muted me-2"></i>
                                    <a href="mailto:{{ $user->contact_info['email'] }}" class="text-decoration-none">
                                        {{ $user->contact_info['email'] }}
                                    </a>
                                </div>
                            @endif

                            @if($user->contact_info['phone'] ?? null)
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-telephone text-muted me-2"></i>
                                    <a href="tel:{{ $user->contact_info['phone'] }}" class="text-decoration-none">
                                        {{ $user->contact_info['phone'] }}
                                    </a>
                                </div>
                            @endif

                            @if($user->contact_info['linkedin'] ?? null)
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-linkedin text-muted me-2"></i>
                                    <a href="{{ $user->contact_info['linkedin'] }}" target="_blank" class="text-decoration-none">
                                        LinkedIn Profile
                                    </a>
                                </div>
                            @endif

                            @if($user->contact_info['website'] ?? null)
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-globe text-muted me-2"></i>
                                    <a href="{{ $user->contact_info['website'] }}" target="_blank" class="text-decoration-none">
                                        Personal Website
                                    </a>
                                </div>
                            @endif
                        @endif

                        <hr class="my-3">

                        <div class="d-grid gap-2">
                            <a href="{{ route('contact.show') }}" class="btn btn-primary">
                                <i class="bi bi-envelope me-1"></i> Send Message
                            </a>
                            <a href="{{ route('download-cv') }}" class="btn btn-outline-primary">
                                <i class="bi bi-download me-1"></i> Download CV
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-graph-up text-primary me-2"></i>
                            Quick Stats
                        </h5>

                        <div class="row g-3 text-center">
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <h4 class="text-primary mb-1">{{ $stats['courses'] ?? 0 }}</h4>
                                    <small class="text-muted">Courses</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <h4 class="text-primary mb-1">{{ $stats['projects'] ?? 0 }}</h4>
                                    <small class="text-muted">Projects</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <h4 class="text-primary mb-1">{{ $stats['publications'] ?? 0 }}</h4>
                                    <small class="text-muted">Publications</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <h4 class="text-primary mb-1">{{ $stats['blog_posts'] ?? 0 }}</h4>
                                    <small class="text-muted">Articles</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-clock-history text-primary me-2"></i>
                            Recent Activity
                        </h5>

                        @if($recentActivity ?? null)
                            @foreach($recentActivity as $activity)
                                <div class="d-flex align-items-start mb-3">
                                    <div class="flex-shrink-0">
                                        @if($activity['type'] === 'course')
                                            <i class="bi bi-book text-primary"></i>
                                        @elseif($activity['type'] === 'project')
                                            <i class="bi bi-code-slash text-success"></i>
                                        @elseif($activity['type'] === 'publication')
                                            <i class="bi bi-journal-text text-info"></i>
                                        @else
                                            <i class="bi bi-pencil-square text-warning"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 fs-6">{{ $activity['title'] }}</h6>
                                        <small class="text-muted">{{ $activity['date'] }}</small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-3">
                                <i class="bi bi-clock text-muted mb-2" style="font-size: 2rem;"></i>
                                <p class="text-muted mb-0">Recent activities will appear here.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
    .bio-content {
        line-height: 1.7;
        font-size: 1.1rem;
    }

    .card {
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .border-start {
        border-left-width: 4px !important;
    }
</style>
@endsection