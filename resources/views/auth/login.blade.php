@extends('layouts.auth')

@section('content')
<style>
    /* 1. RESET & LAYOUT FIXES */
    /* Force the parent layout to behave */
    html, body {
        height: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: hidden !important; /* Removes the right scrollbar */
    }

   .login-wrapper {
        height: 100vh;
        width: 100vw;
        display: flex;
        align-items: center;
        justify-content: center;
        /* High Contrast: Pure White to Solid Black */
        background: linear-gradient(135deg, #ffffff 0%, #94a3b8 45%, #000000 100%);
        padding: 20px;
        box-sizing: border-box;
    }

    /* 2. CARD DESIGN */
    .login-card {
        width: 100%;
        max-width: 400px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }

    .login-header {
        text-align: center;
        margin-bottom: 32px;
    }

    .logo-img {
        height: 100px;
        width: auto;
        margin-bottom: 16px;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
    }

    .login-title {
        color: #1e293b;
        font-size: 22px;
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    /* 3. FORM INPUTS */
    .form-group-custom {
        margin-bottom: 20px;
    }

    .form-group-custom label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .input-wrapper {
        position: relative;
    }

    .form-control-custom {
        width: 100%;
        padding: 12px 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-size: 15px;
        color: #1e293b;
        transition: all 0.2s ease;
        box-sizing: border-box; /* Crucial for width: 100% */
    }

    .form-control-custom:focus {
        outline: none;
        background: #ffffff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    /* 4. BUTTONS & LINKS */
    .btn-login {
        width: 100%;
        background: #2563eb;
        color: white;
        padding: 14px;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: transform 0.1s ease, background 0.2s ease;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
    }

    .btn-login:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .btn-login:active {
        transform: translateY(0);
    }

    .login-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 24px;
        font-size: 13px;
        color: #64748b;
    }

    .forgot-link {
        color: #2563eb;
        text-decoration: none;
        font-weight: 500;
    }

    .forgot-link:hover {
        text-decoration: underline;
    }

    /* ERROR STATES */
    .is-invalid {
        border-color: #ef4444 !important;
    }
    .error-msg {
        color: #ef4444;
        font-size: 12px;
        margin-top: 4px;
        display: block;
    }
</style>

<div class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <img class="logo-img" src="{{ asset('storage/images/ecogreen_logo.png') }}" alt="ecogreen IT">
            <div class="login-title">{{ __('Welcome Back') }}</div>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group-custom">
                <label>{{ __('Email Address') }}</label>
                <div class="input-wrapper">
                    <input type="email" name="email" 
                           class="form-control-custom @error('email') is-invalid @enderror" 
                           value="{{ old('email') }}" required autofocus 
                           placeholder="Enter your email">
                </div>
                @error('email')
                    <span class="error-msg"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="form-group-custom">
                <label>{{ __('Password') }}</label>
                <div class="input-wrapper">
                    <input type="password" name="password" 
                           class="form-control-custom @error('password') is-invalid @enderror" 
                           required placeholder="••••••••">
                </div>
                @error('password')
                    <span class="error-msg"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="login-footer">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" name="remember" style="margin-right: 8px; accent-color: #2563eb;"> 
                    {{ __('Remember Me') }}
                </label>
                @if (Route::has('password.request'))
                    <a class="forgot-link" href="{{ route('password.request') }}">{{ __('Forgot?') }}</a>
                @endif
            </div>

            <button type="submit" class="btn-login" style="margin-top: 30px;">
                {{ __('Login') }}
            </button>
        </form>
    </div>
</div>
@endsection