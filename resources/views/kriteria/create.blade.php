@extends('layout')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h2>Tambah Data Kriteria</h2>
        
        <div class="card">
            <div class="card-body">
                <form action="{{ route('kriteria.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="kode_kriteria" class="form-label">Kode Kriteria</label>
                        <input type="text" class="form-control" id="kode_kriteria" name="kode_kriteria" value="{{ old('kode_kriteria') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nama_kriteria" class="form-label">Nama Kriteria</label>
                        <input type="text" class="form-control" id="nama_kriteria" name="nama_kriteria" value="{{ old('nama_kriteria') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bobot" class="form-label">Bobot</label>
                        <input type="number" step="0.001" min="0" max="1" class="form-control" id="bobot" name="bobot" value="{{ old('bobot') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="jenis" class="form-label">Jenis</label>
                        <select class="form-control" id="jenis" name="jenis" required>
                            <option value="">Pilih Jenis</option>
                            <option value="Benefit" {{ old('jenis') == 'Benefit' ? 'selected' : '' }}>Benefit</option>
                            <option value="Cost" {{ old('jenis') == 'Cost' ? 'selected' : '' }}>Cost</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('kriteria.index') }}" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection