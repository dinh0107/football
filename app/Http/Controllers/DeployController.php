<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Controller tự động triển khai (Auto Deployment Webhook)
 * Kích hoạt tự động git pull và dọn dẹp cache khi có commit mới push lên GitHub
 */
class DeployController extends Controller
{
    /**
     * Khóa bí mật mặc định (có thể cấu hình lại trong file .env bằng DEPLOY_SECRET)
     */
    protected function getSecret(): string
    {
        return env('DEPLOY_SECRET', 'dinh0107_football_secret_2026');
    }

    /**
     * Xử lý webhook tự động pull code
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deploy(Request $request): JsonResponse
    {
        $secret = $this->getSecret();

        // 1. Xác thực bảo mật: Hỗ trợ GitHub Signature (HMAC SHA-256), Header X-Deploy-Secret hoặc Query param ?secret=...
        $providedSecret = $request->header('X-Deploy-Secret') ?: $request->query('secret');
        $githubSignature = $request->header('X-Hub-Signature-256');

        $authorized = false;

        if ($providedSecret && hash_equals($secret, $providedSecret)) {
            $authorized = true;
        } elseif ($githubSignature) {
            $payload = $request->getContent();
            $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);
            if (hash_equals($expectedSignature, $githubSignature)) {
                $authorized = true;
            }
        }

        if (!$authorized) {
            Log::warning('Git Deploy Webhook: Truy cập trái phép từ IP: ' . $request->ip());
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: Sai mã bảo mật DEPLOY_SECRET.'
            ], 403);
        }

        $basePath = base_path();
        $outputs = [];

        // 2. Danh sách lệnh triển khai tự động
        $commands = [
            'git pull origin main 2>&1',
            'php artisan config:clear 2>&1',
            'php artisan view:clear 2>&1',
            'php artisan route:clear 2>&1',
            'php artisan cache:clear 2>&1',
        ];

        // 3. Thực thi từng lệnh trong thư mục gốc dự án
        $oldCwd = getcwd();
        chdir($basePath);

        foreach ($commands as $cmd) {
            $output = [];
            $exitCode = 0;
            exec($cmd, $output, $exitCode);
            $outputs[$cmd] = [
                'exit_code' => $exitCode,
                'output' => implode("\n", $output)
            ];
        }

        // Tự động đồng bộ các file tĩnh ra thư mục gốc nếu chạy chế độ root hosting
        if (is_dir($basePath . '/public/css')) {
            @copy($basePath . '/public/css/app.css', $basePath . '/css/app.css');
        }

        chdir($oldCwd);

        // 4. Ghi log kết quả triển khai
        $logMessage = "[" . date('Y-m-d H:i:s') . "] Auto Deploy triggered by " . $request->ip() . "\n" . json_encode($outputs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        @file_put_contents($basePath . '/storage/logs/deploy.log', $logMessage . "\n----------------------------------------\n", FILE_APPEND);

        Log::info('Git Deploy Webhook: Triển khai thành công', ['ip' => $request->ip()]);

        return response()->json([
            'status' => 'success',
            'message' => 'Triển khai mã nguồn mới và làm mới cache thành công!',
            'timestamp' => now()->toIso8601String(),
            'details' => $outputs,
        ]);
    }
}
