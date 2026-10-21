<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model quản lý ghi đè kênh phát sóng TV Guide cho từng trận đấu từ Admin Dashboard
 */
class MatchOverride extends Model
{
    protected $table = 'match_overrides';

    protected $fillable = [
        'match_id',
        'tv_channel',
        'notes',
    ];

    protected $casts = [
        'match_id' => 'integer',
    ];
}
