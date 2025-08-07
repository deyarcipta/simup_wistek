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
        Schema::table('pengeluaran_lain', function (Blueprint $table) {
            $table->foreignId('piutang_id')->nullable()->after('id')->constrained('piutang')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pengeluaran_lain', function (Blueprint $table) {
            $table->dropForeign(['piutang_id']);
            $table->dropColumn('piutang_id');
        });
    }
};
