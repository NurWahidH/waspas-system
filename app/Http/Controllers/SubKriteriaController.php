<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\SubKriteria;
use Illuminate\Http\Request;

class SubKriteriaController extends Controller
{
    public function index()
    {
        $subKriteria = SubKriteria::with('kriteria')->get();
        return view('sub-kriteria.index', compact('subKriteria'));
    }

    public function create()
    {
        $kriteria = Kriteria::all();
        return view('sub-kriteria.create', compact('kriteria'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kriteria_id' => 'required|exists:kriteria,id',
            'nama_sub_kriteria' => 'required',
            'nilai' => 'required|numeric'
        ]);

        SubKriteria::create($request->all());
        return redirect()->route('sub-kriteria.index')->with('success', 'Sub Kriteria berhasil ditambahkan');
    }

    public function edit(SubKriteria $subKriteria)
    {
        $kriteria = Kriteria::all();
        return view('sub-kriteria.edit', compact('subKriteria', 'kriteria'));
    }

    public function update(Request $request, SubKriteria $subKriteria)
    {
        $request->validate([
            'kriteria_id' => 'required|exists:kriteria,id',
            'nama_sub_kriteria' => 'required',
            'nilai' => 'required|numeric'
        ]);

        $subKriteria->update($request->all());
        return redirect()->route('sub-kriteria.index')->with('success', 'Sub Kriteria berhasil diupdate');
    }

    public function destroy(SubKriteria $subKriteria)
    {
        $subKriteria->delete();
        return redirect()->route('sub-kriteria.index')->with('success', 'Sub Kriteria berhasil dihapus');
    }
}
