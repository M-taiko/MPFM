@extends('layouts.guest')

@section('title', 'Login - ADNOC MPFM')

@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html, body {
        height: 100%;
        font-family: 'Inter', 'Segoe UI', sans-serif;
    }

    body {
        background: url('{{ asset("assets/img/brand/Offshore.jpg") }}') center/cover;
        background-attachment: fixed;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    @supports not (background-attachment: fixed) {
        body {
            background-attachment: scroll;
        }
    }

    /* Dark overlay for background image */
    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.8) 0%, rgba(118, 75, 162, 0.8) 100%);
        z-index: 1;
        pointer-events: none;
    }

    body::after {
        content: '';
        position: fixed;
        top: -50%;
        right: -10%;
        width: 700px;
        height: 700px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 70%);
        border-radius: 50%;
        animation: float 25s ease-in-out infinite;
        z-index: 2;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px) translateX(0px);
        }
        50% {
            transform: translateY(40px) translateX(20px);
        }
    }

    /* Main container */
    .login-container {
        width: 100%;
        max-width: 1200px;
        height: auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
        padding: 40px;
        position: relative;
        z-index: 20;
    }

    /* Left side - Brand section */
    .login-brand {
        color: white;
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .brand-logo {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 20px;
        margin-bottom: 50px;
        animation: slideInLeft 0.8s ease-out;
    }

  .brand-logo img {
    height: auto;
    width: 100%;
    max-width: 280px;
    filter: brightness(0) invert(1);
    margin-bottom: 30px;
    position: relative;
  }

    .brand-logo-text {
        font-size: 36px;
        font-weight: 900;
        letter-spacing: -1px;
        text-align: center;
    }

    .brand-title {
        font-size: 52px;
        font-weight: 900;
        line-height: 1.2;
        margin-bottom: 25px;
        animation: slideInLeft 0.8s ease-out 0.1s backwards;
    }

    .brand-subtitle {
        font-size: 20px;
        opacity: 0.9;
        line-height: 1.7;
        margin-bottom: 60px;
        animation: slideInLeft 0.8s ease-out 0.2s backwards;
    }

    .brand-features {
        display: flex;
        flex-direction: column;
        gap: 20px;
        animation: slideInLeft 0.8s ease-out 0.3s backwards;
    }

    .feature-row {
        display: flex;
        align-items: flex-start;
        gap: 15px;
    }

    .feature-icon {
        font-size: 28px;
        flex-shrink: 0;
    }

    .feature-text {
        font-size: 15px;
        opacity: 0.95;
    }

    .feature-label {
        font-weight: 700;
        display: block;
        margin-bottom: 4px;
    }

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-40px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(40px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Right side - Form section */
    .login-form-wrapper {
        background: white;
        border-radius: 25px;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35);
        padding: 50px;
        animation: slideInRight 0.8s ease-out;
        position: relative;
        overflow: hidden;
    }

    .login-form-wrapper::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(102, 126, 234, 0.05) 0%, transparent 70%);
        border-radius: 50%;
    }

    .login-form-content {
        position: relative;
        z-index: 2;
    }

    .form-title {
        font-size: 32px;
        font-weight: 900;
        color: #333;
        margin-bottom: 10px;
    }

    .form-subtitle {
        font-size: 15px;
        color: #666;
        margin-bottom: 40px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        display: block;
        font-weight: 700;
        font-size: 13px;
        color: #333;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-control {
        width: 100%;
        padding: 14px 18px;
        border: 2px solid #e8e8e8;
        border-radius: 12px;
        font-size: 15px;
        transition: all 0.3s ease;
        background: #f8f9fa;
        color: #333;
        font-family: inherit;
    }

    .form-control::placeholder {
        color: #aaa;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 5px rgba(102, 126, 234, 0.1);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
        background: #fff5f5;
    }

    .form-control.is-invalid:focus {
        box-shadow: 0 0 0 5px rgba(220, 53, 69, 0.1);
    }

    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 13px;
        margin-top: 8px;
        font-weight: 600;
    }

    .form-group-checkbox {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 25px 0;
    }

    .form-checkbox {
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: #667eea;
    }

    .form-checkbox-label {
        font-size: 14px;
        color: #333;
        cursor: pointer;
        user-select: none;
        font-weight: 500;
    }

    .btn-signin {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        margin-bottom: 15px;
    }

    .btn-signin:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
    }

    .btn-signin:active {
        transform: translateY(0);
    }

    .form-links {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        margin-bottom: 30px;
    }

    .forgot-password-link {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .forgot-password-link:hover {
        color: #764ba2;
        text-decoration: underline;
    }

    .security-info {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #16a34a;
    }

    .divider {
        height: 1px;
        background: #e8e8e8;
        margin: 30px 0;
        position: relative;
    }

    .divider-text {
        position: absolute;
        left: 50%;
        top: -10px;
        transform: translateX(-50%);
        background: white;
        padding: 0 15px;
        font-size: 12px;
        color: #999;
        font-weight: 600;
    }

    .signup-section {
        text-align: center;
        font-size: 14px;
        color: #666;
    }

    .signup-section a {
        color: #667eea;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s ease;
        margin-left: 5px;
    }

    .signup-section a:hover {
        color: #764ba2;
        text-decoration: underline;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .login-container {
            grid-template-columns: 1fr;
            gap: 40px;
            max-width: 500px;
            margin: 0 auto;
        }

        .login-brand {
            display: none;
        }

        .login-form-wrapper {
            padding: 40px;
        }

        .form-title {
            font-size: 28px;
        }

        body::before {
            width: 500px;
            height: 500px;
        }

        body::after {
            width: 400px;
            height: 400px;
        }
    }

    @media (max-width: 768px) {
        .login-container {
            padding: 20px;
            max-width: 100%;
            gap: 30px;
        }

        .login-brand {
            padding: 20px;
            text-align: center;
        }

        .brand-logo {
            justify-content: center;
            flex-direction: column;
            align-items: center;
        }

        .brand-logo img {
            max-width: 200px !important;
            width: 90%;
            margin-bottom: 20px;
        }

        .brand-logo-text {
            font-size: 28px;
        }

        .brand-title {
            font-size: 40px;
        }

        .brand-subtitle {
            font-size: 18px;
            margin-bottom: 40px;
        }

        .brand-features {
            gap: 15px;
        }

        .feature-row {
            justify-content: flex-start;
        }

        .login-form-wrapper {
            padding: 30px 20px;
            border-radius: 20px;
        }

        .form-title {
            font-size: 24px;
        }

        .form-subtitle {
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-size: 12px;
        }

        .form-control {
            padding: 12px 15px;
            font-size: 14px;
        }

        .btn-signin {
            padding: 14px;
            font-size: 14px;
        }

        body {
            background: url('{{ asset("assets/img/brand/Offshore.jpg") }}') center/cover;
            background-attachment: scroll;
        }

        body::before {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.82) 0%, rgba(118, 75, 162, 0.82) 100%);
        }

        body::after {
            width: 300px;
            height: 300px;
            bottom: -25%;
            left: -15%;
        }
    }

    @media (max-width: 480px) {
        .login-container {
            padding: 10px;
            grid-template-columns: 1fr;
            gap: 15px;
            max-width: 100%;
        }

        .login-brand {
            display: none;
        }

        .login-form-wrapper {
            max-width: 100%;
            padding: 25px 15px;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
        }

        .login-form-content {
            position: relative;
            z-index: 2;
        }

        .form-title {
            font-size: 26px;
            margin-bottom: 8px;
        }

        .form-subtitle {
            font-size: 14px;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .form-control {
            padding: 12px 14px;
            font-size: 14px;
            border-radius: 10px;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-group-checkbox {
            margin: 18px 0;
            gap: 8px;
        }

        .form-checkbox {
            width: 18px;
            height: 18px;
        }

        .form-checkbox-label {
            font-size: 13px;
        }

        .btn-signin {
            width: 100%;
            padding: 13px;
            font-size: 15px;
            margin-bottom: 12px;
        }

        .form-links {
            font-size: 11px;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 20px;
        }

        .forgot-password-link {
            font-weight: 600;
        }

        .security-info {
            font-size: 11px;
            gap: 6px;
        }

        .divider {
            margin: 20px 0;
        }

        .divider-text {
            font-size: 11px;
            padding: 0 10px;
        }

        .signup-section {
            font-size: 12px;
            line-height: 1.6;
        }

        .signup-section a {
            font-weight: 700;
            margin-left: 3px;
        }

        body {
            background: url('{{ asset("assets/img/brand/Offshore.jpg") }}') center/cover;
            background-attachment: scroll;
        }

        body::before {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.85) 0%, rgba(118, 75, 162, 0.85) 100%);
            width: 100%;
            height: 100%;
            top: 0;
            right: 0;
        }

        body::after {
            width: 250px;
            height: 250px;
            bottom: -40%;
            left: -50%;
        }
    }


       .footer-bottom {
            border-top: 1px solid #333;
            padding-top: 10px;
            text-align: center;
            color: #ffffff;
            font-size: 14px;
        }
