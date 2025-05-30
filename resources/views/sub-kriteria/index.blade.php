@extends('layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Data Sub Kriteria</h2>
    <a href="{{ route('sub-kriteria.create') }}" class="btn btn-primary">Tambah Sub Kriteria</a>
</div>

@php
    $groupedSubKriteria = $subKriteria->groupBy('kriteria.nama_kriteria');
@endphp

@foreach($groupedSubKriteria as $namaKriteria => $subKriteriaItems)
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">{{ $namaKriteria }}</h5>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Sub Kriteria</th>
                    <th>Nilai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subKriteriaItems as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->nama_sub_kriteria }}</td>
                    <td>{{ $item->nilai }}</td>
                    <td>
                        <a href="{{ route('sub-kriteria.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('sub-kriteria.destroy', $item) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endforeach

@if($subKriteria->isEmpty())
<div class="card">
    <div class="card-body text-center">
        <p class="text-muted">Belum ada data sub kriteria.</p>
    </div>
</div>
@endif
@endsection