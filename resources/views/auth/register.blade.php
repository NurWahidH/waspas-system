<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SPK WASPAS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px 0;
        }
        
        .register-container {
            max-width: 480px;
            width: 100%;
            margin: 0 auto;
        }
        
        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .register-header {
            background: linear-gradient(135deg, #6c5ce7 0%, #74b9ff 100%);
            color: white;
            text-align: center;
            padding: 40px 30px 30px;
        }
        
        .register-header i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }
        
        .register-header h2 {
            font-size: 1.8rem;
            font-weight: 600;
            margin: 0;
        }
        
        .register-header p {
            margin: 5px 0 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }
        
        .register-body {
            padding: 40px 30px;
        }
        
        .form-floating {
            margin-bottom: 20px;
        }
        
        .form-floating > .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            height: 60px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-floating > .form-control:focus {
            border-color: #6c5ce7;
            box-shadow: 0 0 0 0.2rem rgba(108, 92, 231, 0.25);
        }
        
        .form-floating > label {
            padding: 1rem 1rem;
            font-weight: 500;
            color: #6c757d;
        }
        
        .btn-register {
            background: linear-gradient(135deg, #6c5ce7 0%, #74b9ff 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 15px;
            width: 100%;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(108, 92, 231, 0.3);
            color: white;
        }
        
        .login-link {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        
        .login-link a {
            color: #6c5ce7;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .login-link a:hover {
            color: #74b9ff;
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            margin-bottom: 20px;
        }
        
        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }
        
        .shape {
            position: absolute;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }
        
        .shape:nth-child(1) {
            top: 10%;
            left: 15%;
            animation-delay: -2s;
        }
        
        .shape:nth-child(2) {
            top: 70%;
            right: 15%;
            animation-delay: -4s;
        }
        
        .shape:nth-child(3) {
            bottom: 10%;
            left: 25%;
            animation-delay: -1s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .password-strength {
            font-size: 0.8rem;
            margin-top: 5px;
        }
        
        .strength-weak { color: #dc3545; }
        .strength-medium { color: #ffc107; }
        .strength-strong { color: #28a745; }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape">
            <i class="fas fa-user-plus fa-3x"></i>
        </div>
        <div class="shape">
            <i class="fas fa-chart-bar fa-4x"></i>
        </div>
        <div class="shape">
            <i class="fas fa-cogs fa-3x"></i>
        </div>
    </div>

    <div class="container">
        <div class="register-container">
            <div class="card register-card">
                <div class="register-header">
                    <i class="fas fa-user-plus"></i>
                    <h2>Daftar Akun</h2>
                    <p>Buat akun baru untuk SPK WASPAS</p>
                </div>
                
                <div class="register-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        
                        <div class="form-floating">
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                   placeholder="Nama Lengkap" value="{{ old('nama_lengkap') }}" required>
                            <label for="nama_lengkap">
                                <i class="fas fa-user me-2"></i>Nama Lengkap
                            </label>
                        </div>

                        <div class="form-floating">
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="Email" value="{{ old('email') }}" required>
                            <label for="email">
                                <i class="fas fa-envelope me-2"></i>Email
                            </label>
                        </div>

                        <div class="form-floating">
                            <input type="text" class="form-control" id="username" name="username" 
                                   placeholder="Username" value="{{ old('username') }}" required>
                            <label for="username">
                                <i class="fas fa-at me-2"></i>Username
                            </label>
                        </div>

                        <div class="form-floating">
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Password" required>
                            <label for="password">
                                <i class="fas fa-lock me-2"></i>Password
                            </label>
                            <div id="password-strength" class="password-strength"></div>
                        </div>

                        <div class="form-floating">
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" 
                                   placeholder="Konfirmasi Password" required>
                            <label for="password_confirmation">
                                <i class="fas fa-lock me-2"></i>Konfirmasi Password
                            </label>
                        </div>

                        <button type="submit" class="btn btn-register">
                            <i class="fas fa-user-plus me-2"></i>Daftar
                        </button>
                    </form>
                    
                    <div class="login-link">
                        <p class="mb-0">Sudah punya akun? <a href="{{ route('login') }}">Masuk Sekarang</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthDiv = document.getElementById('password-strength');
            
            if (password.length === 0) {
                strengthDiv.innerHTML = '';
                return;
            }
            
            let strength = 0;
            
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            let strengthText = '';
            let strengthClass = '';
            
            if (strength < 3) {
                strengthText = 'Password lemah';
                strengthClass = 'strength-weak';
            } else if (strength < 4) {
                strengthText = 'Password sedang';
                strengthClass = 'strength-medium';
            } else {
                strengthText = 'Password kuat';
                strengthClass = 'strength-strong';
            }
            
            strengthDiv.innerHTML = `<i class="fas fa-info-circle me-1"></i>${strengthText}`;
            strengthDiv.className = `password-strength ${strengthClass}`;
        });
    </script>
</body>
</html>