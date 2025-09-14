<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('jobs')->onDelete('cascade');
            $table->text('question_text');
            $table->enum('type', ['video', 'text'])->default('video');
            $table->integer('time_limit')->default(120); // in seconds
            $table->integer('max_attempts')->default(3);
            $table->integer('order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->text('instructions')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
