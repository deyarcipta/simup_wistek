<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // Hapus foreign key lama yang masih mengacu ke users
            $table->dropForeign(['member_id']);

            // Tambahkan foreign key baru ke tabel members
            $table->foreign('member_id')
                  ->references('id')->on('members')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // Balikin lagi ke users kalau rollback
            $table->dropForeign(['member_id']);

            $table->foreign('member_id')
                  ->references('id')->on('users')
                  ->onDelete('set null');
        });
    }
};
