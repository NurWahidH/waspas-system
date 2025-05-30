<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;

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
        
        // Nilai Qi (WASPAS)
        $qi = $this->calculateWaspas($alternatif, $kriteria, $normalisasi);
        
        // Urutkan berdasarkan nilai Qi tertinggi
        $rankedAlternatif = $alternatif->sortByDesc(function($alt) use ($qi) {
            return $qi[$alt->id] ?? 0;
        });

        return view('penilaian.index', compact('rankedAlternatif', 'kriteria', 'matrix', 'normalisasi', 'qi'));
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
     * Menghitung nilai WASPAS
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