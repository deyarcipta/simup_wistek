<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $fillable = [
        'nama_aplikasi',
        'nama_sekolah',
        'alamat',
        'telepon',
        'email',
        'logo',
        'shu_pembagian',
        'jumlah_shift',
        'toleransi_keterlambatan',
        'shift1_mulai',
        'shift2_mulai',
        'shift3_mulai',
    ];

    protected $casts = [
        'shu_pembagian' => 'array',
        'toleransi_keterlambatan' => 'integer',
    ];

    public function getShuPembagianOrDefault()
    {
        return $this->shu_pembagian ?? [
            ['penerima' => 'Jurusan TKJ',   'persentase' => 40],
            ['penerima' => 'Unit Produksi', 'persentase' => 30],
            ['penerima' => 'Sekolah',       'persentase' => 20],
            ['penerima' => 'Honor Pegawai', 'persentase' => 10],
        ];
    }

    public function getShiftSchedules()
    {
        $jumlahShift = $this->jumlah_shift ?? 2;
        $s1Mulai = $this->shift1_mulai ?? '07:00';
        $s2Mulai = $this->shift2_mulai ?? ($jumlahShift == 3 ? '09:30' : '11:00');
        $s3Mulai = $this->shift3_mulai ?? '12:00';

        if ($jumlahShift == 1) {
            return [
                1 => [
                    'nama' => 'Shift 1 Pagi',
                    'mulai' => $s1Mulai,
                    'selesai' => '15:00',
                    'deskripsi' => 'Sedang Berjalan - Jam Tutup UP: 15.00'
                ]
            ];
        } elseif ($jumlahShift == 2) {
            return [
                1 => [
                    'nama' => 'Shift 1 Pagi',
                    'mulai' => $s1Mulai,
                    'selesai' => $s2Mulai,
                    'deskripsi' => 'Sedang Berjalan - Batas Akhir Jam ' . $s2Mulai
                ],
                2 => [
                    'nama' => 'Shift 2 Siang',
                    'mulai' => $s2Mulai,
                    'selesai' => '15:00',
                    'deskripsi' => 'Sedang Berjalan - Jam Tutup UP: 15.00'
                ]
            ];
        } else { // 3 shifts
            return [
                1 => [
                    'nama' => 'Shift 1 Pagi',
                    'mulai' => $s1Mulai,
                    'selesai' => $s2Mulai,
                    'deskripsi' => 'Sedang Berjalan - Batas Akhir Jam ' . $s2Mulai
                ],
                2 => [
                    'nama' => 'Shift 2 Siang',
                    'mulai' => $s2Mulai,
                    'selesai' => $s3Mulai,
                    'deskripsi' => 'Sedang Berjalan - Batas Akhir Jam ' . $s3Mulai
                ],
                3 => [
                    'nama' => 'Shift 3 Sore',
                    'mulai' => $s3Mulai,
                    'selesai' => '15:00',
                    'deskripsi' => 'Sedang Berjalan - Jam Tutup UP: 15.00'
                ]
            ];
        }
    }
}

