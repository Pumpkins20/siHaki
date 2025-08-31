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
                            <div class="info-value">{{ $ciptaan->judul ?? 'Tidak tersedia' }}</div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Tipe HKI</div>
                            <div class="info-value">{{ $ciptaan->tipe_hki ?? 'Tidak tersedia' }}</div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label">Jenis HKI</div>
                            <div class="info-value">{{ $ciptaan->jenis_hki ?? 'Tidak tersedia' }}</div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label">Uraian Singkat</div>
                            <div class="info-value">{{ $ciptaan->uraian_singkat ?? 'Tidak tersedia' }}</div>
                        </div>
                       
                        
       
                        <!-- Section Divider -->
                        <hr class="section-divider">

                        <!-- Pencipta Utama Section -->
                        <div class="section-title">
                            <i class="bi bi-person-fill me-2"></i>Pencipta Utama
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label">Nama Pencipta Utama</div>
                            <div class="info-value">{{ $ciptaan->pencipta_utama ?? 'Tidak tersedia' }}</div>
                        </div>

                        <!-- Section Divider -->
                        <hr class="section-divider">

                        <!-- Anggota Pencipta Section -->
                        @if(isset($ciptaan->anggota_pencipta) && count($ciptaan->anggota_pencipta) > 0)
                            <!-- Section Divider -->
                            <hr class="section-divider">
                            
                            <div class="section-title">
                                <i class="bi bi-people-fill me-2"></i>Anggota Pencipta
                            </div>
                            
                            <div class="anggota-list">
                                @foreach($ciptaan->anggota_pencipta as $index => $anggota)
                                    <div class="anggota-item">
                                        <div class="anggota-number">{{ $index + 1 }}</div>
                                        <div class="anggota-name">{{ $anggota }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Section Divider -->
                        <hr class="section-divider">

                        <!-- File Sertifikat Section -->
                        <div class="section-title">
                            <i class="bi bi-file-earmark-pdf me-2"></i>File Sertifikat HKI
                        </div>
                        
                        @if(isset($ciptaan->has_certificate) && $ciptaan->has_certificate)
                            <div class="file-info">
                                <i class="bi bi-file-earmark-pdf"></i>
                                <div class="file-name">Sertifikat HKI {{ $ciptaan->judul ?? 'Ciptaan' }}</div>
                                <div class="file-size">PDF Document • Tersertifikasi</div>
                                <button class="btn-download" onclick="viewCertificate('{{ $ciptaan->id }}')">
                                    <i class="bi bi-eye"></i>
                                    Lihat Sertifikat 
                                </button>
                            </div>
                        @else
                            <div class="file-info">
                                <i class="bi bi-file-earmark-x text-muted"></i>
                                <div class="file-name text-muted">Sertifikat belum tersedia</div>
                                <div class="file-size text-muted">Pengajuan belum disetujui atau sertifikat belum diterbitkan</div>
                            </div>
                        @endif

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

        // Certificate viewing function
        function viewCertificate(submissionId) {
            const url = `/sertifikat/view/${submissionId}`;
            window.open(url, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes,toolbar=no,menubar=no');
        }
    </script>
</body>
</html>