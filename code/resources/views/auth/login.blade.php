@extends('layouts.pfe-app')

@section('title', __('app.login'))

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<section class="py-5 bg-light min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body p-5">
                        <!-- Top Navigation Bar -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <i class="bi bi-mortarboard text-primary" style="font-size: 2rem;"></i>
                            </div>
                            
                            <div class="d-flex align-items-center" style="gap: 0.25rem;">
                                <!-- Custom Language Switcher for Login Page -->
                               @include('partials.language-switcher')

                                <!-- Dark Mode Toggle -->
                                <button class="btn btn-outline-secondary dark-mode-toggle" type="button" id="darkModeToggle" title="Toggle Dark Mode">
                                    <i class="bi bi-moon-stars" id="darkModeIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Header -->
                        <div class="text-center mb-4">
                            <h2 class="h3 mb-3">{{ __('app.welcome_back') }}</h2>
                            <p class="text-muted">{{ __('app.sign_in_message') }}</p>
                        </div>

                        <!-- Login Form -->
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('app.email_address') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input id="email"
                                           type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           name="email"
                                           value="{{ old('email') }}"
                                           required
                                           autocomplete="email"
                                           autofocus
                                           placeholder="{{ __('app.enter_email') }}">
                                </div>
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('app.password') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                     <input id="password"
                                            type="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            name="password"
                                            required
                                            autocomplete="current-password"
                                            placeholder="{{ __('app.enter_password') }}">
                                     <button class="btn btn-outline-secondary" type="button" 
                                     onclick="  const p = document.getElementById('password');
                                     p.type = (p.type === 'password') ? 'text' : 'password';
                                     "
                                     >
                                         <i class="bi bi-eye" id="passwordToggleIcon"></i>
                                     </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Remember Me -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="remember"
                                           id="remember"
                                           {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('app.remember_me') }}
                                    </label>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>
                                    {{ __('app.sign_in') }}
                                </button>
                            </div>

                            <!-- Forgot Password Link -->
                            <div class="text-center">
                                <a href="#" class="text-decoration-none text-muted small">
                                    {{ __('app.forgot_password') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Quick Test Login -->
                @if(app()->environment(['local', 'development', 'testing']))
                <div class="card mt-4 bg-info bg-opacity-10 border-info">
                    <div class="card-body p-3">
                        <h6 class="card-title text-info mb-3">
                            <i class="bi bi-flask me-2"></i>{{ __('app.quick_test_login') }}
                        </h6>

                        <!-- User Type Selector -->
                        <div class="mb-3">
                            <label for="userTypeSelector" class="form-label text-muted small">{{ __('app.choose_user_type') }}:</label>
                            <select id="userTypeSelector" class="form-select form-select-sm" onchange="fillLoginByUserType()">
                                <option value="">{{ __('app.select_user_type') }}</option>
                                <option value="admin">👤 {{ __('app.administrator') }}</option>
                                <option value="teacher">🎓 {{ __('app.teacher') }}</option>
                                <option value="student">📚 {{ __('app.student') }}</option>
                            </select>
                        </div>

                        <!-- Available Test Credentials -->
                        <div class="credentials-display">
                            <h6 class="text-muted small mb-2">
                                <i class="bi bi-key me-1"></i>{{ __('app.available_test_credentials') }}:
                            </h6>
                            <div class="credential-item mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-2">👤</span>
                                    <div class="credential-text">
                                        <strong>{{ __('app.administrator') }}:</strong><br>
                                        <code class="text-primary">admin@university.edu</code> / <code class="text-primary">password</code>
                                    </div>
                                </div>
                            </div>
                            <div class="credential-item mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-success me-2">🎓</span>
                                    <div class="credential-text">
                                        <strong>{{ __('app.teacher') }}:</strong><br>
                                        <code class="text-success">ahmed.hassan@university.edu</code> / <code class="text-success">password</code>
                                    </div>
                                </div>
                            </div>
                            <div class="credential-item mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-warning me-2">📚</span>
                                    <div class="credential-text">
                                        <strong>{{ __('app.student') }}:</strong><br>
                                        <code class="text-warning">alice.dubois@student.university.edu</code> / <code class="text-warning">password</code>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <small class="text-muted mt-2 d-block">
                            <i class="bi bi-info-circle me-1"></i>{{ __('app.dev_mode_notice') }}
                        </small>
                    </div>
                </div>
                @endif

                <!-- Help Text -->
                <div class="text-center mt-4">
                    <small class="text-muted">
                        {{ __('app.need_help') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
    .min-vh-100 {
        min-height: 100vh;
    }

    .card {
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
    }

    .input-group-text {
        background-color: #f8fafc;
        border-right: none;
        color: #6b7280;
    }

    .form-control {
        border-left: none;
    }

    .form-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        font-weight: 500;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        transform: translateY(-1px);
    }

    .btn-outline-secondary {
        border-left: none;
    }

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }

    /* User Type Selector Styling */
    #userTypeSelector {
        border: 2px solid #e3f2fd;
        border-radius: 8px;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        transition: all 0.3s ease;
    }

    #userTypeSelector:focus {
        border-color: #2196f3;
        box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25);
        background: #ffffff;
    }

    #userTypeSelector option {
        padding: 8px;
        background: #ffffff;
    }

    /* Credentials Display Styling */
    .credentials-display {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-radius: 8px;
        padding: 12px;
        border: 1px solid #e2e8f0;
    }

    .credential-item {
        background: rgba(255, 255, 255, 0.7);
        border-radius: 6px;
        padding: 8px;
        border-left: 3px solid transparent;
        transition: all 0.2s ease;
    }

    .credential-item:hover {
        background: rgba(255, 255, 255, 0.9);
        transform: translateX(2px);
    }

    .credential-item:nth-child(2) { border-left-color: #007bff; }
    .credential-item:nth-child(3) { border-left-color: #28a745; }
    .credential-item:nth-child(4) { border-left-color: #ffc107; }

    .credential-text {
        font-size: 0.85rem;
        line-height: 1.3;
    }

    .credential-text strong {
        color: #495057;
        font-size: 0.9rem;
    }

    .credential-text code {
        font-size: 0.8rem;
        padding: 2px 4px;
        border-radius: 3px;
        background: rgba(255, 255, 255, 0.8);
        font-weight: 500;
    }

    /* Dark mode toggle button styling */
    .dark-mode-toggle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        background: transparent;
        padding: 0;
    }

    .dark-mode-toggle:hover {
        background: rgba(0, 0, 0, 0.05);
    }

    [data-bs-theme="dark"] .dark-mode-toggle:hover {
        background: rgba(255, 255, 255, 0.1);
    }
</style>
@endsection

@section('scripts')
<script>
    // Define user credentials for each role
    const userCredentials = {
        admin: {
            email: 'admin@university.edu',
            password: 'password',
            name: 'System Administrator'
        },
        teacher: {
            email: 'ahmed.hassan@university.edu',
            password: 'password',
            name: 'Prof. Ahmed Hassan'
        },
        student: {
            email: 'alice.dubois@student.university.edu',
            password: 'password',
            name: 'Alice Dubois'
        }
    };

    // Fill login by user type selection
    window.fillLoginByUserType = function() {
        const selector = document.getElementById('userTypeSelector');
        const selectedType = selector.value;

        if (!selectedType) {
            // Clear form if no selection
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            emailInput.value = '';
            passwordInput.value = '';
            return;
        }

        const credentials = userCredentials[selectedType];
        if (credentials) {
            fillLogin(credentials.email, credentials.password);
            console.log(`Filled login for ${credentials.name} (${selectedType})`);
        }
    };

    // Define fillLogin function globally before DOM ready
    window.fillLogin = function(email, password) {
        console.log('fillLogin called with:', email, password);

        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');

        if (!emailInput || !passwordInput) {
            console.error('Email or password input not found');
            return;
        }

        // Clear any existing values
        emailInput.value = '';
        passwordInput.value = '';

        // Set new values
        emailInput.value = email;
        passwordInput.value = password;

        // Trigger input events to ensure any validation or listeners are notified
        emailInput.dispatchEvent(new Event('input', { bubbles: true }));
        passwordInput.dispatchEvent(new Event('input', { bubbles: true }));

        // Add visual feedback
        emailInput.classList.remove('is-invalid');
        passwordInput.classList.remove('is-invalid');
        emailInput.classList.add('is-valid');
        passwordInput.classList.add('is-valid');

        // Focus on email field
        emailInput.focus();
        emailInput.blur();

        setTimeout(function() {
            emailInput.classList.remove('is-valid');
            passwordInput.classList.remove('is-valid');
        }, 1500);

        console.log('Form filled successfully');
    };

    // Function to change language via AJAX - in global scope to avoid conflicts
    function changeLanguage(locale) {
        console.log('changeLanguage function called with locale:', locale);
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        const tokenValue = csrfToken ? csrfToken.getAttribute('content') : null;
        
        fetch(`/language/${locale}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': tokenValue,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(function(response) {
            console.log('Language change response:', response.status);
            if (response.ok) {
                console.log('Language change successful, reloading page');
                // Reload the page to apply new language translations
                location.reload();
            } else {
                console.error('Language change failed:', response.status);
            }
        })
        .catch(function(error) {
            console.error('Error changing language:', error);
        });
    }

    console.log('Login page script starting...');

    // Password toggle function
    window.togglePassword = function() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('passwordToggleIcon');
        
        if (passwordInput && toggleIcon) {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }
    };

    // DOM ready check for login page features
    if (document.readyState === 'loading') {
        // Loading hasn't finished yet
        document.addEventListener('DOMContentLoaded', initializeLoginPageFeatures);
    } else {
        // DOM is already loaded
        initializeLoginPageFeatures();
    }

    function initializeLoginPageFeatures() {
        console.log('Initializing login page features...');
        
        // Add event listeners to language dropdown items
        const languageLinks = document.querySelectorAll('#languageDropdownMenu a[data-locale]');
        console.log('Found', languageLinks.length, 'language links');
        
        if (languageLinks.length > 0) {
            languageLinks.forEach(function(link, index) {
                console.log('Adding event listener to language link', index, link.getAttribute('data-locale'));
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Language link clicked, locale:', this.getAttribute('data-locale'));
                    const locale = this.getAttribute('data-locale');
                    changeLanguage(locale);
                });
            });
        } else {
            console.log('No language links found with selector #languageDropdownMenu a[data-locale]');
        }
        
        // Form validation feedback
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function() {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    // Use static text to avoid Blade syntax issues
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Signing in...';
                    submitBtn.disabled = true;
                }
            });
        }

        // Update dark mode icon after the main layout scripts have run
        setTimeout(function() {
            const darkModeIcon = document.getElementById('darkModeIcon');
            const htmlElement = document.documentElement;
            if (darkModeIcon) {
                const currentTheme = htmlElement.getAttribute('data-bs-theme');
                if (currentTheme === 'dark') {
                    darkModeIcon.className = 'bi bi-sun-fill';
                } else {
                    darkModeIcon.className = 'bi bi-moon-stars';
                }
            }
        }, 100); // Small delay to ensure main layout script runs first
        
        console.log('DOM Content Loaded function completed');
    }
    
    console.log('Main script setup completed');
</script>
@endsection