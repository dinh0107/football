<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model quản lý Bình chọn Dự đoán kết quả trận đấu (Community Match Voting)
 */
class MatchVote extends Model
{
    use HasFactory;

    protected $table = 'match_votes';

    protected $fillable = [
        'match_id',
        'ip_address',
        'choice',
    ];

    protected $casts = [
        'match_id' => 'integer',
    ];

    /**
     * Thống kê tỷ lệ phần trăm bình chọn và trạng thái vote của người dùng
     *
     * @param int $matchId
     * @param string|null $userIp
     * @return array
     */
    public static function getStatsForMatch(int $matchId, ?string $userIp = null): array
    {
        try {
            $votes = static::where('match_id', $matchId)->get();
            $total = $votes->count();

            $homeCount = $votes->where('choice', 'home')->count();
            $drawCount = $votes->where('choice', 'draw')->count();
            $awayCount = $votes->where('choice', 'away')->count();

            // Chưa có lượt bình chọn thì hiện 0%, không hiện tỷ lệ giả
            $homePercent = $total > 0 ? round($homeCount / $total * 100, 1) : 0.0;
            $drawPercent = $total > 0 ? round($drawCount / $total * 100, 1) : 0.0;
            $awayPercent = $total > 0 ? round($awayCount / $total * 100, 1) : 0.0;

            $userVote = null;
            if ($userIp) {
                $userVote = $votes->firstWhere('ip_address', $userIp);
            }

            return [
                'match_id' => $matchId,
                'total_votes' => $total,
                'home' => [
                    'count' => $homeCount,
                    'percent' => $homePercent,
                ],
                'draw' => [
                    'count' => $drawCount,
                    'percent' => $drawPercent,
                ],
                'away' => [
                    'count' => $awayCount,
                    'percent' => $awayPercent,
                ],
                'has_voted' => $userVote !== null,
                'user_choice' => $userVote?->choice,
            ];
        } catch (\Throwable $e) {
            return [
                'match_id' => $matchId,
                'total_votes' => 0,
                'home' => ['count' => 0, 'percent' => 0.0],
                'draw' => ['count' => 0, 'percent' => 0.0],
                'away' => ['count' => 0, 'percent' => 0.0],
                'has_voted' => false,
                'user_choice' => null,
            ];
        }
    }
}
