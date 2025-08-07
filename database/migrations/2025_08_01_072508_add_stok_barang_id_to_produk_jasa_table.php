<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('produk_jasa', function (Blueprint $table) {
            $table->foreignId('stok_barang_id')->nullable()->constrained('stok_barang')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('produk_jasa', function (Blueprint $table) {
            $table->dropForeign(['stok_barang_id']);
            $table->dropColumn('stok_barang_id');
        });
    }
};
