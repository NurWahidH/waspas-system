<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - {{ config('app.name', 'Laravel') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body p-5">
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="fas fa-envelope-circle-check text-primary" style="font-size: 3rem;"></i>
                            </div>
                            <h3 class="card-title mb-2">Verifikasi Email Anda</h3>
                            <p class="text-muted">
                                Terima kasih telah mendaftar! Sebelum memulai, bisakah Anda memverifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan kepada Anda?
                            </p>
                        </div>

                        <!-- Alert Messages -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('status') == 'verification-link-sent')
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-paper-plane me-2"></i>
                                Link verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- User Info -->
                        <div class="bg-light p-3 rounded mb-4">
                            <small class="text-muted">Email terdaftar:</small>
                            <div class="fw-semibold">{{ Auth::user()->email }}</div>
                        </div>

                        <!-- Resend Verification Form -->
                        <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane me-2"></i>
                                Kirim Ulang Email Verifikasi
                            </button>
                        </form>

                        <!-- Divider -->
                        <hr class="my-4">

                        <!-- Action Links -->
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Sudah verifikasi?
                                <a href="{{ route('dashboard') }}" class="text-decoration-none">
                                    Ke Dashboard
                                </a>
                            </small>
                            
                            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-link btn-sm text-muted p-0">
                                    <i class="fas fa-sign-out-alt me-1"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Additional Info Card -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-info-circle text-info me-2"></i>
                            Tidak menerima email?
                        </h6>
                        <small class="text-muted">
                            • Periksa folder spam/junk mail Anda<br>
                            • Pastikan alamat email {{ Auth::user()->email }} benar<br>
                            • Coba kirim ulang email verifikasi dengan tombol di atas
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>