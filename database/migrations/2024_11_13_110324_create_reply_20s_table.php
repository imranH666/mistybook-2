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
        Schema::create('reply_20s', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('post_id')->nullable();
            $table->integer('blog_id')->nullable();
            $table->integer('video_id')->nullable();
            $table->integer('comment_id');
            $table->text('reply');
            $table->integer('status')->default(0);
            $table->integer('reply10')->default(0);
            $table->integer('react')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reply_20s');
    }
};
