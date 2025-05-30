@extends('layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Data Alternatif</h2>
</div>

<div class="card">
    <div class="card-header">
        <h5>Tambah/Edit Alternatif</h5>
    </div>
    <div class="card-body">
        <form id="alternatifForm" method="POST">
            @csrf
            <input type="hidden" id="alternatifId" name="alternatif_id">
            <input type="hidden" id="formMethod" name="_method">
            
            <div class="mb-3">
                <label for="nama_alternatif" class="form-label">Nama Alternatif</label>
                <input type="text" class="form-control" id="nama_alternatif" name="nama_alternatif" required>
                @error('nama_alternatif')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            
            @foreach($kriteria as $krit)
                <div class="mb-3">
                    <label class="form-label">{{ $krit->nama_kriteria }} ({{ $krit->jenis }})</label>
                    <select class="form-control" name="penilaian[{{ $krit->id }}]" required>
                        <option value="">Pilih {{ $krit->nama_kriteria }}</option>
                        @foreach($krit->subKriteria as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->nama_sub_kriteria }} ({{ $sub->nilai }})</option>
                        @endforeach
                    </select>
                    @error("penilaian.{$krit->id}")
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
            @endforeach
            
            <button type="submit" class="btn btn-primary" id="submitBtn">Tambah</button>
            <button type="button" class="btn btn-secondary" onclick="resetForm()">Reset</button>
        </form>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5>Daftar Alternatif</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Alternatif</th>
                        @foreach($kriteria as $krit)
                            <th>{{ $krit->nama_kriteria }}</th>
                        @endforeach
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alternatif as $key => $item)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $item->nama_alternatif }}</td>
                        @foreach($kriteria as $krit)
                            <td>
                                @php
                                    $penilaian = $item->penilaian->where('kriteria_id', $krit->id)->first();
                                @endphp
                                @if($penilaian && $penilaian->subKriteria)
                                    {{ $penilaian->subKriteria->nama_sub_kriteria }} 
                                    <small class="text-muted">({{ $penilaian->subKriteria->nilai }})</small>
                                @else
                                    <span class="text-danger">-</span>
                                @endif
                            </td>
                        @endforeach
                        <td>
                            <button class="btn btn-warning btn-sm" onclick="editAlternatif({{ $item->id }})">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <form action="{{ route('alternatif.destroy', $item) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" 
                                        onclick="return confirm('Yakin hapus alternatif {{ $item->nama_alternatif }}?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($kriteria) + 3 }}" class="text-center">Belum ada data alternatif</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
let alternatifData = @json($alternatif->load('penilaian'));

function resetForm() {
    document.getElementById('alternatifForm').action = "{{ route('alternatif.store') }}";
    document.getElementById('formMethod').value = '';
    document.getElementById('alternatifId').value = '';
    document.getElementById('nama_alternatif').value = '';
    document.getElementById('submitBtn').textContent = 'Tambah';
    
    // Reset all selects
    document.querySelectorAll('select[name^="penilaian"]').forEach(select => select.value = '');
}

function editAlternatif(id) {
    const alternatif = alternatifData.find(alt => alt.id === id);
    if (!alternatif) {
        alert('Data alternatif tidak ditemukan');
        return;
    }
    
    document.getElementById('alternatifForm').action = `/alternatif/${id}`;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('alternatifId').value = id;
    document.getElementById('nama_alternatif').value = alternatif.nama_alternatif;
    document.getElementById('submitBtn').textContent = 'Update';
    
    // Reset all selects first
    document.querySelectorAll('select[name^="penilaian"]').forEach(select => select.value = '');
    
    // Set penilaian values
    if (alternatif.penilaian && alternatif.penilaian.length > 0) {
        alternatif.penilaian.forEach(penilaian => {
            const select = document.querySelector(`select[name="penilaian[${penilaian.kriteria_id}]"]`);
            if (select) {
                select.value = penilaian.sub_kriteria_id;
            }
        });
    }
    
    // Scroll to form
    document.getElementById('alternatifForm').scrollIntoView({ behavior: 'smooth' });
}
</script>
@endsection