<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PremierLeagueService;
use Illuminate\Support\Facades\Log;

/**
 * Command: php artisan epl:warm-cache
 *
 * Tự động làm nóng bộ nhớ đệm (Pre-warm Cache) cho toàn bộ các trận đấu trong Sitemap.
 * Giúp mọi URL trận đấu (kể cả 100+ trận sắp đá) phản hồi ngay lập tức trong 5-15ms,
 * triệt tiêu hoàn toàn cảnh báo "Slow response times" / "longLoadingTime" từ Google và XOVI.
 */
class WarmMatchCache extends Command
{
    protected $signature = 'epl:warm-cache {--limit=150 : Số lượng trận đấu tối đa cần làm nóng cache}';
    protected $description = 'Làm nóng bộ nhớ đệm (Pre-warm Cache) cho toàn bộ trận đấu để tăng tốc độ tải trang';

    public function handle(PremierLeagueService $plService): int
    {
        $limit = (int) $this->option('limit');
        $this->info("Đang bắt đầu làm nóng cache cho tối đa {$limit} trận đấu...");

        try {
            $seasonId = $plService->getCurrentCompSeason()['id'] ?? null;
            $fixtures = $plService->getFixtures(null, $seasonId);

            if (empty($fixtures)) {
                $this->warn('Không tìm thấy danh sách trận đấu nào.');
                return Command::SUCCESS;
            }

            $count = 0;
            $total = min(count($fixtures), $limit);
            $bar = $this->output->createProgressBar($total);
            $bar->start();

            foreach ($fixtures as $fixture) {
                if ($count >= $limit) {
                    break;
                }

                $matchId = (int) ($fixture['id'] ?? 0);
                if ($matchId > 0) {
                    // Gọi nạp dữ liệu chi tiết vào cache nếu chưa có
                    $plService->getMatchDetail($matchId);
                    $count++;
                    $bar->advance();
                }
            }

            $bar->finish();
            $this->newLine();
            $this->info("✓ Đã làm nóng thành công cache cho {$count} trận đấu!");
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Lỗi khi làm nóng cache: ' . $e->getMessage());
            Log::error('Lỗi epl:warm-cache: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
