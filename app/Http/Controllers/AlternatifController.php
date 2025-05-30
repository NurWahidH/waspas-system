<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlternatifController extends Controller
{
    public function index()
    {
        $alternatif = Alternatif::with([
            'penilaian',
            'penilaian.kriteria',
            'penilaian.subKriteria'
        ])->get();
        
        $kriteria = Kriteria::with('subKriteria')->get();
        
        return view('alternatif.index', compact('alternatif', 'kriteria'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_alternatif' => 'required|unique:alternatif',
            'penilaian' => 'required|array',
            'penilaian.*' => 'required|exists:sub_kriteria,id'
        ]);

        DB::beginTransaction();
        try {
            $alternatif = Alternatif::create([
                'nama_alternatif' => $request->nama_alternatif
            ]);

            foreach ($request->penilaian as $kriteriaId => $subKriteriaId) {
                $subKriteria = \App\Models\SubKriteria::find($subKriteriaId);
                
                Penilaian::create([
                    'alternatif_id' => $alternatif->id,
                    'kriteria_id' => $kriteriaId,
                    'sub_kriteria_id' => $subKriteriaId,
                    'nilai' => $subKriteria ? $subKriteria->nilai : 0
                ]);
            }

            DB::commit();
            return redirect()->route('alternatif.index')->with('success', 'Alternatif berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menambahkan alternatif: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Alternatif $alternatif)
    {
        $request->validate([
            'nama_alternatif' => 'required|unique:alternatif,nama_alternatif,' . $alternatif->id,
            'penilaian' => 'required|array',
            'penilaian.*' => 'required|exists:sub_kriteria,id'
        ]);

        DB::beginTransaction();
        try {
            $alternatif->update([
                'nama_alternatif' => $request->nama_alternatif
            ]);

            // Hapus penilaian lama dan buat yang baru
            $alternatif->penilaian()->delete();

            foreach ($request->penilaian as $kriteriaId => $subKriteriaId) {
                $subKriteria = \App\Models\SubKriteria::find($subKriteriaId);
                
                Penilaian::create([
                    'alternatif_id' => $alternatif->id,
                    'kriteria_id' => $kriteriaId,
                    'sub_kriteria_id' => $subKriteriaId,
                    'nilai' => $subKriteria ? $subKriteria->nilai : 0
                ]);
            }

            DB::commit();
            return redirect()->route('alternatif.index')->with('success', 'Alternatif berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal mengupdate alternatif: ' . $e->getMessage());
        }
    }

    public function destroy(Alternatif $alternatif)
    {
        DB::beginTransaction();
        try {
            $alternatif->penilaian()->delete();
            $alternatif->delete();

            DB::commit();
            return redirect()->route('alternatif.index')->with('success', 'Alternatif berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menghapus alternatif: ' . $e->getMessage());
        }
    }
}