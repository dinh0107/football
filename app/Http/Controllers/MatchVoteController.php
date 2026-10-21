<?php

namespace App\Http\Controllers;

use App\Models\MatchVote;
use App\Helpers\FootballSeoHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Controller xử lý tính năng Bình chọn Dự đoán kết quả trận đấu (Community Match Voting)
 */
class MatchVoteController extends Controller
{
    /**
     * Gửi lượt bình chọn dự đoán (1 IP chỉ vote 1 lần mỗi trận)
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function vote(Request $request, int|string $id): JsonResponse
    {
        $matchId = FootballSeoHelper::extractIdFromSlug((string) $id);
        if (!$matchId) {
            return response()->json([
                'success' => false,
                'message' => 'Mã trận đấu không hợp lệ.',
            ], 400);
        }

        $validated = $request->validate([
            'choice' => 'required|in:home,draw,away',
        ]);

        $ipAddress = (string) $request->ip();

        // Kiểm tra xem IP này đã bình chọn cho trận này chưa
        $existingVote = MatchVote::where('match_id', $matchId)
            ->where('ip_address', $ipAddress)
            ->first();

        if ($existingVote) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã bình chọn cho trận đấu này rồi.',
                'stats' => MatchVote::getStatsForMatch($matchId, $ipAddress),
            ], 409);
        }

        // Tạo bình chọn mới
        try {
            MatchVote::create([
                'match_id' => $matchId,
                'ip_address' => $ipAddress,
                'choice' => $validated['choice'],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã bình chọn cho trận đấu này rồi.',
                'stats' => MatchVote::getStatsForMatch($matchId, $ipAddress),
            ], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'Bình chọn dự đoán thành công!',
            'stats' => MatchVote::getStatsForMatch($matchId, $ipAddress),
        ], 200);
    }

    /**
     * Lấy thống kê tỷ lệ phần trăm bình chọn của trận đấu
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function stats(Request $request, int|string $id): JsonResponse
    {
        $matchId = FootballSeoHelper::extractIdFromSlug((string) $id);
        if (!$matchId) {
            return response()->json([
                'error' => 'Mã trận đấu không hợp lệ.',
            ], 400);
        }

        $ipAddress = (string) $request->ip();
        $stats = MatchVote::getStatsForMatch($matchId, $ipAddress);

        return response()->json($stats);
    }
}
