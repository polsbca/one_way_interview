<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users');
            $table->integer('overall_rating')->nullable(); // 1-5 scale
            $table->text('comments')->nullable();
            $table->text('feedback')->nullable();
            $table->enum('decision', ['pending', 'approved', 'rejected', 'hold'])->default('pending');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
