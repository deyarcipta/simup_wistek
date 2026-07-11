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
        Schema::table('logbook_details', function (Blueprint $table) {
            $table->decimal('pendapatan_lain', 15, 2)->default(0)->after('total_uang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logbook_details', function (Blueprint $table) {
            $table->dropColumn('pendapatan_lain');
        });
    }
};
