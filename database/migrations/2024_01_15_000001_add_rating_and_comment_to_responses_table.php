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
        Schema::table('responses', function (Blueprint $table) {
            $table->integer('rating')->nullable()->after('recorded_at');
            $table->text('comment')->nullable()->after('rating');
            $table->foreignId('reviewed_by')->nullable()->after('comment')->constrained('users');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            $table->dropColumn(['rating', 'comment', 'reviewed_by', 'reviewed_at']);
        });
    }
};
