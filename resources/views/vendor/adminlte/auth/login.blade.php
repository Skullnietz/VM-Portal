@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('adminlte_css_pre')
<link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
<style>
    @import url(//fonts.googleapis.com/css?family=Lato:300:400);

    body {
        /* Restore Background Image */
        background-image: url('/Images/Backgrounds/bguser.png') !important;
        background-size: cover !important;
        background-repeat: no-repeat !important;
        background-position: center !important;
        background-attachment: fixed !important;
    }

    /* Waves Animation CSS */
    .waves {
        position: fixed;
        /* Fixed to stay at bottom */
        bottom: 0;
        left: 0;
        width: 100%;
        height: 15vh;
        margin-bottom: -7px;
        min-height: 100px;
        max-height: 150px;
        z-index: 0;
        /* Behind everything */
    }

    .parallax>use {
        animation: move-forever 25s cubic-bezier(.55, .5, .45, .5) infinite;
    }

    .parallax>use:nth-child(1) {
        animation-delay: -2s;
        animation-duration: 7s;
    }

    .parallax>use:nth-child(2) {
        animation-delay: -3s;
        animation-duration: 10s;
    }

    .parallax>use:nth-child(3) {
        animation-delay: -4s;
        animation-duration: 13s;
    }

    .parallax>use:nth-child(4) {
        animation-delay: -5s;
        animation-duration: 20s;
    }

    @keyframes move-forever {
        0% {
            transform: translate3d(-90px, 0, 0);
        }

        100% {
            transform: translate3d(85px, 0, 0);
        }
    }

    @media (max-width: 768px) {
        .waves {
            height: 40px;
            min-height: 40px;
        }
    }
</style>
@stop

{{-- FIX: Use the correct named route for login, defaulting to 'es' if no locale --}}
@php( $login_url = route('validar-registro', ['language' => app()->getLocale() ?: 'es']) )
@php( $register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register') )
@php( $password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset') )

@section('auth_header')
<div class="row mb-4" style="position: relative; z-index: 2;">
    <div class="col-12 text-center">
        <h2 class="font-weight-light text-white" style="text-shadow: 0 2px 4px rgba(0,0,0,0.5);">
            <b>Spartan</b> Vending
        </h2>
    </div>
</div>
@stop

@section('auth_body')
<style>
    .login-box,
    .register-box {
        width: 420px !important;
        position: relative;
        z-index: 2;
    }

    /* Transparent Parent */
    .login-box .card,
    .register-box .card,
    .card-body,
    .login-card-body {
        background-color: transparent !important;
        background: transparent !important;
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
    }

    /* Hide Default Text */
    .login-box-msg,
    .card-body>p,
    .login-card-body>p {
        display: none !important;
    }

    /* Glass Card */
    .glass-card {
        background: rgba(255, 255, 255, 0.85) !important;
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border-radius: 20px !important;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2) !important;
        padding: 40px 30px !important;
        color: #495057;
        position: relative;
        margin-top: 10px;
        z-index: 50 !important;
    }

    /* Inputs */
    .custom-input-wrapper {
        position: relative;
        margin-bottom: 20px;
        width: 100%;
    }

    .form-control {
        display: block;
        width: 100%;
        border-radius: 50px !important;
        background-color: rgba(255, 255, 255, 0.9);
        border: 1px solid #ced4da;
        height: 50px;
        padding-left: 20px;
        padding-right: 50px;
        box-sizing: border-box;
    }

    .form-control:focus {
        background-color: #fff;
        box-shadow: 0 0 0 0.2rem rgba(14, 165, 106, 0.25);
        border-color: #0ea56a;
        outline: none;
    }

    .custom-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        color: #adb5bd;
        font-size: 1.1rem;
        pointer-events: none;
    }

    .btn-primary {
        border-radius: 50px !important;
        background: #007bff;
        border: none;
        font-weight: bold;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 6px rgba(0, 123, 255, 0.3);
        transition: all 0.2s ease-in-out;
        padding: 12px 0;
        font-size: 16px;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 123, 255, 0.4);
        background: #0056b3;
    }

    .auth-links a {
        color: #007bff;
        font-weight: 500;
    }

    .custom-welcome-msg {
        font-weight: 700;
        color: #333;
        margin-bottom: 30px;
        font-size: 1.6rem;
        text-align: center;
        display: block !important;
    }

    .icheck-primary label {
        cursor: pointer;
    }

    .alert-danger {
        border-radius: 15px;
        font-size: 0.9rem;
        margin-bottom: 20px;
    }
</style>

<div class="glass-card">
    <h4 class="custom-welcome-msg">¡Bienvenido!</h4>

    @if($errors->any())
        <div class="alert alert-danger text-center">
            <i class="fas fa-exclamation-triangle mr-1"></i> {{ $errors->first('msg') ?: $errors->first() }}
        </div>
    @endif

    <form action="{{ $login_url }}" method="post">
        @csrf
        <div class="custom-input-wrapper">
            <input type="text" name="usuario" class="form-control" value="{{ old('usuario') }}" placeholder="Usuario"
                autofocus>
            <span class="fas fa-user custom-icon"></span>
        </div>
        <div class="custom-input-wrapper">
            <input type="password" name="password" class="form-control" placeholder="Contraseña">
            <span class="fas fa-lock custom-icon"></span>
        </div>
        <div class="row align-items-center mt-4">
            <div class="col-5">
                <div class="icheck-primary">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember" style="font-weight: normal; color: #555;">Recordarme</label>
                </div>
            </div>
            <div class="col-7">
                <button type=submit class="btn btn-primary btn-block"><i class="fas fa-sign-in-alt mr-2"></i> Iniciar
                    Sesión</button>
            </div>
        </div>
    </form>

    <div class="auth-links mt-4 text-center">
        @if($password_reset_url)
            <a href="{{ $password_reset_url }}">Olvidé mi contraseña</a>
        @endif
    </div>
</div>

<!-- Waves SVG -->
<svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28"
    preserveAspectRatio="none" shape-rendering="auto">
    <defs>
        <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
    </defs>
    <g class="parallax">
        <use xlink:href="#gentle-wave" x="48" y="0" fill="rgba(14, 126, 45, 0.7)" />
        <use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(14, 126, 45, 0.5)" />
        <use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(15, 25, 52, 0.3)" />
        <use xlink:href="#gentle-wave" x="48" y="7" fill="#0F1934" />
    </g>
</svg>
@stop