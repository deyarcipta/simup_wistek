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
        Schema::table('pengaturan', function (Blueprint $table) {
            if (!Schema::hasColumn('pengaturan', 'toleransi_keterlambatan')) {
                $table->integer('toleransi_keterlambatan')->default(15)->after('jumlah_shift');
            }
            if (!Schema::hasColumn('pengaturan', 'shift1_mulai')) {
                $table->string('shift1_mulai')->default('07:00')->after('toleransi_keterlambatan');
            }
            if (!Schema::hasColumn('pengaturan', 'shift2_mulai')) {
                $table->string('shift2_mulai')->default('11:00')->after('shift1_mulai');
            }
            if (!Schema::hasColumn('pengaturan', 'shift3_mulai')) {
                $table->string('shift3_mulai')->default('12:00')->after('shift2_mulai');
            }
        });

        Schema::table('logbook_details', function (Blueprint $table) {
            if (!Schema::hasColumn('logbook_details', 'waktu_mulai')) {
                $table->dateTime('waktu_mulai')->nullable()->after('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan', function (Blueprint $table) {
            $table->dropColumn(['toleransi_keterlambatan', 'shift1_mulai', 'shift2_mulai', 'shift3_mulai']);
        });

        Schema::table('logbook_details', function (Blueprint $table) {
            $table->dropColumn('waktu_mulai');
        });
    }
};
