<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_admin')->default(false)->after('password');
            });
        }

        // Bảng ghi đè thông tin kênh phát sóng TV Guide cho trận đấu
        if (!Schema::hasTable('match_overrides')) {
            Schema::create('match_overrides', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('match_id')->unique();
                $table->string('tv_channel', 100);
                $table->string('notes', 255)->nullable();
                $table->timestamps();
            });
        }

        // Bảng quản lý banner quảng cáo tại các vị trí (banner_top, banner_below_score, banner_footer)
        if (!Schema::hasTable('ad_banners')) {
            Schema::create('ad_banners', function (Blueprint $table) {
                $table->id();
                $table->string('position', 50)->index(); // banner_top, banner_below_score, banner_footer
                $table->string('title', 150);
                $table->string('image_url', 500)->nullable();
                $table->string('target_url', 500)->nullable();
                $table->boolean('is_active')->default(true);
                $table->text('html_code')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_banners');
        Schema::dropIfExists('match_overrides');

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_admin');
            });
        }
    }
};
