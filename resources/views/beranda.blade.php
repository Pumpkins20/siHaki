<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>SiHaki | Home</title>
    <meta name="description" content="Sistem Informasi Hak Kekayaan Intelektual">
    <meta name="keywords" content="HKI, STMIK AMIKOM, Surakarta">

    <!-- Favicons -->
    <link href="{{ asset('landing-page/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('landing-page/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@300;400;500;600;700&family=Nunito+Sans:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- CSS Files -->
    <link href="{{ asset('landing-page/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('landing-page/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('landing-page/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('landing-page/css/main.css') }}" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="index-page">

    <!-- Header -->
    <header id="header" class="header d-flex align-items-center sticky-top">
        <div class="container position-relative d-flex align-items-center">
            
            <a href="{{ url('/') }}" class="logo d-flex align-items-center me-auto">
                <img src="{{ asset('landing-page/img/logo-amikom.png') }}" alt="Logo AMIKOM" style="height: 40px;">
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="#hero" class="active nav-link">Home</a></li>
                    <li><a href="{{ route('pencipta') }}">Pencipta</a></li>
                    <li><a href="#services">Jenis Ciptaan</a></li>
                    <li><a href="#portfolio">Panduan</a></li>
                    <li><a href="#team">Team</a></li>
                    <li class="dropdown">
                        <a href="#"><span>Pengajuan HKI</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                        <ul>
                            <li><a href="#">Informasi Pengajuan</a></li>
                            <li><a href="#">Status Pengajuan</a></li>
                            <li><a href="#">Riwayat Pengajuan</a></li>
                        </ul>
                    </li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

        </div>
    </header>

    <main class="main">

        <!-- Hero Section -->
        <section id="hero" class="hero section">
            <div class="container">
                <div class="row gy-4">
                    <div class="col-lg-7 order-2 order-lg-1 d-flex flex-column justify-content-center">
                        <h1>Ajukan HKI Sekarang</h1>
                        <p>Punya karya ilmiah, desain, atau software? Daftarkan HKI-nya dan dapatkan perlindungan serta pengakuan atas kreativitasmu!</p>
                        <div class="d-flex">
                            <a href="#about" class="btn-get-started">Ajukan Sekarang</a>
                            <a href="#" class="btn-watch-video d-flex align-items-center">
                                <i class="bi bi-download"></i>
                                <span>Download Panduan</span>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-5 order-1 order-lg-2 hero-img">
                        <img src="{{ asset('landing-page/img/hero-img.png') }}" class="img-fluid" alt="Hero Image">
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="about section">
            <div class="container" data-aos="fade-up">
                <div class="section-title">
                    <h2>Statistik Dosen</h2>
                    <p>Data jabatan akademik dosen di Indonesia</p>
                </div>
                
                <div class="row gx-5 align-items-center">
                    <!-- Pie Chart -->
                    <div class="col-lg-6 d-flex justify-content-center">
                        <canvas id="academicRankChart" style="max-width: 100%; height: 300px;"></canvas>
                    </div>

                    <!-- Legend -->
                    <div class="col-lg-6">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="p-3 border rounded bg-light text-dark">
                                    <strong>Lektor:</strong> 114,105
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 border rounded bg-light text-dark">
                                    <strong>Lektor Kepala:</strong> 30,714
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 border rounded bg-light text-dark">
                                    <strong>Asisten Ahli:</strong> 72,123
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 border rounded bg-light text-dark">
                                    <strong>Profesor:</strong> 10,722
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 border rounded bg-light text-dark">
                                    <strong>Unknown:</strong> 78,880
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 border rounded bg-light text-dark">
                                    <strong>Tenaga Pengajar:</strong> 0
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Daftar Ciptaan Section -->
        <section id="ciptaan" class="services section">
            <div class="container section-title" data-aos="fade-up">
                <h2>Daftar Ciptaan</h2>
                <p>Pilih kriteria pencarian dan masukkan kata kunci</p>
            </div>

            <div class="container">
                <div class="row gy-4 justify-content-center" data-aos="fade-up">
                    <div class="col-md-3">
                        <label for="filter1" class="form-label">Filter Berdasarkan</label>
                        <select id="filter1" class="form-select" onchange="updateFilterOptions()">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="nama">Nama</option>
                            <option value="jenis">Jenis Pengajuan</option>
                            <option value="tahun">Tahun</option>
                            <option value="jurusan">Jurusan</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="filter2" class="form-label">Opsi</label>
                        <select id="filter2" class="form-select">
                            <option value="">-- Pilih Opsi --</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="searchInput" class="form-label">Cari</label>
                        <input type="text" class="form-control" id="searchInput" placeholder="Masukkan kata kunci">
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100" onclick="performSearch()">Cari</button>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer id="footer" class="footer">
        <div class="footer-top">
            <div class="container">
                <div class="row gy-3">
                    <!-- Kontak -->
                    <div class="col-lg-8 col-md-6 footer-about">
                        <a href="#" class="logo d-flex align-items-center mb-3">
                            <img src="{{ asset('landing-page/img/logo-footer.png') }}" alt="Logo" class="img-fluid" style="max-height: 70px;">
                        </a>
                        <div class="footer-contact">
                            <p class="mb-1">Alamat: Jl. Veteran, Notosuman, Singopuran, Kec. Kartasura,</p>
                            <p class="mb-1">Kabupaten Sukoharjo, Provinsi Jawa Tengah 57164</p>
                            <p class="mb-1"><strong>Telp/Fax:</strong> (0271) 7851507</p>
                            <p class="mb-1"><strong>Email:</strong> amikomsolo@amikomsolo.ac.id</p>
                            <p class="mb-1"><strong>WhatsApp:</strong> 081329303450</p>
                        </div>
                    </div>

                    <!-- Layanan -->
                    <div class="col-lg-4 col-md-6 text-end">
                        <h5 class="mb-3"><strong>Layanan</strong></h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="#" class="text-white text-decoration-none">Pencipta</a></li>
                            <li class="mb-2"><a href="#" class="text-white text-decoration-none">Jenis Ciptaan</a></li>
                            <li class="mb-2"><a href="#" class="text-white text-decoration-none">Ajukan HKI</a></li>
                            <li><a href="#" class="text-white text-decoration-none">Panduan</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="copyright text-white py-2">
            <div class="container d-flex flex-column flex-lg-row justify-content-between align-items-center">
                <div>
                    © Copyright <strong><span>STMIK AMIKOM Surakarta</span></strong>. All Rights Reserved
                </div>
                <div class="social-links">
                    <a href="#" class="me-3 text-white"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-white"><i class="bi bi-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- JavaScript Files -->
    <script src="{{ asset('landing-page/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('landing-page/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('landing-page/js/main.js') }}"></script>

    <!-- Custom Scripts -->
    <script>
        // Chart JS Script
        const ctx = document.getElementById('academicRankChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Lektor', 'Lektor Kepala', 'Asisten Ahli', 'Profesor', 'Unknown', 'Tenaga Pengajar'],
                datasets: [{
                    data: [114105, 30714, 72123, 10722, 78880, 0],
                    backgroundColor: ['#3ac9d6', '#c89ce4', '#5da8ef', '#ff8c8c', '#999999', '#ffcc66'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Dynamic Filter Options
        function updateFilterOptions() {
            const filter1 = document.getElementById("filter1").value;
            const filter2 = document.getElementById("filter2");

            filter2.innerHTML = "";

            let options = [];

            switch(filter1) {
                case "nama":
                    options = ["A s/d Z", "Z s/d A"];
                    break;
                case "jenis":
                    options = ["Program Komputer", "Sinematografi", "Basis Data"];
                    break;
                case "tahun":
                    options = ["2023", "2024", "2025"];
                    break;
                case "jurusan":
                    options = ["S1 Informatika", "D3 Manajemen Informatika"];
                    break;
            }

            filter2.appendChild(new Option("-- Pilih Opsi --", ""));
            options.forEach(opt => {
                filter2.appendChild(new Option(opt, opt));
            });
        }

        // Search Function
        function performSearch() {
            const filter1 = document.getElementById("filter1").value;
            const filter2 = document.getElementById("filter2").value;
            const searchInput = document.getElementById("searchInput").value;

            // Implement search logic here
            console.log("Search Parameters:", {
                category: filter1,
                option: filter2,
                keyword: searchInput
            });

            // You can add AJAX call here to search data
        }

        // Initialize AOS
        AOS.init();
    </script>

</body>

</html>