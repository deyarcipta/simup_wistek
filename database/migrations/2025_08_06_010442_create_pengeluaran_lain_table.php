<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('pengeluaran_lain', function (Blueprint $table) {
            $table->id();
            $table->string('keterangan');
            $table->decimal('total', 15, 2);
            $table->date('tanggal');
            $table->foreignId('piutang_id')->nullable()->constrained('piutang')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran_lain');
    }
};
