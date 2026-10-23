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
        $isWww = $host && str_starts_with(strtolower($host), 'www.');
        // Production luôn HTTPS: gộp http -> https và www -> non-www thành 1 lần 301
        $scheme = ($request->isSecure() || app()->environment('production')) ? 'https' : 'http';

        if ($host && ($isWww || $scheme !== $request->getScheme())) {
            $targetHost = $isWww ? substr($host, 4) : $host;
            return redirect()->away("{$scheme}://{$targetHost}" . $request->getRequestUri(), 301);
        }

        // Tự động chuyển hướng /index.php, /index.html, /index.htm về root / (301 Permanent Redirect)
        // Kiểm tra cả raw REQUEST_URI (trên web server IIS/Apache thực tế) và $request->path() (môi trường testing)
        $rawUri = $request->server('REQUEST_URI') ?? $request->getRequestUri();
        $uriPath = parse_url($rawUri, PHP_URL_PATH) ?? '/';
        $pathLower = strtolower($uriPath);

        if (in_array($pathLower, ['/index.php', '/index.html', '/index.htm', '/index.php/'])) {
            $queryString = $request->getQueryString();
            $targetUrl = '/' . ($queryString ? "?{$queryString}" : '');
            return redirect()->to($targetUrl, 301);
        }

        if (preg_match('#^/index\.php(/.*)$#i', $uriPath, $matches)) {
            $queryString = $request->getQueryString();
            $subPath = $matches[1] ?: '/';
            $targetUrl = $subPath . ($queryString ? "?{$queryString}" : '');
            return redirect()->to($targetUrl, 301);
        }

        $path = strtolower($request->path());
        if (in_array($path, ['index.php', 'index.html', 'index.htm'])) {
            $queryString = $request->getQueryString();
            $targetUrl = '/' . ($queryString ? "?{$queryString}" : '');
            return redirect()->to($targetUrl, 301);
        }

        return $next($request);
    }
}
