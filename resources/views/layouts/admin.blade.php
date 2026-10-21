<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>@yield('title', 'Admin Dashboard') | Premier League Control Center</title>

    {{-- Favicon & App Icons --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- Tabler Dashboard Core CSS (Theme mã nguồn mở chuẩn quốc tế) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css"/>
    <style>
        :root {
            --tblr-font-sans-serif: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
            --tblr-primary: #0f172a;
            --tblr-primary-rgb: 15, 23, 42;
        }
        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
        .navbar-vertical[data-bs-theme="dark"] {
            background: linear-gradient(180deg, #0f172a 0%, #020617 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.08);
        }
        .navbar-brand-autodark img {
            border-radius: 8px;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="page">
        {{-- Sidebar Điều Hướng Tabler --}}
        <aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark">
                    <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none text-white">
                        <img src="{{ asset('images/logo.png') }}" width="32" height="32" alt="Logo" onerror="this.onerror=null; this.src='/images/logo.png';">
                        <span class="fs-3 fw-bold">EPL Control</span>
                    </a>
                </h1>
                
                <div class="navbar-nav flex-row d-lg-none">
                    <div class="nav-item">
                        <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger">Thoát</button>
                        </form>
                    </div>
                </div>

                <div class="collapse navbar-collapse" id="sidebar-menu">
                    <ul class="navbar-nav pt-lg-3">
                        <li class="nav-item active">
                            <a class="nav-link text-white" href="{{ route('admin.dashboard') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg>
                                </span>
                                <span class="nav-link-title fw-bold">Dashboard Quản Trị</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#panel-tv">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /><path d="M16 3l-4 4l-4 -4" /></svg>
                                </span>
                                <span class="nav-link-title">Kênh Phát Sóng TV</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#panel-maintenance">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" /><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" /></svg>
                                </span>
                                <span class="nav-link-title">Bảo Trì & Xóa Cache</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#panel-banners">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M4 9h16" /><path d="M9 4v16" /></svg>
                                </span>
                                <span class="nav-link-title">Banner Quảng Cáo</span>
                            </a>
                        </li>

                        <li class="nav-item mt-3">
                            <a class="nav-link text-warning" href="{{ route('home') }}" target="_blank">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" /><path d="M11 13l9 -9" /><path d="M15 4h5v5" /></svg>
                                </span>
                                <span class="nav-link-title">Xem Trang Khách &rarr;</span>
                            </a>
                        </li>
                    </ul>

                    {{-- Admin Footer Profile --}}
                    <div class="mt-auto p-3 border-top border-secondary-subtle">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="fw-bold text-white fs-4">{{ Auth::user()->name ?? 'Admin' }}</div>
                                <div class="text-secondary small">Administrator</div>
                            </div>
                            <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Đăng Xuất">
                                    Thoát
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Top Header --}}
        <header class="navbar navbar-expand-md d-none d-lg-flex d-print-none bg-white border-bottom">
            <div class="container-xl">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-green-lt text-green d-flex align-items-center gap-1">
                        <span class="status-dot status-dot-animated bg-green"></span>
                        Hệ Thống Trực Tuyến
                    </span>
                    <span class="text-secondary small">Môi trường: <strong>{{ config('app.env') }}</strong></span>
                </div>

                <div class="navbar-nav flex-row order-md-last align-items-center gap-3">
                    <a href="{{ route('home') }}" target="_blank" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" /><path d="M11 13l9 -9" /><path d="M15 4h5v5" /></svg>
                        Xem Website
                    </a>

                    <div class="nav-item">
                        <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger">Đăng Xuất</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Main Page Content --}}
        <div class="page-wrapper">
            <div class="page-body">
                <div class="container-xl">
                    @yield('content')
                </div>
            </div>

            <footer class="footer footer-transparent d-print-none border-top py-3">
                <div class="container-xl">
                    <div class="row text-center align-items-center flex-row-reverse">
                        <div class="col-lg-auto ms-lg-auto">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item"><a href="{{ route('home') }}" target="_blank" class="link-secondary">Trang Chủ Website</a></li>
                                <li class="list-inline-item"><a href="{{ route('about') }}" target="_blank" class="link-secondary">Giới Thiệu</a></li>
                            </ul>
                        </div>
                        <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item">
                                    Copyright &copy; {{ date('Y') }}
                                    <a href="{{ route('home') }}" class="link-secondary">Premier League Live Tracker</a>. All rights reserved.
                                </li>
                                <li class="list-inline-item">
                                    <span class="badge bg-blue-lt">Tabler Dashboard Open Source UI</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    {{-- Tabler Core JS --}}
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js" defer></script>
    @stack('scripts')
</body>
</html>
