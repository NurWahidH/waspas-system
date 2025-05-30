@extends('layout')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h2>Edit Data Sub Kriteria</h2>
        
        <div class="card">
            <div class="card-body">
                <form action="{{ route('sub-kriteria.update', $subKriteria) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="kriteria_id" class="form-label">Kriteria</label>
                        <select class="form-control" id="kriteria_id" name="kriteria_id" required>
                            <option value="">Pilih Kriteria</option>
                            @foreach($kriteria as $item)
                                <option value="{{ $item->id }}" {{ old('kriteria_id', $subKriteria->kriteria_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama_kriteria }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nama_sub_kriteria" class="form-label">Nama Sub Kriteria</label>
                        <input type="text" class="form-control" id="nama_sub_kriteria" name="nama_sub_kriteria" value="{{ old('nama_sub_kriteria', $subKriteria->nama_sub_kriteria) }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nilai" class="form-label">Nilai</label>
                        <input type="number" step="0.01" class="form-control" id="nilai" name="nilai" value="{{ old('nilai', $subKriteria->nilai) }}" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('sub-kriteria.index') }}" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection