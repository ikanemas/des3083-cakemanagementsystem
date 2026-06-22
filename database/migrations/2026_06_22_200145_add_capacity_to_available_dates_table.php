<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('available_dates', function (Blueprint $table) {
            // Adds a capacity column. You can change the default number (e.g., 5 orders per day)
            $table->integer('capacity')->default(5)->after('is_available');
        });
    }

    public function down(): void
    {
        Schema::table('available_dates', function (Blueprint $table) {
            $table->dropColumn('capacity');
        });
    }
};