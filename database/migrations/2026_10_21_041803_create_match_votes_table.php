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
        Schema::create('match_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id')->index();
            $table->string('ip_address', 45);
            $table->enum('choice', ['home', 'draw', 'away']);
            $table->timestamps();

            $table->unique(['match_id', 'ip_address'], 'unique_match_ip_vote');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_votes');
    }
};
