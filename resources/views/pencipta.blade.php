<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>SiHaki | Pencipta</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Nunito+Sans:ital,wght@0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Bootstrap CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">

  <style>
    :root {
      --default-font: "Roboto", system-ui, -apple-system, sans-serif;
      --heading-font: "Nunito Sans", sans-serif;
      --nav-font: "Poppins", sans-serif;
      --background-color: #ffffff;
      --default-color: #555555;
      --heading-color: #333333;
      --accent-color: #6f42c1;
      --nav-color: #828c91;
      --nav-hover-color: #6f42c1;
    }

    body {
      color: var(--default-color);
      background-color: var(--background-color);
      font-family: var(--default-font);
    }

    .header {
      background-color: var(--background-color);
      padding: 15px 0;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      position: sticky;
      top: 0;
      z-index: 999;
    }

    .header .logo img {
      max-height: 40px;
      margin-right: 8px;
    }

    /* Navigation */
    .navmenu ul {
      margin: 0;
      padding: 0;
      display: flex;
      list-style: none;
      align-items: center;
    }

    .navmenu a {
      color: var(--nav-color);
      padding: 18px 15px;
      font-size: 16px;
      font-family: var(--nav-font);
      text-decoration: none;
      transition: 0.3s;
    }

    .navmenu a:hover,
    .navmenu .active {
      color: var(--nav-hover-color);
    }

    .dropdown {
      position: relative;
    }

    .dropdown ul {
      position: absolute;
      top: 100%;
      left: 0;
      background: white;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      border-radius: 4px;
      padding: 10px 0;
      min-width: 200px;
      display: none;
    }

    .dropdown:hover ul {
      display: block;
    }

    .dropdown ul a {
      padding: 10px 20px;
      color: var(--default-color);
      display: block;
    }

    /* Sections */
    .section {
      padding: 60px 0;
    }

    .services {
      background-color: #f8f9fa;
    }

    /* Pagination */
    .pagination-top, .pagination-bottom {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
      margin: 20px 0;
    }

    .pagination-btn {
      padding: 8px 12px;
      border: 1px solid #ddd;
      background: #fff;
      color: #333;
      text-decoration: none;
      border-radius: 4px;
      transition: all 0.3s;
    }

    .pagination-btn:hover {
      background: #007bff;
      color: #fff;
      text-decoration: none;
    }

    .pagination-btn.active {
      background: #007bff;
      color: #fff;
      border-color: #007bff;
    }

    /* Dosen Cards */
    .dosen-card {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
      overflow: hidden;
      transition: transform 0.3s;
    }

    .dosen-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .dosen-card .card-body {
      padding: 20px;
    }

    .dosen-profile {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .dosen-avatar {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
    }

    .dosen-info h5 {
      margin: 0 0 5px 0;
      color: #333;
      font-weight: 600;
    }

    .dosen-info .nidn {
      color: #666;
      font-size: 14px;
      margin-bottom: 5px;
    }

    .dosen-info .position {
      color: #777;
      font-size: 13px;
    }

    .pengajuan-section {
      margin-top: 20px;
      padding-top: 15px;
      border-top: 1px solid #eee;
    }

    .pengajuan-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }

    .pengajuan-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 8px 0;
      border-bottom: 1px solid #f5f5f5;
    }

    .pengajuan-item:last-child {
      border-bottom: none;
    }

    .pengajuan-name {
      color: #555;
      font-size: 14px;
    }

    .pengajuan-date {
      color: #888;
      font-size: 13px;
    }

    .total-hki {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 15px;
      padding-top: 10px;
      border-top: 1px solid #eee;
      font-weight: 600;
    }

    .selanjutnya-link {
      color: #007bff;
      text-decoration: none;
      font-size: 14px;
    }

    .selanjutnya-link:hover {
      text-decoration: underline;
    }

    /* Mobile Navigation */
    @media (max-width: 1199px) {
      .mobile-nav-toggle {
        color: var(--nav-color);
        font-size: 28px;
        cursor: pointer;
        display: block;
      }

      .navmenu ul {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        flex-direction: column;
        padding: 20px;
      }

      .mobile-nav-active .navmenu ul {
        display: flex;
      }

      .navmenu a {
        padding: 10px 0;
      }
    }

    @media (min-width: 1200px) {
      .mobile-nav-toggle {
        display: none;
      }
    }
  </style>
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center">
    <div class="container position-relative d-flex align-items-center">
      <a href="index.html" class="logo d-flex align-items-center me-auto">
        <img src="https://via.placeholder.com/40x40?text=LOGO" alt="Logo">
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="#beranda">Beranda</a></li>
          <li><a href="#" class="active nav-link">Pencipta</a></li>
          <li><a href="#services">Jenis Ciptaan</a></li>
          <li><a href="#portfolio">Panduan</a></li>
          <li><a href="#team">Team</a></li>
          <li class="dropdown">
            <a href="#"><span>Pengajuan HKI</span> <i class="bi bi-chevron-down"></i></a>
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
    <!-- Filter Section -->
    <section id="ciptaan" class="services section">
      <div class="container">
        <div class="row gy-4 justify-content-center">
          <!-- Dropdown Filter -->
          <div class="col-md-3">
            <label for="filter1" class="form-label">Filter Berdasarkan</label>
            <select id="filter1" class="form-select" onchange="updateFilterOptions()">
              <option value="">-- Pilih Kategori --</option>
              <option value="nama">Nama Dosen</option>
              <option value="prodi">Program Studi</option>
              <option value="jenis">Jenis Pengajuan</option>
              <option value="tahun">Tahun</option>
            </select>
          </div>

          <!-- Sub-Option Filter -->
          <div class="col-md-3">
            <label for="filter2" class="form-label">Opsi</label>
            <select id="filter2" class="form-select">
              <option value="">-- Pilih Opsi --</option>
            </select>
          </div>

          <!-- Search Input -->
          <div class="col-md-4">
            <label for="searchInput" class="form-label">Cari</label>
            <input type="text" class="form-control" id="searchInput" placeholder="Masukkan kata kunci pencarian">
          </div>

          <!-- Submit Button -->
          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100" onclick="performSearch()">Cari</button>
          </div>
        </div>
      </div>
    </section>

    <!-- Pagination Top -->
    <div class="pagination-top">
      <a href="#" class="pagination-btn" id="prevTop">Sebelumnya</a>
      <a href="#" class="pagination-btn active">1</a>
      <a href="#" class="pagination-btn">2</a>
      <a href="#" class="pagination-btn">3</a>
      <a href="#" class="pagination-btn">4</a>
      <a href="#" class="pagination-btn" id="nextTop">Selanjutnya</a>
    </div>

    <!-- Dosen Cards Section -->
    <section class="dosen-section section">
      <div class="container" id="dosenContainer">
        
        <!-- Dosen Card 1 -->
        <div class="dosen-card" data-nama="Dr. Ahmad Surya, M.Kom" data-prodi="S1 Informatika" data-tahun="2025">
          <div class="card-body">
            <div class="dosen-profile">
              <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face" alt="Dosen" class="dosen-avatar">
              <div class="dosen-info">
                <h5>Dr. Ahmad Surya, M.Kom</h5>
                <div class="nidn">0012345678</div>
                <div class="position">Dosen S1 Informatika - STMIK Amikom Surakarta</div>
              </div>
            </div>
            
            <div class="pengajuan-section">
              <div class="pengajuan-header">
                <strong>Pengajuan Terbaru</strong>
              </div>
              
              <div class="pengajuan-item">
                <span class="pengajuan-name">E-Commerce Platform - Program Komputer</span>
                <span class="pengajuan-date">10 Jan 2025</span>
              </div>
              
              <div class="pengajuan-item">
                <span class="pengajuan-name">Video Tutorial Pemrograman - Sinematografi</span>
                <span class="pengajuan-date">07 Jan 2025</span>
              </div>
              
              <div class="pengajuan-item">
                <span class="pengajuan-name">Sistem Inventory - Program Komputer</span>
                <span class="pengajuan-date">03 Jan 2025</span>
              </div>
              
              <div class="total-hki">
                <span>Jumlah HKI Total: 15</span>
                <a href="#" class="selanjutnya-link">Selanjutnya...</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Dosen Card 2 -->
        <div class="dosen-card" data-nama="Prof. Sari Indah, Ph.D" data-prodi="S1 Sistem Informasi" data-tahun="2025">
          <div class="card-body">
            <div class="dosen-profile">
              <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop&crop=face" alt="Dosen" class="dosen-avatar">
              <div class="dosen-info">
                <h5>Prof. Sari Indah, Ph.D</h5>
                <div class="nidn">0012345679</div>
                <div class="position">Dosen S1 Sistem Informasi - STMIK Amikom Surakarta</div>
              </div>
            </div>
            
            <div class="pengajuan-section">
              <div class="pengajuan-header">
                <strong>Pengajuan Terbaru</strong>
              </div>
              
              <div class="pengajuan-item">
                <span class="pengajuan-name">Machine Learning Model - Program Komputer</span>
                <span class="pengajuan-date">09 Jan 2025</span>
              </div>
              
              <div class="pengajuan-item">
                <span class="pengajuan-name">Dataset Penelitian AI - Basis Data</span>
                <span class="pengajuan-date">06 Jan 2025</span>
              </div>
              
              <div class="pengajuan-item">
                <span class="pengajuan-name">Aplikasi IoT Smart Home - Program Komputer</span>
                <span class="pengajuan-date">02 Jan 2025</span>
              </div>
              
              <div class="total-hki">
                <span>Jumlah HKI Total: 23</span>
                <a href="#" class="selanjutnya-link">Selanjutnya...</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Dosen Card 3 -->
        <div class="dosen-card" data-nama="Drs. Budi Santoso, M.T" data-prodi="D3 Manajemen Informatika" data-tahun="2024">
          <div class="card-body">
            <div class="dosen-profile">
              <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face" alt="Dosen" class="dosen-avatar">
              <div class="dosen-info">
                <h5>Drs. Budi Santoso, M.T</h5>
                <div class="nidn">0012345680</div>
                <div class="position">Dosen D3 Manajemen Informatika - STMIK Amikom Surakarta</div>
              </div>
            </div>
            
            <div class="pengajuan-section">
              <div class="pengajuan-header">
                <strong>Pengajuan Terbaru</strong>
              </div>
              
              <div class="pengajuan-item">
                <span class="pengajuan-name">Sistem Monitoring Jaringan - Program Komputer</span>
                <span class="pengajuan-date">11 Jan 2025</span>
              </div>
              
              <div class="pengajuan-item">
                <span class="pengajuan-name">Database Perpustakaan Digital - Basis Data</span>
                <span class="pengajuan-date">08 Jan 2025</span>
              </div>
              
              <div class="pengajuan-item">
                <span class="pengajuan-name">Aplikasi Absensi QR Code - Program Komputer</span>
                <span class="pengajuan-date">04 Jan 2025</span>
              </div>
              
              <div class="total-hki">
                <span>Jumlah HKI Total: 8</span>
                <a href="#" class="selanjutnya-link">Selanjutnya...</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Dosen Card 4 -->
        <div class="dosen-card" data-nama="Dr. Maya Dewi, S.Kom, M.Cs" data-prodi="S1 Informatika" data-tahun="2024">
          <div class="card-body">
            <div class="dosen-profile">
              <img src="https://images.unsplash.com/photo-1489424731084-a5d8b219a5bb?w=150&h=150&fit=crop&crop=face" alt="Dosen" class="dosen-avatar">
              <div class="dosen-info">
                <h5>Dr. Maya Dewi, S.Kom, M.Cs</h5>
                <div class="nidn">0012345681</div>
                <div class="position">Dosen S1 Informatika - STMIK Amikom Surakarta</div>
              </div>
            </div>
            
            <div class="pengajuan-section">
              <div class="pengajuan-header">
                <strong>Pengajuan Terbaru</strong>
              </div>
              
              <div class="pengajuan-item">
                <span class="pengajuan-name">Aplikasi Manajemen Proyek - Program Komputer</span>
                <span class="pengajuan-date">13 Jan 2025</span>
              </div>
              
              <div class="pengajuan-item">
                <span class="pengajuan-name">Film Dokumenter Teknologi - Sinematografi</span>
                <span class="pengajuan-date">10 Jan 2025</span>
              </div>
              
              <div class="pengajuan-item">
                <span class="pengajuan-name">System Database Klinik - Basis Data</span>
                <span class="pengajuan-date">07 Jan 2025</span>
              </div>
              
              <div class="total-hki">
                <span>Jumlah HKI Total: 12</span>
                <a href="#" class="selanjutnya-link">Selanjutnya...</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Dosen Card 5 -->
        <div class="dosen-card" data-nama="Ir. Andi Permana, M.Kom" data-prodi="S1 Sistem Informasi" data-tahun="2023">
          <div class="card-body">
            <div class="dosen-profile">
              <img src="https://images.unsplash.com/photo-1494790108755-2616b2da29c3?w=150&h=150&fit=crop&crop=face" alt="Dosen" class="dosen-avatar">
              <div class="dosen-info">
                <h5>Ir. Andi Permana, M.Kom</h5>
                <div class="nidn">0012345682</div>
                <div class="position">Dosen S1 Sistem Informasi - STMIK Amikom Surakarta</div>
              </div>
            </div>
            
            <div class="pengajuan-section">
              <div class="pengajuan-header">
                <strong>Pengajuan Terbaru</strong>
              </div>
              
              <div class="pengajuan-item">
                <span class="pengajuan-name">Sistem Informasi Akademik - Program Komputer</span>
                <span class="pengajuan-date">12 Jan 2025</span>
              </div>
              
              <div class="pengajuan-item">
                <span class="pengajuan-name">Aplikasi Mobile Learning - Program Komputer</span>
                <span class="pengajuan-date">08 Jan 2025</span>
              </div>
              
              <div class="pengajuan-item">
                <span class="pengajuan-name">Database Mahasiswa - Basis Data</span>
                <span class="pengajuan-date">05 Jan 2025</span>
              </div>
              
              <div class="total-hki">
                <span>Jumlah HKI Total: 18</span>
                <a href="#" class="selanjutnya-link">Selanjutnya...</a>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>

    <!-- Pagination Bottom -->
    <div class="pagination-bottom">
      <a href="#" class="pagination-btn" id="prevBottom">Sebelumnya</a>
      <a href="#" class="pagination-btn active">1</a>
      <a href="#" class="pagination-btn">2</a>
      <a href="#" class="pagination-btn">3</a>
      <a href="#" class="pagination-btn">4</a>
      <a href="#" class="pagination-btn" id="nextBottom">Selanjutnya</a>
    </div>
  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

  <script>
    // Data dosen untuk pencarian
    const dosenData = [
      { nama: "Dr. Ahmad Surya, M.Kom", prodi: "S1 Informatika" },
      { nama: "Prof. Sari Indah, Ph.D", prodi: "S1 Sistem Informasi" },
      { nama: "Drs. Budi Santoso, M.T", prodi: "D3 Manajemen Informatika" },
      { nama: "Dr. Maya Dewi, S.Kom, M.Cs", prodi: "S1 Informatika" },
      { nama: "Ir. Andi Permana, M.Kom", prodi: "S1 Sistem Informasi" }
    ];

    function updateFilterOptions() {
      const filter1 = document.getElementById("filter1").value;
      const filter2 = document.getElementById("filter2");

      filter2.innerHTML = "";
      let options = [];

      if (filter1 === "nama") {
        options = ["A - Z", "Z - A"];
      } else if (filter1 === "prodi") {
        options = ["S1 Informatika", "S1 Sistem Informasi", "D3 Manajemen Informatika"];
      } else if (filter1 === "jenis") {
        options = ["Program Komputer", "Sinematografi", "Basis Data"];
      } else if (filter1 === "tahun") {
        options = ["2023", "2024", "2025"];
      }

      filter2.appendChild(new Option("-- Pilih Opsi --", ""));
      options.forEach(opt => {
        filter2.appendChild(new Option(opt, opt));
      });
    }

    function performSearch() {
      const filter1 = document.getElementById("filter1").value;
      const filter2 = document.getElementById("filter2").value;
      const searchInput = document.getElementById("searchInput").value.toLowerCase();
      const dosenCards = document.querySelectorAll(".dosen-card");

      let filteredCards = Array.from(dosenCards);

      // Filter berdasarkan kategori
      if (filter1 && filter2) {
        filteredCards = filteredCards.filter(card => {
          if (filter1 === "nama") {
            // Logika pengurutan nama akan diterapkan setelah filtering
            return true;
          } else if (filter1 === "prodi") {
            const cardProdi = card.dataset.prodi;
            return cardProdi === filter2;
          } else if (filter1 === "jenis") {
            const pengajuanItems = card.querySelectorAll(".pengajuan-name");
            return Array.from(pengajuanItems).some(item => 
              item.textContent.toLowerCase().includes(filter2.toLowerCase())
            );
          } else if (filter1 === "tahun") {
            const cardTahun = card.dataset.tahun;
            return cardTahun === filter2;
          }
          return true;
        });
      }

      // Filter berdasarkan pencarian teks
      if (searchInput) {
        filteredCards = filteredCards.filter(card => {
          const nama = card.dataset.nama.toLowerCase();
          const prodi = card.dataset.prodi.toLowerCase();
          const pengajuanItems = card.querySelectorAll(".pengajuan-name");
          const pengajuanText = Array.from(pengajuanItems)
            .map(item => item.textContent.toLowerCase())
            .join(" ");
          
          return nama.includes(searchInput) || 
                 prodi.includes(searchInput) || 
                 pengajuanText.includes(searchInput);
        });
      }

      // Urutkan berdasarkan nama jika dipilih
      if (filter1 === "nama" && filter2) {
        filteredCards.sort((a, b) => {
          const namaA = a.dataset.nama;
          const namaB = b.dataset.nama;
          if (filter2 === "A - Z") {
            return namaA.localeCompare(namaB);
          } else if (filter2 === "Z - A") {
            return namaB.localeCompare(namaA);
          }
          return 0;
        });
      }

      // Sembunyikan semua card
      dosenCards.forEach(card => card.style.display = "none");

      // Tampilkan card yang sesuai filter
      filteredCards.forEach(card => card.style.display = "block");

      // Urutkan ulang di DOM
      const container = document.getElementById("dosenContainer");
      filteredCards.forEach(card => container.appendChild(card));
    }

    // Pagination functionality
    document.addEventListener('DOMContentLoaded', function() {
      const paginationBtns = document.querySelectorAll('.pagination-btn');
      const prevBtns = document.querySelectorAll('#prevTop, #prevBottom');
      const nextBtns = document.querySelectorAll('#nextTop, #nextBottom');

      paginationBtns.forEach(btn => {
        if (!btn.id.includes('prev') && !btn.id.includes('next')) {
          btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            document.querySelectorAll('.pagination-btn:not([id])').forEach(b => {
              b.classList.remove('active');
            });
            
            const pageNumber = this.textContent;
            document.querySelectorAll('.pagination-btn').forEach(b => {
              if (b.textContent === pageNumber && !b.id.includes('prev') && !b.id.includes('next')) {
                b.classList.add('active');
              }
            });
          });
        }
      });

      prevBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          console.log('Previous page clicked');
        });
      });

      nextBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          console.log('Next page clicked');
        });
      });

      // Mobile navigation toggle
      const mobileNavToggle = document.querySelector('.mobile