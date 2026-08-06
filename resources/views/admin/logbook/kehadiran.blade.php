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
                        Memantau ketepatan waktu check-in, check-out, ekspor data, serta rekapitulasi shift operator.
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
                    
                    {{-- Form Pencarian, Filter & Ekspor --}}
                    <div class="col-12 col-md-8">
                        <div class="d-flex flex-wrap gap-2 justify-content-md-end align-items-center">
                            <form method="GET" action="{{ route('admin.kehadiran.index') }}" class="d-flex gap-2 align-items-center flex-grow-1 flex-md-grow-0 mb-0">
                                <div style="max-width: 200px;">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                                        <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm" placeholder="Cari nama operator...">
                                    </div>
                                </div>
                                <div style="min-width: 150px;">
                                    <select name="shift_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="">-- Semua Shift --</option>
                                        @foreach($shifts as $s)
                                            <option value="{{ $s->id }}" {{ $shiftFilter == $s->id ? 'selected' : '' }}>
                                                {{ $s->nama_shift }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary shadow-sm">Cari</button>
                            </form>
                            
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.kehadiran.download-pdf', ['search' => $search, 'shift_id' => $shiftFilter]) }}" class="btn btn-sm btn-danger shadow-sm">
                                    <i class="bx bxs-file-pdf me-1"></i> PDF
                                </a>
                                <a href="{{ route('admin.kehadiran.download-excel', ['search' => $search, 'shift_id' => $shiftFilter]) }}" class="btn btn-sm btn-success shadow-sm">
                                    <i class="bx bxs-spreadsheet me-1"></i> Excel/CSV
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                {{-- TAB NAVIGATION --}}
                <ul class="nav nav-tabs mb-4" id="kehadiranTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail-pane" type="button" role="tab" aria-controls="detail-pane" aria-selected="true">
                            <i class="bx bx-list-ul me-1"></i> Detail Presensi Shift
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="rekap-tab" data-bs-toggle="tab" data-bs-target="#rekap-pane" type="button" role="tab" aria-controls="rekap-pane" aria-selected="false">
                            <i class="bx bx-user-check me-1"></i> Rekap Per Operator
                        </button>
                    </li>
                </ul>

                <div class="tab-content p-0" id="kehadiranTabsContent">
                    {{-- TAB 1: DETAIL --}}
                    <div class="tab-pane fade show active" id="detail-pane" role="tabpanel" aria-labelledby="detail-tab">
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
                                            $checkoutTime = $detail->created_at;
                                            $isCheckedOut = true;
                                        } elseif ($detail->shift_id == 2) {
                                            if (in_array($logbook->status, ['shift_2_selesai', 'tutup_up'])) {
                                                $checkoutTime = $detail->updated_at;
                                                $isCheckedOut = true;
                                            }
                                        } elseif ($detail->shift_id == 3) {
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

                    {{-- TAB 2: REKAP PER OPERATOR --}}
                    <div class="tab-pane fade" id="rekap-pane" role="tabpanel" aria-labelledby="rekap-tab">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th style="width: 35%">Nama Operator</th>
                                        <th style="width: 20%">Jumlah Shift Dijaga</th>
                                        <th style="width: 40%">Tanggal Menjaga</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($rekapOperator as $userId => $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($item['user']->foto)
                                                    <img src="{{ asset('storage/photos/' . $item['user']->foto) }}" alt="Foto Profile" class="rounded-circle" width="32" height="32" style="object-fit: cover;">
                                                @else
                                                    <img src="{{ asset('img/avatars/1.png') }}" alt="Foto Default" class="rounded-circle" width="32">
                                                @endif
                                                <div class="d-flex flex-column">
                                                    <span class="fw-semibold text-dark">{{ $item['user']->name }}</span>
                                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $item['user']->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-info fw-bold" style="font-size: 0.85rem;">
                                                {{ $item['jumlah_shift'] }} Shift
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted fw-semibold" style="font-size: 0.85rem;">
                                                {{ $item['tanggal_menjaga'] ?: '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">
                                            <i class="bx bx-user-x mb-2" style="font-size: 3rem;"></i>
                                            <p class="mb-0">Tidak ada rekap operator ditemukan.</p>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
