<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Model quản lý Vị trí đặt banner quảng cáo (Đầu trang, Dưới tỷ số, Chân trang)
 */
class AdBanner extends Model
{
    protected $table = 'ad_banners';

    protected $fillable = [
        'position',
        'title',
        'image_url',
        'target_url',
        'is_active',
        'html_code',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Lấy banner đang kích hoạt theo vị trí (có cache để tối ưu chịu tải cao)
     *
     * @param string $position
     * @return static|null
     */
    public static function getActiveBanner(string $position): ?static
    {
        try {
            return Cache::remember("ad_banner_{$position}", 3600, function () use ($position) {
                return static::where('position', $position)
                    ->where('is_active', true)
                    ->latest()
                    ->first();
            });
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Render mã HTML hiển thị banner quảng cáo
     */
    public function getRenderedHtmlAttribute(): ?string
    {
        if (!empty($this->html_code)) {
            return $this->html_code;
        }

        if (!empty($this->image_url)) {
            $img = sprintf(
                '<img src="%s" alt="%s" style="max-width: 100%%; height: auto; border-radius: 12px; display: block; margin: 0 auto; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">',
                e($this->image_url),
                e($this->title ?? 'Quảng Cáo')
            );

            if (!empty($this->target_url)) {
                return sprintf(
                    '<a href="%s" target="_blank" rel="noopener noreferrer sponsored" style="display: inline-block; max-width: 100%%;">%s</a>',
                    e($this->target_url),
                    $img
                );
            }

            return $img;
        }

        return null;
    }
}
