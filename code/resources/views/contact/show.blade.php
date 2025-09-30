@extends('layouts.app')

@section('title', 'Contact')

@section('content')
<!-- Header Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold">Get In Touch</h1>
                <p class="lead mb-0">Have a question or want to collaborate? I'd love to hear from you.</p>
            </div>
            <div class="col-lg-4 text-center">
                <i class="bi bi-envelope" style="font-size: 6rem; opacity: 0.7;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Contact Content -->
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Contact Form -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body p-4">
                        <h2 class="h4 mb-4">
                            <i class="bi bi-send text-primary me-2"></i>
                            Send Message
                        </h2>

                        <form method="POST" action="{{ route('contact.store') }}" id="contactForm">
                            @csrf

                            <!-- Honeypot field for spam protection -->
                            <input type="text" name="website" style="display: none;" tabindex="-1" autocomplete="off">

                            <div class="row g-3">
                                <!-- Name -->
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name') }}"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Subject -->
                                <div class="col-12">
                                    <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                    <select class="form-select @error('subject') is-invalid @enderror"
                                            id="subject"
                                            name="subject"
                                            required>
                                        <option value="">Choose a subject...</option>
                                        <option value="general_inquiry" {{ old('subject') == 'general_inquiry' ? 'selected' : '' }}>General Inquiry</option>
                                        <option value="collaboration" {{ old('subject') == 'collaboration' ? 'selected' : '' }}>Collaboration Opportunity</option>
                                        <option value="course_question" {{ old('subject') == 'course_question' ? 'selected' : '' }}>Course Question</option>
                                        <option value="research_inquiry" {{ old('subject') == 'research_inquiry' ? 'selected' : '' }}>Research Inquiry</option>
                                        <option value="speaking_engagement" {{ old('subject') == 'speaking_engagement' ? 'selected' : '' }}>Speaking Engagement</option>
                                        <option value="other" {{ old('subject') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Message -->
                                <div class="col-12">
                                    <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('message') is-invalid @enderror"
                                              id="message"
                                              name="message"
                                              rows="6"
                                              placeholder="Please describe your inquiry in detail..."
                                              required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Please be as specific as possible to help me provide the best response.
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <span class="text-danger">*</span> Required fields
                                        </small>
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="bi bi-send me-2"></i>
                                            Send Message
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-person-lines-fill text-primary me-2"></i>
                            Contact Information
                        </h5>

                        <div class="mb-4">
                            <div class="d-flex align-items-start mb-3">
                                <i class="bi bi-envelope text-primary me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">Email</h6>
                                    <p class="text-muted mb-0">
                                        <a href="mailto:contact@example.com" class="text-decoration-none">
                                            contact@example.com
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start mb-3">
                                <i class="bi bi-clock text-primary me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">Response Time</h6>
                                    <p class="text-muted mb-0">Usually within 24-48 hours</p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start">
                                <i class="bi bi-calendar-check text-primary me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">Office Hours</h6>
                                    <p class="text-muted mb-0">
                                        Monday - Friday<br>
                                        9:00 AM - 5:00 PM
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('download-cv') }}" class="btn btn-outline-primary">
                                <i class="bi bi-download me-1"></i>
                                Download CV
                            </a>
                            <a href="{{ route('about') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-person me-1"></i>
                                About Me
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-link-45deg text-primary me-2"></i>
                            Quick Links
                        </h5>

                        <div class="list-group list-group-flush">
                            <a href="{{ route('courses.index') }}" class="list-group-item list-group-item-action border-0 px-0">
                                <i class="bi bi-book me-2"></i>View Courses
                            </a>
                            <a href="{{ route('projects.index') }}" class="list-group-item list-group-item-action border-0 px-0">
                                <i class="bi bi-code-slash me-2"></i>Browse Projects
                            </a>
                            <a href="{{ route('publications.index') }}" class="list-group-item list-group-item-action border-0 px-0">
                                <i class="bi bi-journal-text me-2"></i>Read Publications
                            </a>
                            <a href="{{ route('blog.index') }}" class="list-group-item list-group-item-action border-0 px-0">
                                <i class="bi bi-pencil-square me-2"></i>Blog Articles
                            </a>
                        </div>
                    </div>
                </div>

                <!-- FAQ -->
                <div class="card">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-question-circle text-primary me-2"></i>
                            Common Questions
                        </h5>

                        <div class="accordion accordion-flush" id="faqAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        How quickly do you respond?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        I typically respond to all inquiries within 24-48 hours during business days.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        Do you offer consultations?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Yes, I'm available for academic consultations and project collaborations. Please describe your needs in detail.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        Can students contact you?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Absolutely! I welcome questions from students about courses, research opportunities, or academic guidance.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
    .card {
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
    }

    .accordion-button:focus {
        box-shadow: none;
        border-color: #e5e7eb;
    }

    .accordion-button:not(.collapsed) {
        background-color: #f8fafc;
        color: #2563eb;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('contactForm');

        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Sending...';
            submitBtn.disabled = true;

            // Re-enable button after 5 seconds as fallback
            setTimeout(function() {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
        });

        // Character counter for message field
        const messageField = document.getElementById('message');
        if (messageField) {
            const counter = document.createElement('div');
            counter.className = 'form-text text-end mt-1';
            counter.id = 'messageCounter';
            messageField.parentNode.appendChild(counter);

            function updateCounter() {
                const length = messageField.value.length;
                counter.textContent = `${length}/2000 characters`;

                if (length > 1800) {
                    counter.classList.add('text-warning');
                } else {
                    counter.classList.remove('text-warning');
                }
            }

            messageField.addEventListener('input', updateCounter);
            updateCounter(); // Initial count
        }
    });
</script>
@endsection