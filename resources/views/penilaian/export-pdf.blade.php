<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hasil Penilaian WASPAS</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            padding: 10px 0;
            border-bottom: 3px solid #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            background-color: #e9ecef;
            padding: 10px;
            margin-bottom: 10px;
            font-weight: bold;
            border-left: 4px solid #007bff;
        }
        .formula {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-size: 11px;
        }
        .best-alternative {
            background-color: #fff3cd !important;
        }
        .final-score {
            font-weight: bold;
            color: #0056b3;
        }
        .page-break {
            page-break-before: always;
        }
        .small {
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Data Penilaian & Perhitungan WASPAS</h1>
        <p>Tanggal: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Matrix Pencocokan -->
    <div class="section">
        <div class="section-title">Matrix Pencocokan</div>
        <table>
            <thead>
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

    <!-- Matrix Normalisasi -->
    <div class="section">
        <div class="section-title">Matrix Normalisasi</div>
        <table>
            <thead>
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

    <!-- Bobot Kriteria -->
    <div class="section">
        <div class="section-title">Bobot Kriteria</div>
        <table>
            <thead>
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

    <div class="page-break"></div>

    <!-- Perhitungan WSM -->
    <div class="section">
        <div class="section-title">Perhitungan WSM (Weighted Sum Model)</div>
        <div class="formula">
            <strong>Formula WSM:</strong> Q<sub>i</sub><sup>(1)</sup> = Σ (w<sub>j</sub> × x<sub>ij</sub>') 
            <br>Dimana w<sub>j</sub> = bobot kriteria, x<sub>ij</sub>' = nilai normalisasi
        </div>
        <table>
            <thead>
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

    <!-- Perhitungan WPM -->
    <div class="section">
        <div class="section-title">Perhitungan WPM (Weighted Product Model)</div>
        <div class="formula">
            <strong>Formula WPM:</strong> Q<sub>i</sub><sup>(2)</sup> = Π (x<sub>ij</sub>')<sup>w<sub>j</sub></sup>
            <br>Dimana Π = perkalian, x<sub>ij</sub>' = nilai normalisasi, w<sub>j</sub> = bobot kriteria
        </div>
        <table>
            <thead>
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

    <!-- Hasil Akhir -->
    <div class="section">
        <div class="section-title">Hasil Akhir WASPAS & Ranking</div>
        <div class="formula">
            <strong>Formula WASPAS:</strong> Q<sub>i</sub> = λ × Q<sub>i</sub><sup>(1)</sup> + (1 - λ) × Q<sub>i</sub><sup>(2)</sup>
            <br>Dengan λ = 0.5, sehingga: Q<sub>i</sub> = 0.5 × WSM + 0.5 × WPM
        </div>
        <table>
            <thead>
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
                <tr class="{{ $rank == 1 ? 'best-alternative' : '' }}">
                    <td class="text-center">
                        <strong>{{ $rank }}</strong>
                        @if($rank == 1) ★ @endif
                    </td>
                    <td>
                        <strong>{{ $alt->nama_alternatif }}</strong>
                        @if($rank == 1)
                            <small>(Terbaik)</small>
                        @endif
                    </td>
                    <td class="text-center small">
                        {{ number_format($waspasDetail['wsm'][$alt->id] ?? 0, 4) }}
                    </td>
                    <td class="text-center small">
                        {{ number_format($waspasDetail['wpm'][$alt->id] ?? 0, 4) }}
                    </td>
                    <td class="text-center final-score">
                        {{ number_format($qi[$alt->id] ?? 0, 6) }}
                    </td>
                </tr>
                @php $rank++; @endphp
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 50px; text-align: center; font-size: 10px; color: #666;">
        <p>Dokumen ini dibuat secara otomatis pada {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>