<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>SiHaki | Sistem Informasi Hak Kekayaan Intelektual</title>
    <meta name="description" content="Sistem Informasi Hak Kekayaan Intelektual STMIK AMIKOM Surakarta">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="stylesheet" href="{{ asset('landing-page/css/detail_ciptaan.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
</head>

<body>
  <!-- Header -->
    <header class="header">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <img src="{{ asset('landing-page/img/logo-amikom.png') }}" alt="Logo AMIKOM" style="height: 40px;">
</a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                          <a class="nav-link" href="{{ route('beranda') }}">Beranda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('pencipta') }}">Pencipta</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('jenis_ciptaan') }}">Jenis Ciptaan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('panduan') }}">Panduan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>


    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-md-12">
                    <div class="detail-container">
                        <h2 class="detail-title">Detail Judul HKI</h2>
                        
                        <!-- Basic Information -->

                         <div class="info-row">
                            <div class="info-label">Judul HKI</div>
                            <div class="info-value">Kelompok</div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Tipe HKI</div>
                            <div class="info-value">Hak Cipta</div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label">Jenis HKI</div>
                            <div class="info-value">Program Komputer</div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label">Uraian Singkat</div>
                            <div class="info-value">
                                XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
                                 XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
                                  XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
                                   XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label">Tanggal Publikasi</div>
                            <div class="info-value">15 Maret 2024</div>
                        </div>
                        
       
                        <!-- Section Divider -->
                        <hr class="section-divider">

                        <!-- Pencipta Utama Section -->
                        <div class="section-title">
                            <i class="bi bi-person-fill me-2"></i>Pencipta Utama
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label">Nama Pencipta Utama</div>
                            <div class="info-value">Dr. Ahmad Fauzi, M.Kom</div>
                        </div>

                        <!-- Section Divider -->
                        <hr class="section-divider">

                        <!-- Anggota Pencipta Section -->
                        <div class="section-title">
                            <i class="bi bi-people-fill me-2"></i>Anggota Pencipta
                        </div>
                        
                        <div class="anggota-list">
                            <div class="anggota-item">
                                <div class="anggota-number">1</div>
                                <div class="anggota-name">Sari Dewi Lestari, S.Kom</div>
                            </div>
                            
                            <div class="anggota-item">
                                <div class="anggota-number">2</div>
                                <div class="anggota-name">Budi Santoso, M.T</div>
                            </div>
                            
                            <div class="anggota-item">
                                <div class="anggota-number">3</div>
                                <div class="anggota-name">Rina Kurniasari, S.T</div>
                            </div>
                        </div>

                        <!-- Section Divider -->
                        <hr class="section-divider">

                        <!-- File Sertifikat Section -->
                        <div class="section-title">
                            <i class="bi bi-file-earmark-pdf me-2"></i>File Sertifikat HKI
                        </div>
                        
                        <div class="file-info">
                            <d class="bi bi-file-earmark-pdf"></d>
                            <div class="file-name">Unduh File Sertifikat HKI</div>
                            <div class="file-size">2.4 MB • PDF Document</div>
                            <a href="#" class="btn-download">
                                <i class="bi bi-download"></i>
                                Lihat Sertifikat 
                            </a> <!-- Hanya Bisa Lihat Sertifikat dan Tidak Bisa Mendownload -->
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button type="button" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Smooth scroll behavior for internal links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add loading effect for download button
        document.querySelector('.btn-download').addEventListener('click', function(e) {
            e.preventDefault();
            
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Mengunduh...';
            this.style.pointerEvents = 'none';
            
            // Simulate download process
            setTimeout(() => {
                this.innerHTML = '<i class="bi bi-check-circle me-2"></i>Berhasil Diunduh';
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.style.pointerEvents = 'auto';
                }, 2000);
            }, 1500);
        });

        // Add hover effects for info rows
        document.querySelectorAll('.info-row').forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f8f9fa';
                this.style.borderRadius = '8px';
                this.style.transition = 'all 0.3s ease';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = 'transparent';
            });
        });

        // Print functionality
        document.querySelector('.btn-primary:last-child').addEventListener('click', function() {
            window.print();
        });
    </script>
</body>
</html>