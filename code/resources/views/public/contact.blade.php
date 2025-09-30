@extends('layouts.app')

@section('title', 'Contact Me')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">Get In Touch</h1>
                <p class="lead mb-4">
                    I welcome collaboration opportunities, academic discussions, and inquiries from students and colleagues.
                    Whether you're interested in my research, courses, or potential partnerships, I'd love to hear from you.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form and Info -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row">
            <!-- Contact Form -->
            <div class="col-lg-8 mb-5">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-envelope-fill me-2"></i>Send Me a Message</h4>
                    </div>
                    <div class="card-body p-4">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('contact.store') }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                           id="phone" name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="subject_type" class="form-label">Subject Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('subject_type') is-invalid @enderror"
                                            id="subject_type" name="subject_type" required>
                                        <option value="">Select a subject type</option>
                                        <option value="general_inquiry" {{ old('subject_type') == 'general_inquiry' ? 'selected' : '' }}>General Inquiry</option>
                                        <option value="collaboration" {{ old('subject_type') == 'collaboration' ? 'selected' : '' }}>Research Collaboration</option>
                                        <option value="course_info" {{ old('subject_type') == 'course_info' ? 'selected' : '' }}>Course Information</option>
                                        <option value="student_inquiry" {{ old('subject_type') == 'student_inquiry' ? 'selected' : '' }}>Student Inquiry</option>
                                        <option value="media_interview" {{ old('subject_type') == 'media_interview' ? 'selected' : '' }}>Media/Interview Request</option>
                                        <option value="speaking_engagement" {{ old('subject_type') == 'speaking_engagement' ? 'selected' : '' }}>Speaking Engagement</option>
                                        <option value="other" {{ old('subject_type') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('subject_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('subject') is-invalid @enderror"
                                       id="subject" name="subject" value="{{ old('subject') }}" required>
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('message') is-invalid @enderror"
                                          id="message" name="message" rows="6" required
                                          placeholder="Please provide details about your inquiry...">{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-send me-2"></i>Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="col-lg-4">
                <!-- Direct Contact -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Contact Information</h5>
                    </div>
                    <div class="card-body">
                        @if($teacher->email ?? false)
                            <div class="mb-3">
                                <h6><i class="bi bi-envelope text-primary me-2"></i>Email</h6>
                                <a href="mailto:{{ $teacher->email }}" class="text-decoration-none">
                                    {{ $teacher->email }}
                                </a>
                            </div>
                        @endif

                        @if($teacher->phone ?? false)
                            <div class="mb-3">
                                <h6><i class="bi bi-telephone text-primary me-2"></i>Phone</h6>
                                <a href="tel:{{ $teacher->phone }}" class="text-decoration-none">
                                    {{ $teacher->phone }}
                                </a>
                            </div>
                        @endif

                        @if($teacher->office_location ?? false)
                            <div class="mb-3">
                                <h6><i class="bi bi-geo-alt text-primary me-2"></i>Office</h6>
                                <p class="mb-0">{{ $teacher->office_location }}</p>
                            </div>
                        @endif

                        @if($teacher->office_hours ?? false)
                            <div class="mb-3">
                                <h6><i class="bi bi-clock text-primary me-2"></i>Office Hours</h6>
                                <p class="mb-0">{!! nl2br(e($teacher->office_hours)) !!}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Response Time -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-clock-fill me-2"></i>Response Time</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <strong>General Inquiries:</strong> 1-2 business days
                        </p>
                        <p class="mb-3">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <strong>Student Questions:</strong> Same day (during business hours)
                        </p>
                        <p class="mb-0">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <strong>Research Collaborations:</strong> 2-3 business days
                        </p>
                    </div>
                </div>

                <!-- Social Links -->
                @if(($teacher->website ?? false) || ($teacher->linkedin_url ?? false) || ($teacher->twitter_url ?? false))
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0"><i class="bi bi-share me-2"></i>Connect With Me</h5>
                    </div>
                    <div class="card-body">
                        @if($teacher->website ?? false)
                            <div class="mb-2">
                                <a href="{{ $teacher->website }}" target="_blank" rel="noopener"
                                   class="text-decoration-none">
                                    <i class="bi bi-globe text-primary me-2"></i>Personal Website
                                </a>
                            </div>
                        @endif

                        @if($teacher->linkedin_url ?? false)
                            <div class="mb-2">
                                <a href="{{ $teacher->linkedin_url }}" target="_blank" rel="noopener"
                                   class="text-decoration-none">
                                    <i class="bi bi-linkedin text-primary me-2"></i>LinkedIn
                                </a>
                            </div>
                        @endif

                        @if($teacher->twitter_url ?? false)
                            <div class="mb-2">
                                <a href="{{ $teacher->twitter_url }}" target="_blank" rel="noopener"
                                   class="text-decoration-none">
                                    <i class="bi bi-twitter text-primary me-2"></i>Twitter
                                </a>
                            </div>
                        @endif

                        @if($teacher->google_scholar_url ?? false)
                            <div class="mb-2">
                                <a href="{{ $teacher->google_scholar_url }}" target="_blank" rel="noopener"
                                   class="text-decoration-none">
                                    <i class="bi bi-mortarboard text-primary me-2"></i>Google Scholar
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2>Frequently Asked Questions</h2>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faq1">
                                How quickly do you respond to emails?
                            </button>
                        </h3>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                I aim to respond to all emails within 1-2 business days. Student inquiries during
                                office hours typically receive same-day responses. For urgent matters, please
                                indicate this in your subject line.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faq2">
                                Do you accept research collaboration proposals?
                            </button>
                        </h3>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes, I'm always interested in meaningful research collaborations. Please include
                                details about the proposed project, timeline, and how it aligns with my research
                                interests when you contact me.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faq3">
                                Are you available for speaking engagements?
                            </button>
                        </h3>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                I regularly accept speaking invitations for conferences, seminars, and academic
                                events. Please provide details about the event, audience, and preferred topics
                                when reaching out.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faq4">
                                Can I schedule a meeting outside of office hours?
                            </button>
                        </h3>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Absolutely! While I maintain regular office hours, I'm happy to schedule
                                appointments at mutually convenient times. Please suggest a few preferred
                                time slots in your message.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection