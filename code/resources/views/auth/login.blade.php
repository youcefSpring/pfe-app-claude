@extends('layouts.pfe-app')

@section('title', __('app.login'))

@section('content')
<section class="py-5 bg-light min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body p-5">
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <i class="bi bi-mortarboard text-primary mb-3" style="font-size: 3rem;"></i>
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
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
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

                <!-- Quick Test Login Buttons -->
                @if(app()->environment(['local', 'development', 'testing']))
                <div class="card mt-4 bg-info bg-opacity-10 border-info">
                    <div class="card-body p-3">
                        <h6 class="card-title text-info mb-3">
                            <i class="bi bi-flask me-2"></i>{{ __('app.quick_test_login') }}
                        </h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="fillLogin('admin@example.com', 'password')">
                                    <i class="bi bi-shield-check me-1"></i>Admin
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-success btn-sm w-100" onclick="fillLogin('teacher@example.com', 'password')">
                                    <i class="bi bi-person-check me-1"></i>Teacher
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-warning btn-sm w-100" onclick="fillLogin('student@example.com', 'password')">
                                    <i class="bi bi-mortarboard me-1"></i>Student
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-info btn-sm w-100" onclick="fillLogin('head@example.com', 'password')">
                                    <i class="bi bi-person-gear me-1"></i>Dept Head
                                </button>
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
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePasswordIcon');

        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                if (type === 'text') {
                    toggleIcon.classList.remove('bi-eye');
                    toggleIcon.classList.add('bi-eye-slash');
                } else {
                    toggleIcon.classList.remove('bi-eye-slash');
                    toggleIcon.classList.add('bi-eye');
                }
            });
        }

        // Form validation feedback
        const form = document.querySelector('form');
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Signing In...';
            submitBtn.disabled = true;
        });
    });

    // Fill login form for testing
    function fillLogin(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;

        // Add visual feedback
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');

        emailInput.classList.add('is-valid');
        passwordInput.classList.add('is-valid');

        setTimeout(() => {
            emailInput.classList.remove('is-valid');
            passwordInput.classList.remove('is-valid');
        }, 1000);
    }
</script>
@endsection