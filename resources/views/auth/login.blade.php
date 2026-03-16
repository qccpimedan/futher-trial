@extends('layouts.app')
@php
    $isLocalhost = in_array(request()->getHost(), ['localhost', '127.0.0.1', '10.68.1.37']);
    $assetPath = $isLocalhost ? 'public/' : '';
@endphp

@section('container')
<div class="min-vh-100 d-flex align-items-center login-wrapper">
    <div class="overlay-bg"></div>

    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="login-card">
                    <div class="text-center mb-4">
                        <img src="{{ asset($assetPath . 'dist/img/cpi-logo.png') }}" alt="CPI Logo" class="logo-img">
                        <h4 class="welcome-text mt-3">Welcome Back</h4>
                        <p class="subtitle">Sign in to Paperless Further</p>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger alert-clean">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <div class="form-group-clean">
                            <label for="login" class="form-label-clean">Username or Email</label>
                            <input 
                                id="login" 
                                type="text" 
                                class="form-input-clean" 
                                name="login" 
                                placeholder="Enter your username or email"
                                required 
                                autofocus
                            >
                        </div>

                        <div class="form-group-clean">
                            <label for="password" class="form-label-clean">Password</label>
                            <div class="password-wrapper">
                                <input 
                                    id="password" 
                                    type="password" 
                                    class="form-input-clean" 
                                    name="password" 
                                    placeholder="Enter your password"
                                    required
                                >
                                <button 
                                    type="button" 
                                    class="password-toggle"
                                    onclick="togglePassword()"
                                >
                                    <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn-login">
                            🍗 Sign In
                        </button>
                    </form>

                    <div class="footer-text">
                        <p>Paperless Further v1.4</p>
                        <div class="copyright-text">
                            &copy; {{ date('Y') }} PT. Charoen Pokphand Indonesia <br>
                            <span style="font-size: 12px;">|| All rights reserved by Tim Industry 4.0 ||</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .login-wrapper {
        position: relative;
        background: url("{{ asset($assetPath . 'dist/img/nugget-bg.webp') }}") no-repeat center center;
        background-size: cover;
        background-attachment: fixed;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        overflow: hidden;
    }

    /* Dark overlay biar teks kebaca */
    .overlay-bg {
        position: absolute;
        inset: 0;
        background: linear-gradient(
            135deg,
            rgba(0,0,0,0.65),
            rgba(0,0,0,0.45)
        );
        z-index: 1;
    }

    .container.position-relative {
        z-index: 2;
    }

    /* Glassmorphism Card */
    .login-card {
        background: rgba(255, 255, 255, 0.88);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border-radius: 20px;
        padding: 48px 40px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
        animation: fadeInUp 0.7s ease-out;
        border: 1px solid rgba(255,255,255,0.4);
    }

    .logo-img {
        width: 80px;
        height: auto;
    }

    .welcome-text {
        color: #1f2933;
        font-weight: 700;
        font-size: 26px;
        margin-bottom: 4px;
    }

    .subtitle {
        color: #6b7280;
        font-size: 14px;
        margin: 0;
    }

    .alert-clean {
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #991b1b;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 14px;
        margin-bottom: 24px;
    }

    .form-group-clean {
        margin-bottom: 20px;
    }

    .form-label-clean {
        display: block;
        color: #374151;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .form-input-clean {
        width: 100%;
        padding: 14px 16px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.2s ease;
        background: #f9fafb;
    }

    .form-input-clean:focus {
        outline: none;
        border-color: #f59e0b; /* warna nugget */
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.25);
    }

    .password-wrapper {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #9ca3af;
        cursor: pointer;
    }

    .password-toggle:hover {
        color: #f59e0b;
    }

    /* Tombol tema nugget */
    .btn-login {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 8px;
        box-shadow: 0 8px 20px rgba(249, 115, 22, 0.4);
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(249, 115, 22, 0.55);
    }

    .footer-text {
        text-align: center;
        padding-top: 24px;
        border-top: 1px solid #e5e7eb;
        /* margin-top: 24px; */
    }

    .footer-text p,
    .copyright-text {
        color: #6b7280;
        font-size: 12px;
        margin: 0;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .login-card {
            padding: 32px 24px;
        }

        .logo-img {
            width: 60px;
        }

        .welcome-text {
            font-size: 22px;
        }
    }
</style>

<script>
    function togglePassword() {
        const passwordField = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePasswordIcon');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
</script>
@endsection
