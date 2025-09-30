@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">Dr. Sarah Johnson</h1>
                <h2 class="h3 mb-4">Professor of Computer Science & Artificial Intelligence</h2>
                <p class="lead mb-4">Dedicated to advancing AI research and educating the next generation of computer scientists.</p>
                <a href="#contact" class="btn btn-primary me-3">Get In Touch</a>
                <a href="#research" class="btn btn-outline-light">View Research</a>
            </div>
            <div class="col-lg-4 text-center">
                @if(isset($teacher) && $teacher->avatar)
                    <img src="{{ Storage::url($teacher->avatar) }}" alt="Dr. Sarah Johnson" class="img-fluid rounded-circle profile-img">
                @else
                    <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" alt="Dr. Sarah Johnson" class="img-fluid rounded-circle profile-img">
                @endif
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="section-title">About Me</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <p class="lead">I am a Professor of Computer Science specializing in Artificial Intelligence and Machine Learning at Stanford University.</p>
                <p>{{ $teacher->bio ?? 'With over 15 years of academic experience, my research focuses on developing ethical AI systems, natural language processing, and computer vision applications. I\'m passionate about bridging the gap between theoretical computer science and practical applications that benefit society.' }}</p>
                <p>My teaching philosophy centers on creating an inclusive learning environment where students can develop critical thinking skills and apply theoretical knowledge to real-world problems.</p>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <h4>Education</h4>
                        <ul>
                            <li>Ph.D. in Computer Science, MIT (2005)</li>
                            <li>M.S. in Computer Science, Stanford University (2001)</li>
                            <li>B.S. in Computer Engineering, UC Berkeley (1999)</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h4>Awards & Honors</h4>
                        <ul>
                            <li>ACM Fellow (2020)</li>
                            <li>National Science Foundation Career Award (2012)</li>
                            <li>Best Paper Award, AAAI Conference (2018)</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card p-4">
                    <h4>Research Interests</h4>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-chevron-right text-primary me-2"></i> Machine Learning</li>
                        <li><i class="fas fa-chevron-right text-primary me-2"></i> Natural Language Processing</li>
                        <li><i class="fas fa-chevron-right text-primary me-2"></i> Computer Vision</li>
                        <li><i class="fas fa-chevron-right text-primary me-2"></i> Ethical AI</li>
                        <li><i class="fas fa-chevron-right text-primary me-2"></i> Human-Computer Interaction</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Research Section -->
<section id="research" class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="section-title">Research Areas</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100 p-4 text-center">
                    <div class="card-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h4>Machine Learning</h4>
                    <p>Developing novel algorithms for supervised and unsupervised learning with applications in healthcare and education.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 p-4 text-center">
                    <div class="card-icon">
                        <i class="fas fa-language"></i>
                    </div>
                    <h4>Natural Language Processing</h4>
                    <p>Research on sentiment analysis, text generation, and multilingual models for improved human-computer communication.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 p-4 text-center">
                    <div class="card-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h4>Computer Vision</h4>
                    <p>Exploring deep learning approaches for object recognition, image segmentation, and medical imaging analysis.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Teaching Section -->
<section id="teaching" class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="section-title">Teaching</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <h4>Current Courses</h4>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5>CS229: Machine Learning</h5>
                        <p class="mb-1"><strong>Level:</strong> Graduate</p>
                        <p class="mb-1"><strong>Schedule:</strong> Fall 2023</p>
                        <p>This course provides a broad introduction to machine learning and statistical pattern recognition.</p>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5>CS231N: Deep Learning for Computer Vision</h5>
                        <p class="mb-1"><strong>Level:</strong> Graduate</p>
                        <p class="mb-1"><strong>Schedule:</strong> Spring 2024</p>
                        <p>This course is a deep dive into details of neural network architectures with a focus on learning end-to-end models for vision tasks.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <h4>Teaching Philosophy</h4>
                <p>I believe that education should be accessible, engaging, and transformative. My approach to teaching includes:</p>
                <ul>
                    <li>Creating inclusive learning environments where all students feel valued</li>
                    <li>Connecting theoretical concepts to real-world applications</li>
                    <li>Encouraging collaborative problem-solving and critical thinking</li>
                    <li>Providing timely and constructive feedback</li>
                    <li>Adapting teaching methods to diverse learning styles</li>
                </ul>
                <div class="mt-4">
                    <a href="{{ route('courses.index') }}" class="btn btn-primary me-2">Course Materials</a>
                    <a href="#" class="btn btn-outline-primary">Student Resources</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Publications Section -->
