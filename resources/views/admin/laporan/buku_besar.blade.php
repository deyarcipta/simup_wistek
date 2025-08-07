@extends('admin.layouts.app')

@section('title', 'Buku Besar Keuangan')

@section('content')
<div class="row">
    <div class="col-12">
        {{-- Form Filter --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('laporan.buku-besar') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('laporan.buku-besar.download', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success w-100">
                            <i class="bx bx-download"></i> Download
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabel Buku Besar --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Buku Besar Keuangan</h5>
            </div>
            <div class="card-body">
                {{-- Saldo Akhir di Atas --}}
                <div class="mb-3">
                    <h6 class="fw-bold">Saldo Akhir: 
                        Rp {{ number_format($bukuBesar->last()['saldo'] ?? 0, 0, ',', '.') }}
                    </h6>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th class="text-end">Debit</th>
                                <th class="text-end">Kredit</th>
                                <th class="text-end">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bukuBesar as $row)
                                <tr>
                                    <td>{{ $row['tanggal'] }}</td>
                                    <td>{{ $row['keterangan'] }}</td>
                                    <td class="text-end">{{ $row['debit'] ? 'Rp ' . number_format($row['debit'], 0, ',', '.') : '-' }}</td>
                                    <td class="text-end">{{ $row['kredit'] ? 'Rp ' . number_format($row['kredit'], 0, ',', '.') : '-' }}</td>
                                    <td class="text-end">{{ 'Rp ' . number_format($row['saldo'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            {{-- Saldo Akhir di Bawah --}}
                            <tr class="fw-bold">
                                <td colspan="4" class="text-end">Saldo Akhir</td>
                                <td class="text-end">
                                    Rp {{ number_format($bukuBesar->last()['saldo'] ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
