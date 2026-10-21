<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Quản Trị Viên | Premier League Admin</title>

    {{-- Favicon & App Icons --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --pl-purple: #0f172a;
            --pl-pink: #2563eb;
            --pl-mint: #06b6d4;
            --pl-bg: #f8fafc;
            --pl-card: #ffffff;
            --pl-border: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
            background: var(--pl-bg);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-image: 
                radial-gradient(circle at 10% 10%, rgba(56, 0, 60, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 90% 90%, rgba(233, 0, 82, 0.04) 0%, transparent 40%);
        }
        .login-card {
            width: 100%;
            max-width: 440px;
            background: var(--pl-card);
            border: 1px solid var(--pl-border);
            border-radius: 20px;
            padding: 36px 32px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
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
            margin-bottom: 28px;
        }
        .brand-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--pl-purple) 0%, var(--pl-pink) 100%);
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.25rem;
            font-weight: 900;
            margin-bottom: 12px;
            box-shadow: 0 4px 12px rgba(56, 0, 60, 0.2);
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
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
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
            color: #334155;
            margin-bottom: 6px;
        }
        .form-input {
            width: 100%;
            padding: 11px 14px;
            border-radius: 10px;
            border: 1.5px solid var(--pl-border);
            font-size: 0.925rem;
            font-family: inherit;
            outline: none;
            transition: all 0.2s ease;
        }
        .form-input:focus {
            border-color: var(--pl-purple);
            box-shadow: 0 0 0 3px rgba(56, 0, 60, 0.08);
        }
        .btn-submit {
            width: 100%;
            padding: 13px;
            border-radius: 10px;
            border: none;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.2);
            margin-top: 8px;
        }
        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.28);
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 0.825rem;
            color: var(--text-muted);
            text-decoration: none;
        }
        .back-link:hover {
            color: var(--pl-purple);
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-header">
            <img src="{{ asset('images/logo.png') }}" alt="Logo tysongoaihang" width="56" height="56" style="border-radius: 14px; margin-bottom: 12px; box-shadow: 0 4px 14px rgba(37,99,235,0.25);">
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
</body>
</html>
