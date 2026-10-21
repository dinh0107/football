#!/bin/bash
set -e

echo "=== [$(date)] Bắt đầu tự động cập nhật mã nguồn ==="

# Chuyển về thư mục dự án
cd "$(dirname "$0")"

# 1. Kéo code mới nhất từ nhánh main
echo ">> Đang chạy git pull origin main..."
git pull origin main

# 2. Xóa và tối ưu hóa bộ nhớ đệm Laravel
echo ">> Đang làm mới bộ nhớ đệm Laravel..."
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan cache:clear

# 3. Đồng bộ file tĩnh ra thư mục gốc (nếu dùng Document Root httpdocs)
if [ -d "public/css" ]; then
    echo ">> Đồng bộ tệp tĩnh CSS/JS..."
    cp -r public/css ./ 2>/dev/null || true
    cp -r public/js ./ 2>/dev/null || true
    cp public/favicon* ./ 2>/dev/null || true
fi

echo "=== Triển khai hoàn tất thành công! ==="
