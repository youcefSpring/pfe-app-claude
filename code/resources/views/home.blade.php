@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">{{ $teacher->name ?? 'Dr. Benabderrezak Youcef' }}</h1>
                    <h2 class="h3 mb-4">{{ $teacher->title ?? 'Reasearcher in cyber security & Web Developer' }}</h2>
                    <p class="lead mb-4">{{ $teacher->bio ?? 'Dedicated to advancing AI research and educating the next generation of computer scientists.' }}</p>
                    <a href="#contact" class="btn btn-primary me-3">Get In Touch</a>
                    <a href="#research" class="btn btn-outline-light me-3">View Research</a>
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-success">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-warning">
                            <i class="fas fa-sign-in-alt me-2"></i>Admin Login
                        </a>
                    @endauth
                </div>
                <div class="col-lg-4 text-center">
                    @if(isset($teacher) && $teacher->avatar)
                        <img src="{{ Storage::url($teacher->avatar) }}" alt="{{ $teacher->name ?? 'Dr. Benabderrezak Youcef' }}" class="img-fluid rounded-circle profile-img">
                    @else
                        <img src="data:image/jpeg;base64,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" alt="{{ $teacher->name ?? 'Dr. Benabderrezak Youcef' }}" class="img-fluid rounded-circle profile-img">
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
                    <p class="lead">{{ $teacher->title ?? 'I am a Professor of Computer Science specializing in Artificial Intelligence and Machine Learning at Stanford University.' }}</p>
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
                            <li><i class="fas fa-chevron-right text-primary me-2"></i> Cyber Security</li>
                            <li><i class="fas fa-chevron-right text-primary me-2"></i> UML</li>
                            <li><i class="fas fa-chevron-right text-primary me-2"></i> Web development</li>
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
                        <h4>Cyber Security & Machine Learning</h4>
                        <p>Developing novel algorithms for supervised and unsupervised learning with applications in Cyber Security</p>
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
                        <a href="#" class="btn btn-primary me-2">Course Materials</a>
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
                        <a href="#" class="btn btn-sm btn-outline-primary">Read Paper</a>
                    </div>
                    <div class="publication-item">
                        <h5>Transformer Models for Multilingual Text Classification</h5>
                        <p class="text-muted">Johnson, S., & Chen, L. (2021). Proceedings of the 2021 Conference on Empirical Methods in Natural Language Processing.</p>
                        <a href="#" class="btn btn-sm btn-outline-primary">Read Paper</a>
                    </div>
                    <div class="publication-item">
                        <h5>Advancements in Few-Shot Learning for Computer Vision</h5>
                        <p class="text-muted">Patel, R., Johnson, S., & Williams, K. (2020). IEEE Transactions on Pattern Analysis and Machine Intelligence, 42(5), 1156-1170.</p>
                        <a href="#" class="btn btn-sm btn-outline-primary">Read Paper</a>
                    </div>
                    <div class="publication-item">
                        <h5>Human-Centered AI: Designing for User Trust and Understanding</h5>
                        <p class="text-muted">Johnson, S. (2019). ACM Transactions on Interactive Intelligent Systems, 9(4), 1-35.</p>
                        <a href="#" class="btn btn-sm btn-outline-primary">Read Paper</a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card p-4">
                        <h4>Research Lab</h4>
                        <p>I direct the <strong>Intelligent Systems Lab</strong> at Stanford University, where we explore cutting-edge AI research with practical applications.</p>
                        <p>Our lab welcomes graduate students and postdoctoral researchers interested in AI ethics, NLP, and computer vision.</p>
                        <a href="#" class="btn btn-primary">Lab Website</a>
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
