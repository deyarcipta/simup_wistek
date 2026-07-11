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
        // 1. Hapus FK & Kolom dari tabel pengeluaran_lain
        Schema::table('pengeluaran_lain', function (Blueprint $table) {
            if (Schema::hasColumn('pengeluaran_lain', 'pencairan_id')) {
                $table->dropForeign(['pencairan_id']);
                $table->dropColumn('pencairan_id');
            }
            if (Schema::hasColumn('pengeluaran_lain', 'member_id')) {
                // Check if the foreign key exists, otherwise drop column anyway
                try {
                    $table->dropForeign(['member_id']);
                } catch (\Exception $e) {}
                $table->dropColumn('member_id');
            }
        });

        // 2. Hapus FK & Kolom dari tabel transaksi
        Schema::table('transaksi', function (Blueprint $table) {
            if (Schema::hasColumn('transaksi', 'member_id')) {
                try {
                    $table->dropForeign(['member_id']);
                } catch (\Exception $e) {}
                $table->dropColumn('member_id');
            }
            if (Schema::hasColumn('transaksi', 'bonus')) {
                $table->dropColumn('bonus');
            }
        });

        // 3. Drop tabel pencairan_saldo
        Schema::dropIfExists('pencairan_saldo');

        // 4. Drop tabel members
        Schema::dropIfExists('members');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed as this is a permanent removal.
    }
};
