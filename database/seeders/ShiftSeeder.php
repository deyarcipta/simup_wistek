<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shift::updateOrCreate(
            ['id' => 1],
            [
                'nama_shift' => 'Shift 1 Pagi',
                'jam_mulai' => '07:00:00',
                'jam_selesai' => '11:00:00',
            ]
        );

        Shift::updateOrCreate(
            ['id' => 2],
            [
                'nama_shift' => 'Shift 2 Siang',
                'jam_mulai' => '11:00:00',
                'jam_selesai' => '15:00:00',
            ]
        );
    }
}
