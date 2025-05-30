@extends('layout')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">Dashboard</h2>
        <div class="alert alert-info" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            Selamat datang! 
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6">
        <a href="/kriteria" class="text-decoration-none">
            <div class="stats-card bg-purple">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Data Kriteria</h6>
                        <h3>{{ $totalKriteria }}</h3>
                    </div>
                    <i class="fas fa-list"></i>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <a href="/sub-kriteria" class="text-decoration-none">
            <div class="stats-card bg-green">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Data Sub Kriteria</h6>
                        <h3>{{ $totalSubKriteria }}</h3>
                    </div>
                    <i class="fas fa-sitemap"></i>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <a href="/alternatif" class="text-decoration-none">
            <div class="stats-card bg-pink">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Data Alternatif</h6>
                        <h3>{{ $totalAlternatif }}</h3>
                    </div>
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <a href="/penilaian" class="text-decoration-none">
            <div class="stats-card bg-blue">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Data Penilaian</h6>
                        <h3>{{ $totalPenilaian }}</h3>
                    </div>
                    <i class="fas fa-calculator"></i>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Action Cards -->
<div class="row">
    <div class="col-lg-6">
        <div class="card h-100" style="background: linear-gradient(135deg, #ff8c42 0%, #ff6b35 100%); color: white;">
            <div class="card-body text-center d-flex flex-column justify-content-center">
                <i class="fas fa-calculator fa-3x mb-3 opacity-75"></i>
                <h5 class="card-title">Data Penilaian</h5>
                <p class="card-text">Kelola data penilaian alternatif berdasarkan kriteria yang telah ditentukan.</p>
                <a href="/penilaian" class="btn btn-light btn-custom mt-auto">
                    <i class="fas fa-eye me-2"></i>Lihat Data
                </a>
            </div>
        </div>
    </div>
    

<style>
/* Tambahan CSS untuk hover effect pada stats cards */
.stats-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

a.text-decoration-none:hover .stats-card h6,
a.text-decoration-none:hover .stats-card h3 {
    color: inherit;
}
</style>
@endsection