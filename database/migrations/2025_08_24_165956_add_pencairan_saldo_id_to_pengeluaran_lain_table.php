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
            $table->unsignedBigInteger('pencairan_id')->nullable()->after('member_id');
            $table->foreign('pencairan_id')->references('id')->on('pencairan_saldo')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengeluaran_lain', function (Blueprint $table) {
            if (Schema::hasColumn('pengeluaran_lain', 'pencairan_id')) {
                $table->dropForeign(['pencairan_id']);
                $table->dropColumn('pencairan_id');
            }
        });
    }
};
