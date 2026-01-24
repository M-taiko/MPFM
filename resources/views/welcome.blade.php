<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> MPFM - Offshore Operations Management System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Feather Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.css">

    <!-- Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Poppins', sans-serif;
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Gradient Backgrounds */
        .gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-secondary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .gradient-dark {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        }

        .gradient-ocean {
            background: linear-gradient(135deg, #0093E9 0%, #80D0C7 100%);
        }

        /* Utility Classes */
        .container-custom {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .text-gradient {
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Navigation */
        nav {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            padding: 15px 0;
        }

        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100px;
            padding: 10px 0;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #333;
        }

        .logo img {
            height: 80px;
            width: auto;
            display: block;
        }

        .logo-text {
            font-size: 28px;
            font-weight: 900;
            color: #333;
            letter-spacing: -1px;
        }

        .logo-text .logo-accent {
            color: #667eea;
            font-weight: 900;
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 12px 28px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            cursor: pointer;
            border: none;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        /* Hero Section */
        .hero {
            padding: 80px 0 100px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.95) 0%, rgba(118, 75, 162, 0.95) 100%),
                        url('{{ asset("assets/img/brand/Offshore.jpg") }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 20s ease-in-out infinite;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: 5%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 25s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(30px);
            }
        }

        .hero-content {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .hero-text h1 {
            font-size: clamp(40px, 5vw, 56px);
            font-weight: 900;
            margin-bottom: 25px;
            line-height: 1.2;
        }

        .hero-text p {
            font-size: 18px;
            margin-bottom: 35px;
            opacity: 0.95;
            line-height: 1.8;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: white;
            color: #667eea;
            padding: 15px 35px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 15px 35px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
            border: 2px solid white;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-secondary:hover {
            background: white;
            color: #667eea;
        }

        .hero-features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 25px;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-5px);
        }

        .feature-card svg {
            width: 32px;
            height: 32px;
            margin-bottom: 12px;
        }

        .feature-card h4 {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
        }

        @media (max-width: 768px) {
            .hero-content {
                grid-template-columns: 1fr;
                padding: 0;
            }

            .hero-text h1 {
                font-size: 36px;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .btn-primary, .btn-secondary {
                width: 100%;
                justify-content: center;
            }
        }

        /* Stats Section */
        .stats-section {
            padding: 60px 0;
            background: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .stat-item {
            text-align: center;
            padding: 30px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .stat-number {
            font-size: 48px;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-desc {
            font-size: 13px;
            color: #666;
        }

        /* Roles Section */
        .roles-section {
            padding: 80px 0;
            background: #f8f9fa;
        }

        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-header h2 {
            font-size: clamp(32px, 4vw, 48px);
            font-weight: 900;
            margin-bottom: 15px;
            color: #333;
        }

        .section-header p {
            font-size: 18px;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }

        .roles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .role-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.4s ease;
            position: relative;
            border-top: 5px solid #667eea;
        }

        .role-card:nth-child(2) {
            border-top-color: #9333ea;
        }

        .role-card:nth-child(3) {
            border-top-color: #0ea5e9;
        }

        .role-card:nth-child(4) {
            border-top-color: #16a34a;
        }

        .role-card:nth-child(5) {
            border-top-color: #d97706;
        }

        .role-card:nth-child(6) {
            border-top-color: #dc2626;
        }

        .role-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }

        .role-header {
            padding: 30px;
            display: flex;
            align-items: center;
            gap: 20px;
            border-bottom: 1px solid #f0f0f0;
        }

        .role-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .role-card:nth-child(1) .role-icon {
            background: #eef2ff;
            color: #667eea;
        }

        .role-card:nth-child(2) .role-icon {
            background: #faf5ff;
            color: #9333ea;
        }

        .role-card:nth-child(3) .role-icon {
            background: #f0f9ff;
            color: #0ea5e9;
        }

        .role-card:nth-child(4) .role-icon {
            background: #f0fdf4;
            color: #16a34a;
        }

        .role-card:nth-child(5) .role-icon {
            background: #fffbeb;
            color: #d97706;
        }

        .role-card:nth-child(6) .role-icon {
            background: #fef2f2;
            color: #dc2626;
        }

        .role-title {
            font-size: 22px;
            font-weight: 800;
            color: #333;
        }

        .role-body {
            padding: 30px;
        }

        .role-description {
            font-size: 15px;
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .role-features {
            list-style: none;
        }

        .role-features li {
            padding: 10px 0;
            font-size: 14px;
            color: #555;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .role-features li::before {
            content: '✓';
            color: #16a34a;
            font-weight: 700;
            font-size: 18px;
        }

        /* Workflow Section */
        .workflow-section {
            padding: 80px 0;
            background: white;
        }

        .workflow-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 25px;
            padding: 50px 30px;
            color: white;
            overflow-x: auto;
        }

        .workflow-steps {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            min-width: max-content;
            position: relative;
        }

        .workflow-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            min-width: 120px;
        }

        .step-number {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            border: 3px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 900;
            margin-bottom: 15px;
        }

        .step-title {
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 8px;
        }

        .step-role {
            font-size: 12px;
            opacity: 0.85;
            text-align: center;
        }

        .workflow-line {
            flex: 1;
            height: 3px;
            background: rgba(255, 255, 255, 0.3);
            min-width: 30px;
            margin-top: 25px;
        }

        /* Features Section */
        .features-section {
            padding: 80px 0;
            background: #f8f9fa;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .feature-item {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.4s ease;
            text-align: center;
        }

        .feature-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 25px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }

        .feature-item:nth-child(1) .feature-icon {
            background: #eef2ff;
            color: #667eea;
        }

        .feature-item:nth-child(2) .feature-icon {
            background: #faf5ff;
            color: #9333ea;
        }

        .feature-item:nth-child(3) .feature-icon {
            background: #f0f9ff;
            color: #0ea5e9;
        }

        .feature-item:nth-child(4) .feature-icon {
            background: #f0fdf4;
            color: #16a34a;
        }

        .feature-item:nth-child(5) .feature-icon {
            background: #fffbeb;
            color: #d97706;
        }

        .feature-item:nth-child(6) .feature-icon {
            background: #fef2f2;
            color: #dc2626;
        }

        .feature-item h3 {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 15px;
            color: #333;
        }

        .feature-item p {
            color: #666;
            font-size: 15px;
            line-height: 1.6;
        }

        /* CTA Section */
        .cta-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 20s ease-in-out infinite;
        }

        .cta-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
        }

        .cta-content h2 {
            font-size: clamp(32px, 4vw, 48px);
            font-weight: 900;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .cta-content p {
            font-size: 18px;
            margin-bottom: 35px;
            opacity: 0.95;
            line-height: 1.8;
        }

        /* Footer */
        footer {
            background: #1a1a2e;
            color: #eee;
            padding: 60px 0 30px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-section h4 {
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 20px;
            color: white;
        }

        .footer-section p, .footer-section a {
            font-size: 14px;
            color: #aaa;
            text-decoration: none;
            line-height: 1.8;
        }

        .footer-section a:hover {
            color: #667eea;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }

        .footer-logo img {
            height: 40px;
        }

        .footer-bottom {
            border-top: 1px solid #333;
            padding-top: 30px;
            text-align: center;
            color: #888;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .hero-content {
                gap: 40px;
            }

            .roles-grid, .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .nav-content {
                height: auto;
                gap: 20px;
                padding: 15px 0;
            }

            .logo {
                gap: 12px;
            }

            .logo img {
                height: 60px !important;
                width: auto;
            }

            .logo-text {
                font-size: 22px;
            }

            .btn-login {
                padding: 10px 20px;
                font-size: 14px;
            }

            .roles-grid, .features-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .section-header h2 {
                font-size: 32px;
            }

            .hero {
                padding: 60px 0 80px;
            }

            .hero-text h1 {
                font-size: 32px;
            }

            .hero-text p {
                font-size: 16px;
            }

            .workflow-container {
                padding: 30px 15px;
            }

            .workflow-line {
                display: none;
            }

            .workflow-steps {
                flex-wrap: wrap;
            }

            .workflow-step {
                flex: 1;
                min-width: 100px;
            }

            .step-number {
                width: 60px;
                height: 60px;
                font-size: 28px;
            }

            .step-title {
                font-size: 12px;
            }

            .feature-card {
                padding: 20px;
            }

            .feature-card h4 {
                font-size: 14px;
            }

            .role-card {
                margin-bottom: 20px;
            }

            .cta-content h2 {
                font-size: 32px;
            }

            .cta-content p {
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            nav {
                padding: 10px 0;
            }

            .nav-content {
                height: auto;
                flex-direction: column;
                gap: 15px;
                padding: 15px 0;
            }

            .logo {
                gap: 10px;
                justify-content: center;
            }

            .logo img {
                height: 50px !important;
                width: auto;
            }

            .logo-text {
                font-size: 18px;
            }

            .btn-login {
                padding: 8px 16px;
                font-size: 12px;
            }

            .hero {
                padding: 40px 0 60px;
            }

            .hero-text h1 {
                font-size: 26px;
                margin-bottom: 15px;
            }

            .hero-text p {
                font-size: 14px;
                margin-bottom: 20px;
            }

            .hero-buttons {
                flex-direction: column;
                gap: 10px;
            }

            .btn-primary, .btn-secondary {
                padding: 12px 20px;
                font-size: 13px;
            }

            .hero-features {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .feature-card {
                padding: 15px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-item {
                padding: 20px;
            }

            .stat-number {
                font-size: 36px;
            }

            .section-header h2 {
                font-size: 26px;
            }

            .section-header p {
                font-size: 14px;
            }

            .role-header {
                padding: 20px;
            }

            .role-body {
                padding: 20px;
            }

            .role-title {
                font-size: 18px;
            }

            .workflow-step {
                min-width: 80px;
            }

            .step-number {
                width: 50px;
                height: 50px;
                font-size: 24px;
            }

            .step-title {
                font-size: 11px;
            }

            .step-role {
                font-size: 10px;
            }

            .feature-item {
                padding: 25px 15px;
            }

            .feature-item h3 {
                font-size: 18px;
            }

            .feature-item p {
                font-size: 13px;
            }

            .cta-section {
                padding: 60px 0;
            }

            .cta-content h2 {
                font-size: 24px;
                margin-bottom: 15px;
            }

            .cta-content p {
                font-size: 14px;
                margin-bottom: 25px;
            }

            .footer-content {
                gap: 25px;
                grid-template-columns: 1fr;
            }

            .footer-section h4 {
                font-size: 16px;
                margin-bottom: 15px;
            }

            .footer-section p, .footer-section a {
                font-size: 13px;
            }
        }

        .section-divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #ddd, transparent);
            margin: 0;
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav>
        <div class="container-custom">
            <div class="nav-content">
                <a href="#" class="logo">
                    <img src="{{ asset('assets/img/brand/logo.png') }}" alt=" MPFM Logo">
                    <div class="logo-text">
                   <span class="logo-accent" style="margin-right: 5px;">ADNOC</span>MPFM
                    </div>
                </a>
                <a href="{{ route('login') }}" class="btn-login">Login →</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container-custom">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>Offshore Operations Made <span style="color: #ffd89b;">Intelligent</span></h1>
                    <p> MPFM delivers precision multiphase flow meter testing with complete operational oversight, surveillance, and task management for modern offshore production.</p>
                    <div class="hero-buttons">
                        <a href="{{ route('login') }}" class="btn-primary">
                            <span>Get Started</span>
                            <span style="font-size: 18px;">→</span>
                        </a>
                        <a href="#features" class="btn-secondary">
                            <span style="font-size: 18px;">↓</span>
                            <span>Learn More</span>
                        </a>
                    </div>
                </div>
                <div class="hero-features">
                    <div class="feature-card">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <h4>Real-Time Data</h4>
                    </div>
                    <div class="feature-card">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <h4>Full Analytics</h4>
                    </div>
                    <div class="feature-card">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h4>Quality Assurance</h4>
                    </div>
                    <div class="feature-card">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h4>Complete History</h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container-custom">
            <h2 style="text-align: center; font-size: 42px; font-weight: 900; margin-bottom: 20px; color: #333;">System Capabilities</h2>
            <p style="text-align: center; color: #666; font-size: 18px; margin-bottom: 50px;">Enterprise-grade platform built for operational excellence</p>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">6</div>
                    <div class="stat-label">Role Types</div>
                    <div class="stat-desc">Granular permission control</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">∞</div>
                    <div class="stat-label">Scalability</div>
                    <div class="stat-desc">Unlimited task management</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Uptime</div>
                    <div class="stat-desc">Enterprise reliability</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">360°</div>
                    <div class="stat-label">Visibility</div>
                    <div class="stat-desc">Complete transparency</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Roles Section -->
    <section class="roles-section">
        <div class="container-custom">
            <div class="section-header">
                <h2>Comprehensive Role-Based System</h2>
                <p>6 distinct roles with granular permissions designed for seamless offshore operations management</p>
            </div>
            <div class="roles-grid">
                <!-- Admin -->
                <div class="role-card">
                    <div class="role-header">
                        <div class="role-icon">⚙️</div>
                        <h3 class="role-title">Admin</h3>
                    </div>
                    <div class="role-body">
                        <p class="role-description">System administrator with complete control and oversight</p>
                        <ul class="role-features">
                            <li>Full system access</li>
                            <li>User management</li>
                            <li>Configuration control</li>
                            <li>System monitoring</li>
                        </ul>
                    </div>
                </div>

                <!-- Job Officer -->
                <div class="role-card">
                    <div class="role-header">
                        <div class="role-icon">📋</div>
                        <h3 class="role-title">Job Officer</h3>
                    </div>
                    <div class="role-body">
                        <p class="role-description">Monthly plan creation and final approval authority</p>
                        <ul class="role-features">
                            <li>Create monthly plans</li>
                            <li>Submit plans for review</li>
                            <li>Final approval power</li>
                            <li>Task rejection rights</li>
                        </ul>
                    </div>
                </div>

                <!-- Surveillance Supervisor -->
                <div class="role-card">
                    <div class="role-header">
                        <div class="role-icon">👁️</div>
                        <h3 class="role-title">Surveillance</h3>
                    </div>
                    <div class="role-body">
                        <p class="role-description">Task classification and MPFM assignment oversight</p>
                        <ul class="role-features">
                            <li>Classify tasks</li>
                            <li>Assign to MPFM</li>
                            <li>Pre-review tasks</li>
                            <li>Dashboard access</li>
                        </ul>
                    </div>
                </div>

                <!-- MPFM Supervisor -->
                <div class="role-card">
                    <div class="role-header">
                        <div class="role-icon">🔬</div>
                        <h3 class="role-title">MPFM Supervisor</h3>
                    </div>
                    <div class="role-body">
                        <p class="role-description">Test operations and technical review management</p>
                        <ul class="role-features">
                            <li>Assign operators</li>
                            <li>Review test data</li>
                            <li>Approve/reject tests</li>
                            <li>Analytics access</li>
                        </ul>
                    </div>
                </div>

                <!-- Production Supervisor -->
                <div class="role-card">
                    <div class="role-header">
                        <div class="role-icon">⚡</div>
                        <h3 class="role-title">Production</h3>
                    </div>
                    <div class="role-body">
                        <p class="role-description">Production asset oversight and reporting</p>
                        <ul class="role-features">
                            <li>View production data</li>
                            <li>Archive access</li>
                            <li>Generate reports</li>
                            <li>Historical analysis</li>
                        </ul>
                    </div>
                </div>

                <!-- MPFM Operator -->
                <div class="role-card">
                    <div class="role-header">
                        <div class="role-icon">🛠️</div>
                        <h3 class="role-title">MPFM Operator</h3>
                    </div>
                    <div class="role-body">
                        <p class="role-description">Field execution and data collection</p>
                        <ul class="role-features">
                            <li>Execute tests</li>
                            <li>Upload data</li>
                            <li>Submit reports</li>
                            <li>Task tracking</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <!-- Workflow Section -->
    <section class="workflow-section">
        <div class="container-custom">
            <div class="section-header">
                <h2>Complete Workflow Process</h2>
                <p>From planning to completion - every step is tracked with role-based controls</p>
            </div>
            <div class="workflow-container">
                <div class="workflow-steps">
                    <div class="workflow-step">
                        <div class="step-number">1</div>
                        <div class="step-title">Monthly<br>Planning</div>
                        <div class="step-role">Job Officer</div>
                    </div>
                    <div class="workflow-line"></div>
                    <div class="workflow-step">
                        <div class="step-number">2</div>
                        <div class="step-title">Task<br>Submission</div>
                        <div class="step-role">Job Officer</div>
                    </div>
                    <div class="workflow-line"></div>
                    <div class="workflow-step">
                        <div class="step-number">3</div>
                        <div class="step-title">Classification &<br>Assignment</div>
                        <div class="step-role">Surveillance</div>
                    </div>
                    <div class="workflow-line"></div>
                    <div class="workflow-step">
                        <div class="step-number">4</div>
                        <div class="step-title">Operator<br>Execution</div>
                        <div class="step-role">MPFM Operator</div>
                    </div>
                    <div class="workflow-line"></div>
                    <div class="workflow-step">
                        <div class="step-number">5</div>
                        <div class="step-title">MPFM<br>Review</div>
                        <div class="step-role">MPFM Supervisor</div>
                    </div>
                    <div class="workflow-line"></div>
                    <div class="workflow-step">
                        <div class="step-number">6</div>
                        <div class="step-title">Final<br>Approval</div>
                        <div class="step-role">Job Officer</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="container-custom">
            <div class="section-header">
                <h2>Powerful Features</h2>
                <p>Comprehensive tools designed for modern offshore operations</p>
            </div>
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">📅</div>
                    <h3>Monthly Planning</h3>
                    <p>Create and manage comprehensive test plans with full audit trails and version control</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🔬</div>
                    <h3>MPFM Testing</h3>
                    <p>Execute tests with real-time data capture, automated calculations and reporting</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">✓</div>
                    <h3>Task Management</h3>
                    <p>Track lifecycle from creation to completion with status updates and deadlines</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">👁️</div>
                    <h3>Surveillance</h3>
                    <p>Comprehensive oversight dashboards for review, approval and management</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">📊</div>
                    <h3>Analytics</h3>
                    <p>Interactive charts, real-time metrics and comprehensive reports with KPIs</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🗂️</div>
                    <h3>Archive</h3>
                    <p>Complete historical records with full traceability and compliance documentation</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-content">
            <h2>Ready to Transform Your Offshore Operations?</h2>
            <p> MPFM provides the complete solution for multiphase flow meter management with operational excellence and surveillance oversight.</p>
            <a href="{{ route('login') }}" class="btn-primary">
                <span>Access the System</span>
                <span style="font-size: 18px;">→</span>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container-custom">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <img src="{{ asset('assets/img/brand/logo-white.png') }}" alt=" MPFM">
                        <span style="font-weight: 800; font-size: 16px;"> MPFM</span>
                    </div>
                    <p>Advanced offshore operations management system built for precision and reliability</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <a href="{{ route('login') }}">Login</a><br>
                    <a href="#features">Features</a><br>
                    <a href="#roles-section">Roles</a>
                </div>
                <div class="footer-section">
                    <h4>Support</h4>
                    <a href="#">Documentation</a><br>
                    <a href="#">Support Center</a><br>
                    <a href="#">Contact Us</a>
                </div>
                <div class="footer-section">
                    <h4>Company</h4>
                    <a href="#">About Us</a><br>
                    <a href="#">Technology</a><br>
                    <a href="#">Privacy Policy</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }}  MPFM. All Rights Reserved. | Engineering Precision • Operational Excellence || this software is proprietary and confidential </p>
                <br>
                <p>Developed by <a href="https://www.facebook.com/m.tarek2014" target="_blank">Mohamed Tarek</a> with <a href="https://masarsoft.io">Masarsoft</a></p>
            </div>
        </div>
    </footer>

</body>
</html>
