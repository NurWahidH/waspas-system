<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\SubKriteria;
use App\Models\Alternatif;
use App\Models\Penilaian;

class DashboardController extends Controller
{
    public function index()
    {
        $totalKriteria = Kriteria::count();
        $totalSubKriteria = SubKriteria::count();
        $totalAlternatif = Alternatif::count();
        $totalPenilaian = Penilaian::count();

        return view('dashboard', compact('totalKriteria', 'totalSubKriteria', 'totalAlternatif', 'totalPenilaian'));
    }
}