<section id="publications" class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="section-title">Selected Publications</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <div class="publication-item">
                    <h5>Ethical Considerations in AI Systems</h5>
                    <p class="text-muted">Johnson, S., Zhang, W., & Lee, M. (2022). Journal of Artificial Intelligence Research, 74, 153-190.</p>
                    <a href="{{ route('publications.index') }}" class="btn btn-sm btn-outline-primary">Read Paper</a>
                </div>
                <div class="publication-item">
                    <h5>Transformer Models for Multilingual Text Classification</h5>
                    <p class="text-muted">Johnson, S., & Chen, L. (2021). Proceedings of the 2021 Conference on Empirical Methods in Natural Language Processing.</p>
                    <a href="{{ route('publications.index') }}" class="btn btn-sm btn-outline-primary">Read Paper</a>
                </div>
                <div class="publication-item">
                    <h5>Advancements in Few-Shot Learning for Computer Vision</h5>
                    <p class="text-muted">Patel, R., Johnson, S., & Williams, K. (2020). IEEE Transactions on Pattern Analysis and Machine Intelligence, 42(5), 1156-1170.</p>
                    <a href="{{ route('publications.index') }}" class="btn btn-sm btn-outline-primary">Read Paper</a>
                </div>
                <div class="publication-item">
                    <h5>Human-Centered AI: Designing for User Trust and Understanding</h5>
                    <p class="text-muted">Johnson, S. (2019). ACM Transactions on Interactive Intelligent Systems, 9(4), 1-35.</p>
                    <a href="{{ route('publications.index') }}" class="btn btn-sm btn-outline-primary">Read Paper</a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card p-4">
                    <h4>Research Lab</h4>
                    <p>I direct the <strong>Intelligent Systems Lab</strong> at Stanford University, where we explore cutting-edge AI research with practical applications.</p>
                    <p>Our lab welcomes graduate students and postdoctoral researchers interested in AI ethics, NLP, and computer vision.</p>
                    <a href="{{ route('projects.index') }}" class="btn btn-primary">Lab Website</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="section-title">Contact</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <h4>Get In Touch</h4>
                <p>I welcome inquiries from students, collaborators, and anyone interested in my research or teaching.</p>
                <ul class="contact-info">
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Office Location</strong><br>
                            Gates Computer Science Building, Room 392<br>
                            Stanford University
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>Email</strong><br>
                            sarah.johnson@stanford.edu
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-phone"></i>
                        <div>
                            <strong>Phone</strong><br>
                            +1 (650) 123-4567
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Office Hours</strong><br>
                            Tuesdays & Thursdays, 2:00 PM - 4:00 PM<br>
                            or by appointment
                        </div>
                    </li>
                </ul>
            </div>
            <div class="col-lg-6">
                <h4>Send a Message</h4>

                <!-- Display success/error messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Please correct the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('contact.store') }}" method="POST">
                    @csrf
                    <!-- Honeypot field for spam protection (hidden from users) -->
                    <input type="text" name="website" value="" style="position: absolute; left: -9999px;" tabindex="-1" autocomplete="off">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
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
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
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
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('subject') is-invalid @enderror"
                               id="subject"
                               name="subject"
                               value="{{ old('subject') }}"
                               required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('message') is-invalid @enderror"
                                  id="message"
                                  name="message"
                                  rows="5"
                                  required>{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="btn-text">
                            <i class="fas fa-paper-plane me-2"></i>Send Message
                        </span>
                        <span class="btn-loading d-none">
                            <span class="spinner-border spinner-border-sm me-2"></span>Sending...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced contact form submission
    const contactForm = document.querySelector('form[action="{{ route('contact.store') }}"]');
    const submitBtn = document.getElementById('submitBtn');

    if (contactForm && submitBtn) {
        contactForm.addEventListener('submit', function() {
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.querySelector('.btn-text').classList.add('d-none');
            submitBtn.querySelector('.btn-loading').classList.remove('d-none');
        });
    }

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-danger)');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Smooth scroll to contact form if there are validation errors
    @if($errors->any())
        document.querySelector('#contact').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    @endif

    // Character count for message textarea
    const messageTextarea = document.getElementById('message');
    if (messageTextarea) {
        const maxLength = 5000;
        const charCount = document.createElement('small');
        charCount.className = 'text-muted';
        charCount.style.float = 'right';
        messageTextarea.parentNode.appendChild(charCount);

        function updateCharCount() {
            const remaining = maxLength - messageTextarea.value.length;
            charCount.textContent = `${messageTextarea.value.length}/${maxLength} characters`;
            charCount.className = remaining < 100 ? 'text-warning' : 'text-muted';
        }

        messageTextarea.addEventListener('input', updateCharCount);
        updateCharCount(); // Initial count
    }
});
</script>
@endsection