@echo off
REM Chuyển đến thư mục chứa file batch này
cd /d "%~dp0"

REM Thực thi lịch trình Laravel Scheduler
php artisan schedule:run

exit /b 0
