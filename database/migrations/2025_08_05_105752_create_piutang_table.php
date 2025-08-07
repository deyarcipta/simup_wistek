<?php

// database/migrations/xxxx_xx_xx_create_piutang_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePiutangTable extends Migration
{
    public function up()
    {
        Schema::create('piutang', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_peminjaman');
            $table->string('nama_barang');
            $table->integer('jumlah_barang');
            $table->decimal('nominal', 15, 2);
            $table->string('kepada');
            $table->decimal('sisa_nominal', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('piutang');
    }
}
