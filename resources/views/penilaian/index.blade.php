@extends('layout')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <h2>Data Penilaian & Perhitungan WASPAS</h2>
    
    <!-- Export Buttons -->
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-download"></i> Cetak Hasil
        </button>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item" href="{{ route('penilaian.export.pdf') }}">
                    <i class="fas fa-file-pdf text-danger"></i> Export PDF
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('penilaian.export.excel') }}">
                    <i class="fas fa-file-excel text-success"></i> Export Excel
                </a>
            </li>
        </ul>
    </div>
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

<!-- Perhitungan WSM (Weighted Sum Model) -->
<div class="card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-plus-circle"></i> Perhitungan WSM (Weighted Sum Model)</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info small">
            <strong>Formula WSM:</strong> Q<sub>i</sub><sup>(1)</sup> = Σ (w<sub>j</sub> × x<sub>ij</sub>') 
            <br>Dimana w<sub>j</sub> = bobot kriteria, x<sub>ij</sub>' = nilai normalisasi
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>Alternatif</th>
                        <th class="text-center">Qi<sup>(1)</sup> - WSM</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rankedAlternatif as $alt)
                    <tr>
                        <td><strong>{{ $alt->nama_alternatif }}</strong></td>
                        <td class="text-center">
                            <strong>{{ number_format($waspasDetail['wsm'][$alt->id] ?? 0, 6) }}</strong>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Perhitungan WPM (Weighted Product Model) -->
<div class="card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-times-circle"></i> Perhitungan WPM (Weighted Product Model)</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info small">
            <strong>Formula WPM:</strong> Q<sub>i</sub><sup>(2)</sup> = Π (x<sub>ij</sub>')<sup>w<sub>j</sub></sup>
            <br>Dimana Π = perkalian, x<sub>ij</sub>' = nilai normalisasi, w<sub>j</sub> = bobot kriteria
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-warning">
                    <tr>
                        <th>Alternatif</th>
                        <th class="text-center">Qi<sup>(2)</sup> - WPM</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rankedAlternatif as $alt)
                    <tr>
                        <td><strong>{{ $alt->nama_alternatif }}</strong></td>
                        <td class="text-center">
                            <strong>{{ number_format($waspasDetail['wpm'][$alt->id] ?? 0, 6) }}</strong>
                        </td>
                    </tr>
                    @endforeach
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
        <div class="alert alert-success small">
            <strong>Formula WASPAS:</strong> Q<sub>i</sub> = λ × Q<sub>i</sub><sup>(1)</sup> + (1 - λ) × Q<sub>i</sub><sup>(2)</sup>
            <br>Dengan λ = 0.5, sehingga: Q<sub>i</sub> = 0.5 × WSM + 0.5 × WPM
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-success">
                    <tr>
                        <th class="text-center">Ranking</th>
                        <th>Alternatif</th>
                        <th class="text-center">WSM</th>
                        <th class="text-center">WPM</th>
                        <th class="text-center">Nilai Qi Final</th>
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
                        <td class="text-center small">
                            {{ number_format($waspasDetail['wsm'][$alt->id] ?? 0, 4) }}
                        </td>
                        <td class="text-center small">
                            {{ number_format($waspasDetail['wpm'][$alt->id] ?? 0, 4) }}
                        </td>
                        <td class="text-center">
                            <strong class="text-primary">{{ number_format($qi[$alt->id] ?? 0, 6) }}</strong>
                        </td>
                    </tr>
                    @php $rank++; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>



@endsection