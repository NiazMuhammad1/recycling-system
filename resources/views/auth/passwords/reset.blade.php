@extends('layouts.auth')

@section('content')
<style>
    /* 1. LAYOUT RESET */
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
        /* High Contrast White to Black Gradient */
        background: linear-gradient(135deg, #ffffff 0%, #94a3b8 45%, #000000 100%);
    }

    /* 2. CARD STYLING */
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
        margin-bottom: 30px;
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

    /* 3. FORM ELEMENTS */
    .form-group-custom {
        margin-bottom: 20px;
    }

    .form-group-custom label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #4b5563;
        margin-bottom: 8px;
    }

    .form-control-custom {
        width: 100%;
        height: 48px;
        padding: 10px 15px;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        font-size: 15px;
        box-sizing: border-box; /* Prevents input from overflowing */
    }

    .form-control-custom:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    /* 4. BUTTON */
    .btn-reset {
        width: 100%;
        height: 50px;
        background: #000000;
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        margin-top: 10px;
    }

    .btn-reset:hover {
        background: #2d3748;
    }

    /* Error Text */
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
            <h2 class="login-title">{{ __('Reset Password') }}</h2>
        </div>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group-custom">
                <label>{{ __('Email Address') }}</label>
                <input type="email" name="email" class="form-control-custom" value="{{ $email ?? old('email') }}" required autofocus>
                @error('email')
                    <span class="error-text"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="form-group-custom">
                <label>{{ __('New Password') }}</label>
                <input type="password" name="password" class="form-control-custom" required>
                @error('password')
                    <span class="error-text"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="form-group-custom">
                <label>{{ __('Confirm New Password') }}</label>
                <input type="password" name="password_confirmation" class="form-control-custom" required>
            </div>

            <button type="submit" class="btn-reset">
                {{ __('Update Password') }}
            </button>
        </form>
    </div>
</div>
@endsection