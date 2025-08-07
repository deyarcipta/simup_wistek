<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengaturan;

class PengaturanSeeder extends Seeder
{
    public function run(): void
    {
        Pengaturan::create([
            'nama_aplikasi' => 'SIMUP Wistek',
            'nama_sekolah' => 'SMK Wisata Indonesia',
            'alamat' => 'Jl. Lenteng Agung Raya Gg. Langgar No.1, RT.9/RW.3, Kebagusan, Ps. Minggu, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12520',
            'telepon' => '085172331507',
            'email' => 'wistinteknologi@gmail.com',
            'logo' => null,
        ]);
    }
}
