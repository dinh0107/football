<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: Tự động chuyển hướng WWW sang Non-WWW (301 Permanent Redirect)
 * Đảm bảo Google và các công cụ tìm kiếm chỉ lập chỉ mục 1 phiên bản tên miền duy nhất (tysongoaihang.site),
 * triệt tiêu hoàn toàn vấn đề phân tán sức mạnh SEO (Duplicate domain / WWW vs non-WWW).
 */
class RedirectWwwToNonWww
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->header('host');

        if ($host && str_starts_with(strtolower($host), 'www.')) {
            $nonWwwHost = substr($host, 4);
            $scheme = $request->isSecure() ? 'https' : $request->getScheme();
            $targetUrl = "{$scheme}://{$nonWwwHost}" . $request->getRequestUri();

            return redirect()->away($targetUrl, 301);
        }

        // Tự động chuyển hướng /index.php, /index.html, /index.htm về root / (301 Permanent Redirect)
        $path = strtolower($request->path());
        if (in_array($path, ['index.php', 'index.html', 'index.htm'])) {
            $queryString = $request->getQueryString();
            $targetUrl = '/' . ($queryString ? "?{$queryString}" : '');
            return redirect()->to($targetUrl, 301);
        }

        return $next($request);
    }
}
