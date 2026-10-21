<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Đăng Nhập Quản Trị Viên | Premier League Control</title>

    {{-- Favicon & App Icons --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    {{-- Google Font & Tabler Core CSS --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css"/>
    <style>
        :root {
            --tblr-font-sans-serif: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            --tblr-primary: #0f172a;
        }
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="d-flex flex-column">
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href="{{ route('home') }}" class="navbar-brand navbar-brand-autodark">
                    <img src="{{ asset('images/logo.png') }}" height="60" alt="Logo" style="border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.3);" onerror="this.onerror=null; this.src='/images/logo.png';">
                </a>
            </div>

            <div class="card card-md shadow-lg border-0" style="border-radius: 14px;">
                <div class="card-body p-4 p-md-5">
                    <h2 class="h2 text-center mb-1 fw-black text-dark">Đăng Nhập Quản Trị</h2>
                    <p class="text-center text-secondary small mb-4">Hệ thống Quản lý Dữ liệu Ngoại Hạng Anh</p>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible" role="alert">
                            {{ session('warning') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('admin.login.submit') }}" method="POST" autocomplete="off">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold">Địa chỉ Email</label>
                            <input type="email" name="email" class="form-control" placeholder="admin@premierleague.local" value="{{ old('email') }}" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Mật khẩu
                            </label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-check">
                                <input type="checkbox" name="remember" value="1" class="form-check-input"/>
                                <span class="form-check-label">Ghi nhớ đăng nhập trên thiết bị này</span>
                            </label>
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn btn-dark w-100 fw-bold py-2 fs-4">
                                Đăng Nhập Hệ Thống
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center text-secondary-emphasis mt-3">
                <a href="{{ route('home') }}" class="text-white-50 small text-decoration-none">
                    &larr; Quay lại trang chủ người dùng
                </a>
            </div>
        </div>
    </div>

    {{-- Tabler Core JS --}}
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js" defer></script>
</body>
</html>
