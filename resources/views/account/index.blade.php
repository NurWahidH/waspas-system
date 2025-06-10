@extends('layout')

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Profile Overview -->
        <div class="profile-section">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="col-md-10">
                    <h4 class="mb-1">{{ $user->nama_lengkap }}</h4>
                    <p class="mb-1"><i class="fas fa-envelope me-2"></i>{{ $user->email }}</p>
                    <p class="mb-0"><i class="fas fa-user-tag me-2"></i>{{ $user->username }}</p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Profile Information -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user-edit me-2"></i>Informasi Profil
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('account.update-profile') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control disabled-field" id="username" 
                                       value="{{ $user->username }}" readonly>
                                <label for="username">
                                    <i class="fas fa-user me-2"></i>Username
                                </label>
                                <div class="form-text">Username tidak dapat diubah</div>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="email" class="form-control disabled-field" id="email" 
                                       value="{{ $user->email }}" readonly>
                                <label for="email">
                                    <i class="fas fa-envelope me-2"></i>Email
                                </label>
                                <div class="form-text">Email tidak dapat diubah</div>
                            </div>

                            <div class="form-floating mb-4">
                                <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" 
                                       id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" 
                                       placeholder="Nama Lengkap" required>
                                <label for="nama_lengkap">
                                    <i class="fas fa-id-card me-2"></i>Nama Lengkap
                                </label>
                                @error('nama_lengkap')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary btn-custom">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Change Password -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-lock me-2"></i>Ubah Password
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('account.update-password') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" name="current_password" placeholder="Password Saat Ini" required>
                                <label for="current_password">
                                    <i class="fas fa-key me-2"></i>Password Saat Ini
                                </label>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" placeholder="Password Baru" required>
                                <label for="password">
                                    <i class="fas fa-lock me-2"></i>Password Baru
                                </label>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-floating mb-4">
                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                       id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi Password Baru" required>
                                <label for="password_confirmation">
                                    <i class="fas fa-lock me-2"></i>Konfirmasi Password Baru
                                </label>
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-warning btn-custom">
                                <i class="fas fa-key me-2"></i>Ubah Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Informasi Akun
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="badge bg-success me-3">
                                        <i class="fas fa-calendar-plus"></i>
                                    </div>
                                    <div>
                                        <strong>Tanggal Bergabung</strong><br>
                                        <small class="text-muted">{{ $user->created_at->setTimezone('Asia/Jakarta')->format('d F Y, H:i') }} WIB</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="badge bg-primary me-3">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div>
                                        <strong>Terakhir Diperbarui</strong><br>
                                        <small class="text-muted">{{ $user->updated_at->setTimezone('Asia/Jakarta')->format('d F Y, H:i') }} WIB</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="fas fa-shield-alt me-3"></i>
                            <div>
                                <strong>Keamanan Akun:</strong> Pastikan untuk menggunakan password yang kuat dan unik untuk menjaga keamanan akun Anda.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection