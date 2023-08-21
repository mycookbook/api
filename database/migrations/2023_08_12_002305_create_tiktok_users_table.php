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
        Schema::create('tiktok_users', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->text('open_id')->nullable();
            $table->boolean('is_verified')->default(0);
            $table->text('profile_deep_link')->nullable();
            $table->text('bio_description')->nullable();
            $table->text('display_name')->nullable();
            $table->text('avatar_large_url')->nullable();
            $table->text('avatar_url_100')->nullable();
            $table->text('avatar_url')->nullable();
            $table->text('union_id')->nullable();
            $table->integer('video_count')->default(0);
            $table->text('videos')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiktok_users');
    }
};
