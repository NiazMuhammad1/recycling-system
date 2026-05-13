@extends('layouts.auth')

@section('content')
<style>
    /* 1. LAYOUT RESET & GRADIENT */
    html, body {
        height: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: hidden !important;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .login-wrapper {
        height: 100vh;
        width: 100vw;
        display: flex;
        align-items: center;
        justify-content: center;
        /* Matches your Login & Reset pages */
        background: linear-gradient(135deg, #ffffff 0%, #94a3b8 45%, #000000 100%);
        padding: 20px;
    }

    /* 2. CARD DESIGN */
    .login-card {
        width: 100%;
        max-width: 420px;
        background: #ffffff;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    }

    .login-header {
        text-align: center;
        margin-bottom: 25px;
    }

    .logo-img {
        height: 100px;
        width: auto;
        margin-bottom: 15px;
    }

    .login-title {
        color: #111827;
        font-size: 22px;
        font-weight: 700;
        margin: 0;
    }

    .login-subtitle {
        color: #6b7280;
        font-size: 14px;
        margin-top: 8px;
    }

    /* 3. FORM ELEMENTS */
    .form-group-custom {
        margin-top: 25px;
        margin-bottom: 20px;
    }

    .form-group-custom label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }

    .form-control-custom {
        width: 100%;
        height: 48px;
        padding: 10px 15px;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        font-size: 15px;
        box-sizing: border-box;
    }

    .form-control-custom:focus {
        outline: none;
        border-color: #000000;
        box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.05);
    }

    /* 4. BUTTONS & ALERTS */
    .btn-submit {
        width: 100%;
        height: 50px;
        background: #000000;
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-submit:hover {
        background: #1f2937;
    }

    .back-to-login {
        display: block;
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
        color: #6b7280;
        text-decoration: none;
    }

    .back-to-login:hover {
        text-decoration: underline;
    }

    .alert-custom {
        background-color: #ecfdf5;
        border: 1px solid #10b981;
        color: #065f46;
        padding: 12px;
        border-radius: 10px;
        font-size: 14px;
        margin-bottom: 20px;
    }

    .error-text {
        color: #dc2626;
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }
</style>

<div class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <img class="logo-img" src="{{ asset('storage/images/ecogreen_logo.png') }}" alt="Ecogreen Logo">
            <h2 class="login-title">{{ __('Forgot Password?') }}</h2>
            <p class="login-subtitle">{{ __('Enter your email to get a reset link.') }}</p>
        </div>

        @if (session('status'))
            <div class="alert-custom" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group-custom">
                <label>{{ __('Email Address') }}</label>
                <input type="email" name="email" 
                       class="form-control-custom @error('email') is-invalid @enderror" 
                       value="{{ old('email') }}" required autofocus
                       placeholder="Enter your registered email">
                
                @error('email')
                    <span class="error-text"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <button type="submit" class="btn-submit">
                {{ __('Send Reset Link') }}
            </button>

            <a href="{{ route('login') }}" class="back-to-login">
                {{ __('Back to Login') }}
            </a>
        </form>
    </div>
</div>
@endsection