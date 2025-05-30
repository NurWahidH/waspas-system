@extends('layout')

@section('content')
<div class="mb-4">
    <h2>Data Penilaian & Perhitungan WASPAS</h2>
</div>

<!-- Matrix Pencocokan -->
<div class="card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-table"></i> Matrix Pencocokan</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Alternatif</th>
                        @foreach($kriteria as $krit)
                            <th class="text-center">
                                {{ $krit->kode_kriteria }}
                                <br><small>({{ $krit->jenis }})</small>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($rankedAlternatif as $alt)
                    <tr>
                        <td><strong>{{ $alt->nama_alternatif }}</strong></td>
                        @foreach($kriteria as $krit)
                            <td class="text-center">{{ $matrix[$alt->id][$krit->id] ?? 0 }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Matrix Normalisasi -->
<div class="card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-calculator"></i> Matrix Normalisasi</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Alternatif</th>
                        @foreach($kriteria as $krit)
                            <th class="text-center">{{ $krit->kode_kriteria }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($rankedAlternatif as $alt)
                    <tr>
                        <td><strong>{{ $alt->nama_alternatif }}</strong></td>
                        @foreach($kriteria as $krit)
                            <td class="text-center">{{ number_format($normalisasi[$alt->id][$krit->id] ?? 0, 4) }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bobot Kriteria -->
<div class="card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-weight-hanging"></i> Bobot Kriteria</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-secondary">
                    <tr>
                        @foreach($kriteria as $krit)
                            <th class="text-center">
                                {{ $krit->kode_kriteria }}
                                <br><small>{{ $krit->nama_kriteria }}</small>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @foreach($kriteria as $krit)
                            <td class="text-center"><strong>{{ $krit->bobot }}</strong></td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Nilai Qi dan Ranking -->
<div class="card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-trophy"></i> Hasil Akhir WASPAS & Ranking</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-success">
                    <tr>
                        <th class="text-center">Ranking</th>
                        <th>Alternatif</th>
                        <th class="text-center">Nilai Qi</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php $rank = 1; @endphp
                    @foreach($rankedAlternatif as $alt)
                    <tr class="{{ $rank == 1 ? 'table-warning' : '' }}">
                        <td class="text-center">
                            @if($rank == 1)
                                <i class="fas fa-crown text-warning"></i>
                            @endif
                            <strong>{{ $rank }}</strong>
                        </td>
                        <td>
                            <strong>{{ $alt->nama_alternatif }}</strong>
                            @if($rank == 1)
                                <span class="badge bg-success ms-2">Terbaik</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <strong>{{ number_format($qi[$alt->id] ?? 0, 6) }}</strong>
                        </td>
                        <td class="text-center">
                            @if($rank <= 3)
                                <span class="badge bg-primary">Top {{ $rank }}</span>
                            @else
                                <span class="badge bg-secondary">Peringkat {{ $rank }}</span>
                            @endif
                        </td>
                    </tr>
                    @php $rank++; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Informasi Perhitungan -->
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-info-circle"></i> Informasi Perhitungan WASPAS</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>Langkah & Formula:</h6>
                <ul class="small text-muted">
                    <li><strong>Normalisasi Benefit:</strong> x<sub>ij</sub>' = x<sub>ij</sub> / max(x<sub>ij</sub>)</li>
                    <li><strong>Normalisasi Cost:</strong> x<sub>ij</sub>' = min(x<sub>ij</sub>) / x<sub>ij</sub></li>
                    <li><strong>WSM:</strong> Q<sub>i</sub><sup>(1)</sup> = Σ (w<sub>j</sub> × x<sub>ij</sub>')</li>
                    <li><strong>WPM:</strong> Q<sub>i</sub><sup>(2)</sup> = Π (x<sub>ij</sub>')<sup>w<sub>j</sub></sup></li>
                    <li><strong>Agregasi:</strong> Q<sub>i</sub> = λ × Q<sub>i</sub><sup>(1)</sup> + (1 - λ) × Q<sub>i</sub><sup>(2)</sup></li>
                </ul>
                <p><small>dengan λ = 0.5</small></p>
            </div>
            <div class="col-md-6">
                <h6>Keterangan:</h6>
                <ul class="small text-muted">
                    <li>WSM = Weighted Sum Model</li>
                    <li>WPM = Weighted Product Model</li>
                    <li>Q<sub>i</sub> = Skor akhir alternatif ke-i</li>
                    <li>w<sub>j</sub> = Bobot kriteria ke-j</li>
                    <li>x<sub>ij</sub>' = Nilai normalisasi</li>
                    <li>Benefit: nilai tinggi lebih baik</li>
                    <li>Cost: nilai rendah lebih baik</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection