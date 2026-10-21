@extends('layouts.app')

@push('styles')
<style>
    .admin-login-wrapper {
        min-height: calc(100vh - 380px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 16px;
    }
    .login-card {
        width: 100%;
        max-width: 440px;
        background: #ffffff;
        border: 1px solid var(--border-light);
        border-radius: var(--radius-lg);
        padding: 36px 32px;
        box-shadow: var(--shadow-md);
        position: relative;
        overflow: hidden;
    }
    .login-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--pl-purple), var(--pl-pink), var(--pl-mint));
    }
    .brand-header {
        text-align: center;
        margin-bottom: 26px;
    }
    .brand-title {
        font-size: 1.35rem;
        font-weight: 900;
        color: var(--pl-purple);
    }
    .brand-subtitle {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-top: 4px;
    }
    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 700;
        margin-bottom: 20px;
    }
    .alert-error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .alert-success {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    .form-group {
        margin-bottom: 18px;
    }
    .form-label {
        display: block;
        font-size: 0.825rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 6px;
    }
    .form-input {
        width: 100%;
        padding: 11px 14px;
        border-radius: 8px;
        border: 1px solid var(--border-light);
        font-size: 0.925rem;
        font-family: inherit;
        background: #f8fafc;
        outline: none;
        transition: all 0.2s ease;
    }
    .form-input:focus {
        border-color: var(--pl-pink);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }
    .btn-submit {
        width: 100%;
        padding: 13px;
        border-radius: 8px;
        border: none;
        background: var(--pl-purple);
        color: #ffffff;
        font-size: 0.95rem;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.2);
        margin-top: 8px;
    }
    .btn-submit:hover {
        background: #1e293b;
        transform: translateY(-1px);
    }
    .back-link {
        display: block;
        text-align: center;
        margin-top: 20px;
        font-size: 0.825rem;
        color: var(--text-muted);
        text-decoration: none;
        font-weight: 600;
    }
    .back-link:hover {
        color: var(--pl-purple);
        text-decoration: underline;
    }
</style>
@endpush

@section('content')
<div class="admin-login-wrapper">
    <div class="login-card">
        <div class="brand-header">
            <img src="{{ asset('images/logo.png') }}" alt="Logo tysongoaihang" width="56" height="56" style="border-radius: 14px; margin-bottom: 12px; box-shadow: 0 4px 14px rgba(37,99,235,0.25);" onerror="this.onerror=null; this.src='/images/logo.png';">
            <h1 class="brand-title">Admin Dashboard</h1>
            <p class="brand-subtitle">Hệ thống Quản trị Website Ngoại Hạng Anh</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('warning'))
            <div class="alert alert-error">{{ session('warning') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Địa chỉ Email Quản trị</label>
                <input type="email" id="email" name="email" class="form-input" 
                       value="{{ old('email') }}" required autofocus placeholder="admin@premierleague.local">
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" class="form-input" 
                       required placeholder="••••••••">
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; font-size: 0.825rem;">
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                    <input type="checkbox" name="remember" value="1">
                    Ghi nhớ đăng nhập
                </label>
            </div>

            <button type="submit" class="btn-submit">Đăng Nhập Quản Trị</button>
        </form>

        <a href="{{ route('home') }}" class="back-link">&larr; Quay lại trang chủ người dùng</a>
    </div>
</div>
@endsection
