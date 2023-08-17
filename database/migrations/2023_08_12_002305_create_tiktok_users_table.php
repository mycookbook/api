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
            $table->integer('user_id');
            $table->text('open_id');
            $table->text('code');
            $table->boolean('is_verified');
            $table->text('profile_deep_link');
            $table->text('bio_description');
            $table->text('display_name');
            $table->text('avatar_large_url');
            $table->text('avatar_url_100');
            $table->text('avatar_url');
            $table->text('union_id');
            $table->integer('video_count');
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
