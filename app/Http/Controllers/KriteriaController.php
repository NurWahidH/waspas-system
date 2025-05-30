<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\Alternatif;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    public function index()
    {
        $kriteria = Kriteria::all();
        return view('kriteria.index', compact('kriteria'));
    }

    public function create()
    {
        return view('kriteria.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_kriteria' => 'required|unique:kriteria',
            'nama_kriteria' => 'required',
            'bobot' => 'required|numeric|min:0|max:1',
            'jenis' => 'required|in:Benefit,Cost'
        ]);

        Kriteria::create($request->all());
        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil ditambahkan');
    }

    public function edit(Kriteria $kriteria)
    {
        return view('kriteria.edit', compact('kriteria'));
    }

    public function update(Request $request, Kriteria $kriteria)
    {
        $request->validate([
            'kode_kriteria' => 'required|unique:kriteria,kode_kriteria,' . $kriteria->id,
            'nama_kriteria' => 'required',
            'bobot' => 'required|numeric|min:0|max:1',
            'jenis' => 'required|in:Benefit,Cost'
        ]);

        $kriteria->update($request->all());
        
        // Refresh data alternatif dengan kriteria yang sudah diupdate
        $this->refreshAlternatifData();
        
        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil diupdate dan data alternatif telah disegarkan');
    }

    public function destroy(Kriteria $kriteria)
    {
        // Hapus semua penilaian yang terkait dengan kriteria ini
        $kriteria->penilaian()->delete();
        
        $kriteria->delete();
        
        // Refresh data alternatif setelah kriteria dihapus
        $this->refreshAlternatifData();
        
        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil dihapus');
    }
    
    /**
     * Method untuk refresh/update data alternatif
     * agar sinkron dengan perubahan kriteria
     */
    private function refreshAlternatifData()
    {
        // Ambil semua alternatif dengan relasi penilaian
        $alternatif = Alternatif::with(['penilaian', 'penilaian.kriteria', 'penilaian.subKriteria'])->get();
        
        foreach ($alternatif as $alt) {
            // Bisa tambahkan logic khusus jika diperlukan
            // Misalnya recalculate score, update timestamp, dll
            $alt->touch(); // Update timestamp untuk menandai data telah direfresh
        }
    }
}