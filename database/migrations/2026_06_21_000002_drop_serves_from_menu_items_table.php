<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('menu_items', 'serves')) {
            return;
        }

        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('serves');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('menu_items', 'serves')) {
            return;
        }

        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('serves')->nullable()->after('price');
        });
    }
};
