<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PenilaianController extends Controller
{
    public function index()
    {
        // Ambil data dengan eager loading
        $alternatif = Alternatif::with([
            'penilaian' => function($query) {
                $query->with(['kriteria', 'subKriteria']);
            }
        ])->get();
        
        $kriteria = Kriteria::with('subKriteria')->orderBy('id')->get();
        
        // Matrix Pencocokan
        $matrix = $this->buildMatrix($alternatif, $kriteria);
        
        // Matrix Normalisasi
        $normalisasi = $this->buildNormalization($alternatif, $kriteria, $matrix);
        
        // Perhitungan Detail WASPAS
        $waspasDetail = $this->calculateWaspasDetail($alternatif, $kriteria, $normalisasi);
        
        // Nilai Qi (WASPAS)
        $qi = $waspasDetail['qi'];
        
        // Urutkan berdasarkan nilai Qi tertinggi
        $rankedAlternatif = $alternatif->sortByDesc(function($alt) use ($qi) {
            return $qi[$alt->id] ?? 0;
        });

        return view('penilaian.index', compact(
            'rankedAlternatif', 
            'kriteria', 
            'matrix', 
            'normalisasi', 
            'qi',
            'waspasDetail'
        ));
    }

    /**
     * Export to Excel
     */
    public function exportExcel()
    {
        $data = $this->getCalculationData();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header
        $sheet->setCellValue('A1', 'HASIL PERHITUNGAN WASPAS');
        $sheet->mergeCells('A1:' . chr(65 + count($data['kriteria'])) . '1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        
        // Matrix Pencocokan
        $row = 3;
        $sheet->setCellValue('A' . $row, 'MATRIX PENCOCOKAN');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        
        // Header kriteria
        $sheet->setCellValue('A' . $row, 'Alternatif');
        $col = 'B';
        foreach ($data['kriteria'] as $krit) {
            $sheet->setCellValue($col . $row, $krit->kode_kriteria . ' (' . $krit->jenis . ')');
            $col++;
        }
        $sheet->getStyle('A' . $row . ':' . chr(65 + count($data['kriteria'])) . $row)->getFont()->setBold(true);
        $row++;
        
        // Data matrix
        foreach ($data['rankedAlternatif'] as $alt) {
            $sheet->setCellValue('A' . $row, $alt->nama_alternatif);
            $col = 'B';
            foreach ($data['kriteria'] as $krit) {
                $sheet->setCellValue($col . $row, $data['matrix'][$alt->id][$krit->id] ?? 0);
                $col++;
            }
            $row++;
        }
        
        // Matrix Normalisasi
        $row += 2;
        $sheet->setCellValue('A' . $row, 'MATRIX NORMALISASI');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        
        // Header normalisasi
        $sheet->setCellValue('A' . $row, 'Alternatif');
        $col = 'B';
        foreach ($data['kriteria'] as $krit) {
            $sheet->setCellValue($col . $row, $krit->kode_kriteria);
            $col++;
        }
        $sheet->getStyle('A' . $row . ':' . chr(65 + count($data['kriteria'])) . $row)->getFont()->setBold(true);
        $row++;
        
        // Data normalisasi
        foreach ($data['rankedAlternatif'] as $alt) {
            $sheet->setCellValue('A' . $row, $alt->nama_alternatif);
            $col = 'B';
            foreach ($data['kriteria'] as $krit) {
                $sheet->setCellValue($col . $row, number_format($data['normalisasi'][$alt->id][$krit->id] ?? 0, 4));
                $col++;
            }
            $row++;
        }
        
        // Perhitungan WSM
        $row += 2;
        $sheet->setCellValue('A' . $row, 'PERHITUNGAN WSM');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Alternatif');
        $sheet->setCellValue('B' . $row, 'Qi(1) - WSM');
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
        $row++;
        
        foreach ($data['rankedAlternatif'] as $alt) {
            $sheet->setCellValue('A' . $row, $alt->nama_alternatif);
            $sheet->setCellValue('B' . $row, number_format($data['waspasDetail']['wsm'][$alt->id] ?? 0, 6));
            $row++;
        }
        
        // Perhitungan WPM
        $row += 2;
        $sheet->setCellValue('A' . $row, 'PERHITUNGAN WPM');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Alternatif');
        $sheet->setCellValue('B' . $row, 'Qi(2) - WPM');
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
        $row++;
        
        foreach ($data['rankedAlternatif'] as $alt) {
            $sheet->setCellValue('A' . $row, $alt->nama_alternatif);
            $sheet->setCellValue('B' . $row, number_format($data['waspasDetail']['wpm'][$alt->id] ?? 0, 6));
            $row++;
        }
        
        // Hasil Akhir
        $row += 2;
        $sheet->setCellValue('A' . $row, 'HASIL AKHIR & RANKING');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Ranking');
        $sheet->setCellValue('B' . $row, 'Alternatif');
        $sheet->setCellValue('C' . $row, 'Nilai Qi');
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
        $row++;
        
        $rank = 1;
        foreach ($data['rankedAlternatif'] as $alt) {
            $sheet->setCellValue('A' . $row, $rank);
            $sheet->setCellValue('B' . $row, $alt->nama_alternatif);
            $sheet->setCellValue('C' . $row, number_format($data['qi'][$alt->id] ?? 0, 6));
            $row++;
            $rank++;
        }
        
        // Auto-adjust column widths
        foreach (range('A', chr(65 + count($data['kriteria']))) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        
        $fileName = 'hasil_waspas_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    
    /**
     * Export to PDF
     */
    public function exportPdf()
    {
        $data = $this->getCalculationData();
        
        $pdf = Pdf::loadView('penilaian.export-pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        
        $fileName = 'hasil_waspas_' . date('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($fileName);
    }

    /**
     * Get calculation data for export
     */
    private function getCalculationData()
    {
        // Ambil data dengan eager loading
        $alternatif = Alternatif::with([
            'penilaian' => function($query) {
                $query->with(['kriteria', 'subKriteria']);
            }
        ])->get();
        
        $kriteria = Kriteria::with('subKriteria')->orderBy('id')->get();
        
        // Matrix Pencocokan
        $matrix = $this->buildMatrix($alternatif, $kriteria);
        
        // Matrix Normalisasi
        $normalisasi = $this->buildNormalization($alternatif, $kriteria, $matrix);
        
        // Perhitungan Detail WASPAS
        $waspasDetail = $this->calculateWaspasDetail($alternatif, $kriteria, $normalisasi);
        
        // Nilai Qi (WASPAS)
        $qi = $waspasDetail['qi'];
        
        // Urutkan berdasarkan nilai Qi tertinggi
        $rankedAlternatif = $alternatif->sortByDesc(function($alt) use ($qi) {
            return $qi[$alt->id] ?? 0;
        });

        return compact('rankedAlternatif', 'kriteria', 'matrix', 'normalisasi', 'qi', 'waspasDetail');
    }

    /**
     * Membangun matrix pencocokan dengan auto sub kriteria
     */
    private function buildMatrix($alternatif, $kriteria)
    {
        $matrix = [];
        foreach ($alternatif as $alt) {
            foreach ($kriteria as $krit) {
                $penilaian = $alt->penilaian->where('kriteria_id', $krit->id)->first();
                
                if ($penilaian) {
                    // Jika ada sub kriteria, ambil nilai dari sub kriteria
                    if ($penilaian->sub_kriteria_id && $penilaian->subKriteria) {
                        $matrix[$alt->id][$krit->id] = $penilaian->subKriteria->nilai ?? 0;
                    } else {
                        // Jika tidak ada sub kriteria, gunakan nilai langsung
                        $matrix[$alt->id][$krit->id] = $penilaian->nilai ?? 0;
                    }
                } else {
                    // Jika tidak ada penilaian sama sekali, cek apakah ada sub kriteria default
                    $defaultSubKriteria = $krit->subKriteria->first();
                    if ($defaultSubKriteria) {
                        // Buat penilaian otomatis dengan sub kriteria pertama
                        $newPenilaian = Penilaian::create([
                            'alternatif_id' => $alt->id,
                            'kriteria_id' => $krit->id,
                            'sub_kriteria_id' => $defaultSubKriteria->id,
                            'nilai' => $defaultSubKriteria->nilai
                        ]);
                        $matrix[$alt->id][$krit->id] = $defaultSubKriteria->nilai;
                    } else {
                        $matrix[$alt->id][$krit->id] = 0;
                    }
                }
            }
        }
        return $matrix;
    }

    /**
     * Alternative buildMatrix method - tanpa auto create penilaian
     */
    private function buildMatrixAlternative($alternatif, $kriteria)
    {
        $matrix = [];
        foreach ($alternatif as $alt) {
            foreach ($kriteria as $krit) {
                $penilaian = $alt->penilaian->where('kriteria_id', $krit->id)->first();
                
                if ($penilaian) {
                    // Prioritaskan nilai dari sub kriteria jika ada
                    if ($penilaian->sub_kriteria_id && $penilaian->subKriteria) {
                        $matrix[$alt->id][$krit->id] = $penilaian->subKriteria->nilai;
                    } elseif ($penilaian->nilai) {
                        $matrix[$alt->id][$krit->id] = $penilaian->nilai;
                    } else {
                        $matrix[$alt->id][$krit->id] = 0;
                    }
                } else {
                    $matrix[$alt->id][$krit->id] = 0;
                }
            }
        }
        return $matrix;
    }

    /**
     * Membangun matrix normalisasi
     */
    private function buildNormalization($alternatif, $kriteria, $matrix)
    {
        $normalisasi = [];
        
        foreach ($kriteria as $krit) {
            $values = [];
            foreach ($alternatif as $alt) {
                $values[] = $matrix[$alt->id][$krit->id];
            }
            
            $values = array_filter($values, function($val) { return $val > 0; });
            
            if (empty($values)) {
                foreach ($alternatif as $alt) {
                    $normalisasi[$alt->id][$krit->id] = 0;
                }
                continue;
            }
            
            if ($krit->jenis == 'Benefit') {
                $max = max($values);
                foreach ($alternatif as $alt) {
                    $normalisasi[$alt->id][$krit->id] = $max > 0 ? $matrix[$alt->id][$krit->id] / $max : 0;
                }
            } else { // Cost
                $min = min($values);
                foreach ($alternatif as $alt) {
                    $currentValue = $matrix[$alt->id][$krit->id];
                    $normalisasi[$alt->id][$krit->id] = $currentValue > 0 ? $min / $currentValue : 0;
                }
            }
        }
        
        return $normalisasi;
    }

    /**
     * Menghitung nilai WASPAS dengan detail perhitungan
     */
    private function calculateWaspasDetail($alternatif, $kriteria, $normalisasi)
    {
        $wsm = [];
        $wpm = [];
        $qi = [];
        
        foreach ($alternatif as $alt) {
            $wsmValue = 0; // Weighted Sum Model
            $wpmValue = 1; // Weighted Product Model
            
            foreach ($kriteria as $krit) {
                $normalizedValue = $normalisasi[$alt->id][$krit->id] ?? 0;
                $weight = $krit->bobot;
                
                // WSM Calculation
                $wsmValue += $normalizedValue * $weight;
                
                // WPM Calculation
                if ($normalizedValue > 0 && $weight > 0) {
                    $wpmValue *= pow($normalizedValue, $weight);
                } else {
                    $wpmValue *= 0.001;
                }
            }
            
            $wsm[$alt->id] = $wsmValue;
            $wpm[$alt->id] = $wpmValue;
            
            // Formula WASPAS: Q = λ*WSM + (1-λ)*WPM, dengan λ = 0.5
            $qi[$alt->id] = 0.5 * $wsmValue + 0.5 * $wpmValue;
        }
        
        return [
            'wsm' => $wsm,
            'wpm' => $wpm,
            'qi' => $qi
        ];
    }

    /**
     * Menghitung nilai WASPAS (method lama untuk kompatibilitas)
     */
    private function calculateWaspas($alternatif, $kriteria, $normalisasi)
    {
        $qi = [];
        
        foreach ($alternatif as $alt) {
            $wsm = 0; // Weighted Sum Model
            $wpm = 1; // Weighted Product Model
            
            foreach ($kriteria as $krit) {
                $normalizedValue = $normalisasi[$alt->id][$krit->id] ?? 0;
                $weight = $krit->bobot;
                
                $wsm += $normalizedValue * $weight;
                
                if ($normalizedValue > 0 && $weight > 0) {
                    $wpm *= pow($normalizedValue, $weight);
                } else {
                    $wpm *= 0.001;
                }
            }
            
            // Formula WASPAS: Q = λ*WSM + (1-λ)*WPM, dengan λ = 0.5
            $qi[$alt->id] = 0.5 * $wsm + 0.5 * $wpm;
        }
        
        return $qi;
    }

    /**
     * Method untuk sinkronisasi penilaian otomatis
     */
    public function syncPenilaian()
    {
        $alternatif = Alternatif::all();
        $kriteria = Kriteria::with('subKriteria')->get();
        
        foreach ($alternatif as $alt) {
            foreach ($kriteria as $krit) {
                // Cek apakah sudah ada penilaian
                $existingPenilaian = Penilaian::where('alternatif_id', $alt->id)
                    ->where('kriteria_id', $krit->id)
                    ->first();
                
                if (!$existingPenilaian && $krit->subKriteria->count() > 0) {
                    // Ambil sub kriteria pertama sebagai default
                    $defaultSubKriteria = $krit->subKriteria->first();
                    
                    Penilaian::create([
                        'alternatif_id' => $alt->id,
                        'kriteria_id' => $krit->id,
                        'sub_kriteria_id' => $defaultSubKriteria->id,
                        'nilai' => $defaultSubKriteria->nilai
                    ]);
                }
            }
        }
        
        return redirect()->back()->with('success', 'Penilaian berhasil disinkronisasi!');
    }
}