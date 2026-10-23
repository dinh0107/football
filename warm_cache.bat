@echo off
REM Chuyển đến thư mục chứa file batch này
cd /d "%~dp0"

echo [Dang lam nong bo nho dem tran dau EPL...]
php artisan epl:warm-cache --limit=150

echo [Hoan tat lam nong cache!]
exit /b 0
