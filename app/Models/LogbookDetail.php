<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogbookDetail extends Model
{
    protected $table = 'logbook_details';

    protected $fillable = [
        'logbook_id',
        'shift_id',
        'user_id',
        'waktu_mulai',
        'jumlah_print',
        'harga_print',
        'jumlah_fotokopi',
        'harga_fotokopi',
        'jumlah_jilid',
        'harga_jilid',
        'total_uang',
        'pendapatan_lain',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
    ];

    public function logbook()
    {
        return $this->belongsTo(Logbook::class, 'logbook_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the lateness status and metadata for this shift logbook detail.
     *
     * @return array
     */
    public function getLatenessInfo()
    {
        $time = $this->waktu_mulai ?? ($this->shift_id == 1 ? ($this->logbook?->created_at ?? $this->created_at) : $this->created_at);
        if (!$time) {
            return [
                'is_late' => false,
                'status_text' => 'Tepat Waktu',
                'badge_class' => 'bg-label-success',
            ];
        }

        $pengaturan = Pengaturan::first();
        $toleransi = $pengaturan ? ($pengaturan->toleransi_keterlambatan ?? 15) : 15;
        
        $scheduledStartStr = '07:00';
        if ($pengaturan) {
            if ($this->shift_id == 1) {
                $scheduledStartStr = $pengaturan->shift1_mulai ?? '07:00';
            } elseif ($this->shift_id == 2) {
                $scheduledStartStr = $pengaturan->shift2_mulai ?? '11:00';
            } elseif ($this->shift_id == 3) {
                $scheduledStartStr = $pengaturan->shift3_mulai ?? '12:00';
            } else {
                $scheduledStartStr = $this->shift?->jam_mulai ? substr($this->shift->jam_mulai, 0, 5) : '07:00';
            }
        } else {
            $scheduledStartStr = $this->shift?->jam_mulai ? substr($this->shift->jam_mulai, 0, 5) : '07:00';
        }

        try {
            $dateStr = $time->format('Y-m-d');
            $scheduledStart = \Carbon\Carbon::parse($dateStr . ' ' . $scheduledStartStr);
        } catch (\Exception $e) {
            return [
                'is_late' => false,
                'status_text' => 'Tepat Waktu',
                'badge_class' => 'bg-label-success',
            ];
        }

        if ($time->lte($scheduledStart)) {
            return [
                'is_late' => false,
                'status_text' => 'Tepat Waktu',
                'badge_class' => 'bg-label-success',
            ];
        }

        $diffInMinutes = $scheduledStart->diffInMinutes($time);

        if ($diffInMinutes <= $toleransi) {
            return [
                'is_late' => false,
                'status_text' => 'Tepat Waktu',
                'badge_class' => 'bg-label-success',
            ];
        }

        return [
            'is_late' => true,
            'status_text' => 'Terlambat ' . $diffInMinutes . ' menit',
            'badge_class' => 'bg-label-danger',
        ];
    }
}
