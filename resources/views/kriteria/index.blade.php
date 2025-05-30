@extends('layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Data Kriteria</h2>
    <a href="{{ route('kriteria.create') }}" class="btn btn-primary">Tambah Kriteria</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Kriteria</th>
                    <th>Nama Kriteria</th>
                    <th>Bobot</th>
                    <th>Jenis</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kriteria as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->kode_kriteria }}</td>
                    <td>{{ $item->nama_kriteria }}</td>
                    <td>{{ $item->bobot }}</td>
                    <td>
                        <span class="badge bg-{{ $item->jenis == 'Benefit' ? 'success' : 'danger' }}">
                            {{ $item->jenis }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('kriteria.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('kriteria.destroy', $item) }}" method="POST" class="d-inline">
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
@endsection
