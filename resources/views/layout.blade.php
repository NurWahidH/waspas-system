<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK WASPAS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            background: linear-gradient(135deg, #ff8c42 0%, #ff6b35 100%);
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .sidebar .nav-link {
            color: white;
            padding: 12px 20px;
            margin: 5px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.2);
            transform: translateX(5px);
        }
        .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.3);
        }
        .brand-title {
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .section-title {
            color: rgba(255,255,255,0.8);
            font-size: 0.8rem;
            font-weight: bold;
            padding: 15px 20px 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .main-content {
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #6c5ce7 0%, #74b9ff 100%);
            color: white;
            border: none;
            padding: 15px 20px;
        }
        .stats-card {
            border-radius: 15px;
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        .stats-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        .stats-card h3 {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 10px 0;
        }
        .stats-card i {
            font-size: 2rem;
            opacity: 0.8;
        }
        .bg-purple { background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%); }
        .bg-green { background: linear-gradient(135deg, #00b894 0%, #55efc4 100%); }
        .bg-pink { background: linear-gradient(135deg, #fd79a8 0%, #fdcb6e 100%); }
        .bg-blue { background: linear-gradient(135deg, #0984e3 0%, #74b9ff 100%); }
        .bg-orange { background: linear-gradient(135deg, #ff7675 0%, #fd79a8 100%); }
        
        .btn-custom {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .table th {
            background-color: #f8f9fa;
            border: none;
            font-weight: 600;
            color: #495057;
        }
        .badge-custom {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="brand-title">
                    <i class="fas fa-calculator me-2"></i>SPK WASPAS
                </div>
                
                <div class="section-title">Menu Utama</div>
                <nav class="nav flex-column">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <i class="fas fa-home me-2"></i>Dashboard
                    </a>
                </nav>
                
                <div class="section-title">Master Data</div>
                <nav class="nav flex-column">
                    <a class="nav-link" href="{{ route('kriteria.index') }}">
                        <i class="fas fa-list me-2"></i>Data Kriteria
                    </a>
                    <a class="nav-link" href="{{ route('sub-kriteria.index') }}">
                        <i class="fas fa-sitemap me-2"></i>Data Sub Kriteria
                    </a>
                    <a class="nav-link" href="{{ route('alternatif.index') }}">
                        <i class="fas fa-users me-2"></i>Data Alternatif
                    </a>
                    <a class="nav-link" href="{{ route('penilaian.index') }}">
                        <i class="fas fa-calculator me-2"></i>Data Penilaian
                    </a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>