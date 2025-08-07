<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('produk_jasa', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('jenis', ['produk', 'jasa']);
            $table->decimal('harga', 15, 2);
            $table->integer('jumlah')->nullable(); // hanya untuk produk
            $table->string('satuan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_jasa');
    }
};
