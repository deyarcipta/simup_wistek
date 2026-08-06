@extends('admin.layouts.app')
@section('title', 'Kehadiran & Jam Kerja Operator')

@section('content')
<div class="row">
    {{-- STATS HEADER --}}
    <div class="col-12 mb-4">
        <div class="card bg-primary text-white shadow-sm">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <h5 class="text-white-50 mb-1">Kehadiran & Jam Kerja Operator</h5>
                    <h2 class="text-white mb-0 fw-bold">Monitoring Presensi Shift</h2>
                    <p class="mb-0 mt-1 text-white-50" style="font-size: 0.85rem;">
                        Memantau ketepatan waktu check-in dan waktu check-out operator UP.
                    </p>
                </div>
                <div class="d-none d-md-block" style="font-size: 4rem; opacity: 0.25;">
                    <i class="bx bx-user-check"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER & LIST CARD --}}
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header pb-2">
                <div class="row g-3 justify-content-between align-items-center">
                    <div class="col-12 col-md-4">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-time-five me-1"></i> Log Kehadiran Shift</h5>
                    </div>
                    
                    {{-- Form Pencarian & Filter --}}
                    <div class="col-12 col-md-8">
                        <form method="GET" action="{{ route('admin.kehadiran.index') }}" class="row g-2 justify-content-md-end align-items-center">
                            <div class="col-12 col-sm-6 col-md-5">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                                    <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm" placeholder="Cari nama operator...">
                                </div>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4">
                                <select name="shift_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">-- Semua Shift --</option>
                                    @foreach($shifts as $s)
                                        <option value="{{ $s->id }}" {{ $shiftFilter == $s->id ? 'selected' : '' }}>
                                            {{ $s->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-sm-2 col-md-2 d-grid">
                                <button type="submit" class="btn btn-sm btn-primary shadow-sm">Cari</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%">No</th>
                                <th style="width: 15%">Tanggal</th>
                                <th style="width: 25%">Operator</th>
                                <th style="width: 15%">Shift</th>
                                <th style="width: 13%">Check-In</th>
                                <th style="width: 13%">Check-Out</th>
                                <th style="width: 14%">Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($details as $index => $detail)
                            @php
                                $logbook = $detail->logbook;
                                $lateness = $detail->getLatenessInfo();
                                
                                // Tentukan jam checkin
                                $checkinTime = $detail->waktu_mulai ?? ($detail->shift_id == 1 ? $logbook->created_at : $detail->created_at);

                                // Tentukan jam checkout
                                $checkoutTime = null;
                                $isCheckedOut = false;
                                
                                if ($detail->shift_id == 1) {
                                    // Shift 1 selalu selesai/checkout saat LogbookDetail dibuat
                                    $checkoutTime = $detail->created_at;
                                    $isCheckedOut = true;
                                } elseif ($detail->shift_id == 2) {
                                    // Shift 2 checkout saat logbook status 'shift_2_selesai' atau 'tutup_up'
                                    if (in_array($logbook->status, ['shift_2_selesai', 'tutup_up'])) {
                                        $checkoutTime = $detail->updated_at;
                                        $isCheckedOut = true;
                                    }
                                } elseif ($detail->shift_id == 3) {
                                    // Shift 3 checkout saat logbook status 'tutup_up'
                                    if ($logbook->status === 'tutup_up') {
                                        $checkoutTime = $detail->updated_at;
                                        $isCheckedOut = true;
                                    }
                                }
                            @endphp
                            <tr>
                                <td>{{ $details->firstItem() + $index }}</td>
                                <td class="fw-semibold text-dark">
                                    {{ $logbook->tanggal->translatedFormat('d F Y') }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($detail->user->foto)
                                            <img src="{{ asset('storage/photos/' . $detail->user->foto) }}" alt="Foto Profile" class="rounded-circle" width="32" height="32" style="object-fit: cover;">
                                        @else
                                            <img src="{{ asset('img/avatars/1.png') }}" alt="Foto Default" class="rounded-circle" width="32">
                                        @endif
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold text-dark">{{ $detail->user->name }}</span>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $detail->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-label-primary">
                                        {{ $detail->shift->nama_shift }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">
                                        {{ $checkinTime ? $checkinTime->format('H:i') : '-' }}
                                    </div>
                                    <small class="text-muted" style="font-size: 0.72rem;">WIB</small>
                                </td>
                                <td>
                                    @if($isCheckedOut && $checkoutTime)
                                        <div class="fw-bold text-dark">
                                            {{ $checkoutTime->format('H:i') }}
                                        </div>
                                        <small class="text-muted" style="font-size: 0.72rem;">WIB</small>
                                    @else
                                        <span class="badge bg-label-warning"><i class="bx bx-loader animate-spin me-1"></i> Sedang Bertugas</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $lateness['badge_class'] }}">
                                        <i class="bx bx-time me-1"></i> {{ $lateness['status_text'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="bx bx-calendar-x mb-2" style="font-size: 3rem;"></i>
                                    <p class="mb-0">Tidak ada log kehadiran operator ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <small class="text-muted">
                        Menampilkan {{ $details->firstItem() ?? 0 }} s.d. {{ $details->lastItem() ?? 0 }} dari {{ $details->total() }} data
                    </small>
                    <div>
                        {{ $details->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
