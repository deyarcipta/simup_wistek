<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengeluaran_lain', function (Blueprint $table) {
            if (!Schema::hasColumn('pengeluaran_lain', 'member_id')) {
                $table->unsignedBigInteger('member_id')->nullable()->after('id');
            }

            // tambahkan FK hanya kalau belum ada
            $table->foreign('member_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pengeluaran_lain', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->dropColumn('member_id');
        });
    }
};
