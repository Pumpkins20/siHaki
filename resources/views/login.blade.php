<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <title>Login - SiHaki</title>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 2rem;
            text-align: center;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-card">
                    <!-- Header -->
                    <div class="login-header">
                        <i class="bi bi-shield-lock fs-1 mb-3"></i>
                        <h2 class="mb-0">SiHaki Login</h2>
                        <p class="mb-0 opacity-75">Sistem Hak Kekayaan Intelektual</p>
                    </div>
                    
                    <!-- Form -->
                    <div class="p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        <form action="{{ route('login.post') }}" method="POST">
                            @csrf 
                            <div class="mb-3">
                                <label for="nidn" class="form-label">
                                    <i class="bi bi-person-badge me-1"></i>NIDN
                                </label>
                                <input type="text" 
                                       value="{{ old('nidn') }}" 
                                       name="nidn" 
                                       id="nidn"
                                       class="form-control @error('nidn') is-invalid @enderror" 
                                       placeholder="Masukkan NIDN Anda"
                                       required>
                                @error('nidn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <small>Gunakan NIDN yang terdaftar di sistem</small>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock me-1"></i>Password
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           name="password" 
                                           id="password"
                                           class="form-control @error('password') is-invalid @enderror" 
                                           placeholder="Masukkan password"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <small>Password default: NIDN Anda. Silakan ganti setelah login pertama.</small>
                                </div>
                            </div>
                            
                            <div class="d-grid mb-3">
                                <button name="submit" type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                                </button>
                            </div>
                        </form>
                        
                        <!-- Info -->
                        <div class="text-center mt-4">
                            <div class="alert alert-info">
                                <small>
                                    <i class="bi bi-info-circle me-1"></i>
                                    <strong>Info Login:</strong><br>
                                    • Gunakan NIDN sebagai username<br>
                                    • Password default sama dengan NIDN<br>
                                    • Hubungi admin jika mengalami kesulitan
                                </small>
                            </div>
                        </div>

                        <!-- Contact -->
                        <div class="text-center border-top pt-3">
                            <small class="text-muted">
                                Butuh bantuan? Hubungi: 
                                <a href="mailto:hki@amikom.ac.id" class="text-decoration-none">hki@amikom.ac.id</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (password.type === 'password') {
                password.type = 'text';
                toggleIcon.className = 'bi bi-eye-slash';
            } else {
                password.type = 'password';
                toggleIcon.className = 'bi bi-eye';
            }
        });

        // Auto-fill password with NIDN (for demonstration)
        document.getElementById('nidn').addEventListener('input', function() {
            // Optional: Auto-suggest password (remove in production)
            // document.getElementById('password').value = this.value;
        });
    </script>
</body>
</html>