</style>

<div class="login-container">
    <!-- Left Brand Section -->
    <div class="login-brand">
        <div class="brand-logo">
            <img src="{{ asset('assets/img/brand/logo-white.png') }}" alt=" MPFM">
            <div class="brand-logo-text">ADNOC MPFM</div>
        </div>

        <h1 class="brand-title">Offshore Operations, Perfected</h1>

        <p class="brand-subtitle">
            Precision multiphase flow meter testing with complete operational oversight and surveillance management.
        </p>

        <div class="brand-features">
            <div class="feature-row">
                <div class="feature-icon">⚡</div>
                <div>
                    <span class="feature-label">Real-Time Monitoring</span>
                    <span class="feature-text">Live data tracking and instant alerts</span>
                </div>
            </div>
            <div class="feature-row">
                <div class="feature-icon">🔐</div>
                <div>
                    <span class="feature-label">Enterprise Security</span>
                    <span class="feature-text">Role-based access & encrypted communication</span>
                </div>
            </div>
            <div class="feature-row">
                <div class="feature-icon">📊</div>
                <div>
                    <span class="feature-label">Advanced Analytics</span>
                    <span class="feature-text">Comprehensive reports and KPI tracking</span>
                </div>
            </div>
            <div class="feature-row">
                <div class="feature-icon">✓</div>
                <div>
                    <span class="feature-label">Workflow Management</span>
                    <span class="feature-text">Automated task routing and approvals</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Login Form Section -->
    <div class="login-form-wrapper">
        <div class="login-form-content">
            <h2 class="form-title">Welcome Back</h2>
            <p class="form-subtitle">Sign in to access your dashboard</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Field -->
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input
                        id="email"
                        type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        autofocus
                        placeholder="you@example.com"
                    >
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input
                        id="password"
                        type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                    >
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="form-group-checkbox">
                    <input
                        type="checkbox"
                        class="form-checkbox"
                        name="remember"
                        id="remember"
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    <label class="form-checkbox-label" for="remember">
                        Remember me for 30 days
                    </label>
                </div>

                <!-- Sign In Button -->
                <button type="submit" class="btn-signin">Sign In Now</button>

                <!-- Links Section -->
                <div class="form-links">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-password-link">
                            Forgot password?
                        </a>
                    @endif
                    <div class="security-info">
                        🔒 Encrypted & Secure
                    </div>
                </div>

                <!-- Divider -->
                <div class="divider">
                    <span class="divider-text">or</span>
                </div>

                <!-- Sign Up Section -->
                <div class="signup-section">
                    Don't have an account? or forget your credentials? <br><a href="#">Contact Support</a><br>
                Call your system administrator to get access.
                </div>
            </form>
        </div>
    </div>
</div>

 <div class="footer-bottom">
                <p>&copy; {{ date('Y') }}  MPFM. All Rights Reserved. | Engineering Precision • Operational Excellence || this software is proprietary and confidential </p>
                <br>
                <p>Developed by <a href="https://www.facebook.com/m.tarek2014" target="_blank">Mohamed Tarek</a> with <a href="https://masarsoft.io">Masarsoft</a></p>
            </div>

@endsection

