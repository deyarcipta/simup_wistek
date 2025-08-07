<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stok_barang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang');
            $table->string('satuan')->nullable(); // pcs, box, dll
            $table->integer('stok')->default(0);
            $table->integer('harga_beli')->nullable();
            $table->integer('harga_jual')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_barang');
    }
};
