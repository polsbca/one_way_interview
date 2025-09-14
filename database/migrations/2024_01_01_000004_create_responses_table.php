<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->text('video_path')->nullable();
            $table->text('text_response')->nullable();
            $table->integer('duration')->nullable(); // in seconds
            $table->integer('file_size')->nullable(); // in bytes
            $table->integer('attempt_number')->default(1);
            $table->enum('status', ['pending', 'recording', 'uploaded', 'failed'])->default('pending');
            $table->timestamp('recorded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('responses');
    }
};
