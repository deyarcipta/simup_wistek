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
        Schema::create('logbooks', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->decimal('kas_awal', 15, 2);
            $table->decimal('kas_akhir', 15, 2)->nullable();
            $table->enum('stok_kertas', ['Aman', 'Habis'])->nullable();
            $table->text('status_mesin')->nullable();
            $table->enum('status', ['aktif', 'shift_1_selesai', 'tutup_up'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbooks');
    }
};
