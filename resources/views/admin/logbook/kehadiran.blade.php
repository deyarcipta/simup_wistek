@extends('admin.layouts.app')
@section('title', 'Kehadiran & Jam Kerja Operator')

@section('content')
<div class="row">
    {{-- STATS HEADER --}}
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #696cff 0%, #4346e6 100%);">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <h5 class="text-white mb-1" style="opacity: 0.85;"><i class="bx bx-user-voice me-1"></i> Kehadiran & Jam Kerja Operator</h5>
                    <h2 class="text-white mb-2 fw-bold">Monitoring Presensi Shift</h2>
                    <p class="mb-0 text-white" style="font-size: 0.85rem; max-width: 600px; opacity: 0.9;">
                        Pantau ketepatan waktu check-in, jam check-out, status keterlambatan secara otomatis, serta lakukan rekapitulasi data dan ekspor laporan.
                    </p>
                </div>
                <div class="d-none d-md-block text-white" style="font-size: 5rem; opacity: 0.25; line-height: 1;">
                    <i class="bx bx-time-five"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER & LIST CARD --}}
    <div class="col-12">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header pb-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar bg-light-primary rounded p-1" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #e8e9ff;">
                        <i class="bx bx-calendar text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-dark">Data Log Presensi</h5>
                        <small class="text-muted">Kelola data kehadiran operator berdasarkan filter</small>
                    </div>
                </div>
                
                {{-- Form Pencarian & Filter --}}
                <div class="d-flex flex-wrap gap-2 justify-content-md-end align-items-center">
                    <form method="GET" action="{{ route('admin.kehadiran.index') }}" class="d-flex gap-2 align-items-center flex-grow-1 flex-md-grow-0 mb-0">
                        <div style="max-width: 220px;">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text text-muted"><i class="bx bx-search"></i></span>
                                <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Cari nama operator..." style="border-radius: 0.375rem;">
                            </div>
                        </div>
                        <div style="min-width: 170px;">
                            <select name="shift_id" class="form-select" onchange="this.form.submit()" style="border-radius: 0.375rem;">
                                <option value="">-- Semua Shift --</option>
                                @foreach($shifts as $s)
                                    <option value="{{ $s->id }}" {{ $shiftFilter == $s->id ? 'selected' : '' }}>
                                        {{ $s->nama_shift }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary px-3"><i class="bx bx-filter-alt me-1"></i> Cari</button>
                        @if($search || $shiftFilter)
                            <a href="{{ route('admin.kehadiran.index') }}" class="btn btn-outline-secondary px-2" title="Reset Filter"><i class="bx bx-refresh"></i></a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="card-body pt-1">
                {{-- TAB NAVIGATION (PILLS STYLE) --}}
                <ul class="nav nav-pills mb-4" id="kehadiranTabs" role="tablist" style="background-color: #f5f6ff; padding: 6px; border-radius: 0.5rem; display: inline-flex;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-semibold" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail-pane" type="button" role="tab" aria-controls="detail-pane" aria-selected="true" style="padding: 8px 20px; border-radius: 0.375rem;">
                            <i class="bx bx-list-ul me-1"></i> Detail Presensi Shift
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="rekap-tab" data-bs-toggle="tab" data-bs-target="#rekap-pane" type="button" role="tab" aria-controls="rekap-pane" aria-selected="false" style="padding: 8px 20px; border-radius: 0.375rem;">
                            <i class="bx bx-user-check me-1"></i> Rekap Per Operator
                        </button>
                    </li>
                </ul>

                <div class="tab-content p-0" id="kehadiranTabsContent">
                    {{-- TAB 1: DETAIL --}}
                    <div class="tab-pane fade show active" id="detail-pane" role="tabpanel" aria-labelledby="detail-tab">
                        {{-- Tab Action Row --}}
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
                            <span class="text-muted fw-semibold" style="font-size: 0.85rem;">
                                Menampilkan tabel riwayat presensi check-in dan check-out.
                            </span>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.kehadiran.download-pdf', ['search' => $search, 'shift_id' => $shiftFilter]) }}" class="btn btn-outline-danger btn-sm px-3">
                                    <i class="bx bxs-file-pdf me-1"></i> PDF
                                </a>
                                <a href="{{ route('admin.kehadiran.download-excel', ['search' => $search, 'shift_id' => $shiftFilter]) }}" class="btn btn-outline-success btn-sm px-3">
                                    <i class="bx bxs-spreadsheet me-1"></i> Excel
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive border rounded">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="table-light text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                    <tr>
                                        <th class="text-center" style="width: 5%">No</th>
                                        <th style="width: 15%">Tanggal</th>
                                        <th style="width: 25%">Operator</th>
                                        <th class="text-center" style="width: 15%">Shift</th>
                                        <th style="width: 15%">Check-In</th>
                                        <th style="width: 15%">Check-Out</th>
                                        <th class="text-center" style="width: 10%">Status Kehadiran</th>
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

                                        // Tentukan badge shift
                                        $shiftBadgeClass = 'bg-label-primary';
                                        if ($detail->shift_id == 1) {
                                            $shiftBadgeClass = 'bg-label-success';
                                        } elseif ($detail->shift_id == 2) {
                                            $shiftBadgeClass = 'bg-label-warning';
                                        } elseif ($detail->shift_id == 3) {
                                            $shiftBadgeClass = 'bg-label-info';
                                        }
                                    @endphp
                                    <tr>
                                        <td class="text-center text-muted fw-semibold">{{ $details->firstItem() + $index }}</td>
                                        <td class="fw-semibold text-dark">
                                            {{ $logbook->tanggal->translatedFormat('d F Y') }}
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                @if($detail->user->foto)
                                                    <img src="{{ asset('storage/photos/' . $detail->user->foto) }}" alt="Foto" class="rounded-circle" width="38" height="38" style="object-fit: cover; border: 1px solid #ddd;">
                                                @else
                                                    <div class="avatar-initial rounded-circle bg-label-primary d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; font-size: 0.9rem;">
                                                        {{ strtoupper(substr($detail->user->name, 0, 2)) }}
                                                    </div>
                                                @endif
                                                <div class="d-flex flex-column">
                                                    <span class="fw-semibold text-dark" style="font-size: 0.9rem;">{{ $detail->user->name }}</span>
                                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $detail->user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $shiftBadgeClass }} px-2 py-1" style="font-size: 0.78rem;">
                                                {{ $detail->shift->nama_shift }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1 text-dark">
                                                <i class="bx bx-log-in-circle text-success" style="font-size: 1.15rem;"></i>
                                                <span class="fw-bold">{{ $checkinTime ? $checkinTime->format('H:i') : '-' }}</span>
                                                <small class="text-muted" style="font-size: 0.7rem; font-weight: normal;">WIB</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($isCheckedOut && $checkoutTime)
                                                <div class="d-flex align-items-center gap-1 text-dark">
                                                    <i class="bx bx-log-out-circle text-danger" style="font-size: 1.15rem;"></i>
                                                    <span class="fw-bold">{{ $checkoutTime->format('H:i') }}</span>
                                                    <small class="text-muted" style="font-size: 0.7rem; font-weight: normal;">WIB</small>
                                                </div>
                                            @else
                                                <span class="badge bg-label-warning px-2"><i class="bx bx-sync bx-spin me-1"></i> Sedang Bertugas</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $lateness['badge_class'] }} px-2 py-1">
                                                <i class="bx {{ $lateness['is_late'] ? 'bx-alarm-off' : 'bx-badge-check' }} me-1" style="font-size: 0.85rem;"></i> {{ $lateness['status_text'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">
                                            <i class="bx bx-calendar-x mb-2" style="font-size: 3.5rem; opacity: 0.4;"></i>
                                            <p class="mb-0 fw-semibold">Tidak ada log kehadiran operator ditemukan.</p>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- PAGINATION --}}
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mt-4 gap-2">
                            <small class="text-muted fw-semibold">
                                Menampilkan {{ $details->firstItem() ?? 0 }} s.d. {{ $details->lastItem() ?? 0 }} dari {{ $details->total() }} data
                            </small>
                            <div>
                                {{ $details->links() }}
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: REKAP PER OPERATOR --}}
                    <div class="tab-pane fade" id="rekap-pane" role="tabpanel" aria-labelledby="rekap-tab">
                        {{-- Tab Action Row --}}
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
                            <span class="text-muted fw-semibold" style="font-size: 0.85rem;">
                                Menampilkan rekapitulasi jumlah shift dan riwayat tanggal tugas setiap operator.
                            </span>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.kehadiran.rekap-pdf', ['search' => $search, 'shift_id' => $shiftFilter]) }}" class="btn btn-outline-danger btn-sm px-3">
                                    <i class="bx bxs-file-pdf me-1"></i> PDF
                                </a>
                                <a href="{{ route('admin.kehadiran.rekap-excel', ['search' => $search, 'shift_id' => $shiftFilter]) }}" class="btn btn-outline-success btn-sm px-3">
                                    <i class="bx bxs-spreadsheet me-1"></i> Excel
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive border rounded">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="table-light text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                    <tr>
                                        <th class="text-center" style="width: 5%">No</th>
                                        <th style="width: 35%">Nama Operator</th>
                                        <th class="text-center" style="width: 20%">Jumlah Shift Dijaga</th>
                                        <th style="width: 40%">Tanggal Menjaga</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($rekapOperator as $userId => $item)
                                    <tr>
                                        <td class="text-center text-muted fw-semibold">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                @if($item['user']->foto)
                                                    <img src="{{ asset('storage/photos/' . $item['user']->foto) }}" alt="Foto" class="rounded-circle" width="38" height="38" style="object-fit: cover; border: 1px solid #ddd;">
                                                @else
                                                    <div class="avatar-initial rounded-circle bg-label-primary d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; font-size: 0.9rem;">
                                                        {{ strtoupper(substr($item['user']->name, 0, 2)) }}
                                                    </div>
                                                @endif
                                                <div class="d-flex flex-column">
                                                    <span class="fw-semibold text-dark" style="font-size: 0.9rem;">{{ $item['user']->name }}</span>
                                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $item['user']->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-info fw-bold px-3 py-1" style="font-size: 0.82rem;">
                                                {{ $item['jumlah_shift'] }} Shift
                                            </span>
                                        </td>
                                        <td>
                                            @if($item['tanggal_menjaga'])
                                                @php
                                                    $datesArray = explode(', ', $item['tanggal_menjaga']);
                                                @endphp
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($datesArray as $date)
                                                        <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.75rem; font-weight: 550;">
                                                            {{ $date }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">
                                            <i class="bx bx-user-x mb-2" style="font-size: 3.5rem; opacity: 0.4;"></i>
                                            <p class="mb-0 fw-semibold">Tidak ada rekap operator ditemukan.</p>
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
