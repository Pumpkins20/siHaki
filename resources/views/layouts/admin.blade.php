<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - SiHaki</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        /* ✅ RESPONSIVE LAYOUT FIXES */
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
        }

        body {
            font-size: 0.875rem;
            overflow-x: hidden;
        }

        /* ✅ Sidebar Responsive Design */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        /* ✅ Main Content Responsive */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
            width: calc(100% - var(--sidebar-width));
            padding: 0;
        }

        .main-content.expanded {
            margin-left: var(--sidebar-collapsed-width);
            width: calc(100% - var(--sidebar-collapsed-width));
        }

        /* ✅ Content Container */
        .content-wrapper {
            padding: 1rem;
            max-width: 100%;
            overflow-x: auto;
        }

        /* ✅ Navbar */
        .admin-navbar {
            background: #fff;
            border-bottom: 1px solid #e3e6f0;
            padding: 0.5rem 1rem;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        /* ✅ Mobile Toggle Button */
        .mobile-toggle {
            display: none;
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1001;
            background: var(--bs-primary);
            border: none;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        /* ✅ Responsive Breakpoints */
        
        /* Large screens - Desktop */
        @media (min-width: 1200px) {
            .main-content {
                margin-left: var(--sidebar-width);
                width: calc(100% - var(--sidebar-width));
            }
            
            .content-wrapper {
                padding: 1.5rem;
            }
        }

        /* Medium screens - Tablet Landscape */
        @media (max-width: 1199.98px) and (min-width: 992px) {
            .sidebar {
                width: 200px;
            }
            
            .main-content {
                margin-left: 200px;
                width: calc(100% - 200px);
            }
            
            .content-wrapper {
                padding: 1rem;
            }
        }

        /* Small to Medium screens - Tablet Portrait */
        @media (max-width: 991.98px) and (min-width: 768px) {
            .sidebar {
                width: var(--sidebar-collapsed-width);
                overflow: hidden;
            }
            
            .main-content {
                margin-left: var(--sidebar-collapsed-width);
                width: calc(100% - var(--sidebar-collapsed-width));
            }
            
            .content-wrapper {
                padding: 0.75rem;
            }
            
            /* Hide sidebar text on tablet */
            .sidebar .nav-link span {
                display: none;
            }
            
            .sidebar .nav-link {
                text-align: center;
                padding: 0.75rem 0.5rem;
            }
        }

        /* Mobile screens */
        @media (max-width: 767.98px) {
            .sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-width);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .content-wrapper {
                padding: 0.5rem;
            }
            
            .mobile-toggle {
                display: block;
            }
            
            /* Mobile overlay */
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
        }

        /* ✅ Zoom Out Responsiveness */
        @media (max-width: 1400px) {
            .table-responsive {
                font-size: 0.8rem;
            }
            
            .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.8rem;
            }
            
            .card-body {
                padding: 0.75rem;
            }
        }

        @media (max-width: 1200px) {
            .container-fluid {
                padding: 0;
            }
            
            .card {
                margin-bottom: 0.75rem;
            }
            
            .h1, .h2, .h3 {
                font-size: calc(1rem + 0.5vw);
            }
        }

        /* ✅ Very small zoom or high resolution */
        @media (max-width: 900px) {
            .col-xl-3,
            .col-xl-4,
            .col-xl-6,
            .col-xl-8 {
                margin-bottom: 1rem;
            }
            
            .btn-group .btn {
                padding: 0.2rem 0.4rem;
                font-size: 0.75rem;
            }
            
            .pagination .page-link {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
        }

        /* ✅ Table Responsive Improvements */
        .table-responsive {
            border-radius: 0.375rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .table th,
        .table td {
            border-top: 1px solid #e3e6f0;
            vertical-align: middle;
            padding: 0.75rem;
        }

        @media (max-width: 768px) {
            .table th,
            .table td {
                padding: 0.5rem 0.25rem;
                font-size: 0.8rem;
            }
            
            .table th:nth-child(n+4),
            .table td:nth-child(n+4) {
                display: none;
            }
        }

        /* ✅ Card Improvements */
        .card {
            border: 0;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1rem;
        }

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }

        /* ✅ Alert Responsive */
        .alert {
            margin-bottom: 1rem;
            border: 0;
            border-radius: 0.35rem;
        }

        /* ✅ Form Responsive */
        @media (max-width: 576px) {
            .form-control,
            .form-select {
                font-size: 0.9rem;
            }
            
            .btn {
                font-size: 0.8rem;
                padding: 0.375rem 0.75rem;
            }
        }

        /* ✅ Sidebar Toggle Animation */
        .sidebar-toggle {
            background: none;
            border: none;
            color: #5a5c69;
            font-size: 1.25rem;
            padding: 0.5rem;
            border-radius: 0.35rem;
            transition: all 0.3s;
        }

        .sidebar-toggle:hover {
            background: #eaecf4;
            color: #3a3b45;
        }

        /* ✅ Print Styles */
        @media print {
            .sidebar,
            .mobile-toggle,
            .sidebar-toggle,
            .btn,
            .pagination {
                display: none !important;
            }
            
            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }

        /* ✅ High DPI / Retina Display Support */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .table {
                font-size: 0.9rem;
            }
            
            .btn {
                font-weight: 500;
            }
        }

        /* ✅ Focus and Accessibility */
        .btn:focus,
        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            border-color: #667eea;
        }

        /* ✅ Loading States */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        /* ✅ Utility Classes */
        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }

        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Mobile Toggle Button -->
    <button class="mobile-toggle d-lg-none" id="mobileToggle">
        <i class="bi bi-list"></i>
    </button>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            @include('partials.admin-sidebar')
        </div>
        
        <!-- Main Content -->
        <div class="main-content flex-grow-1" id="mainContent">

            <!-- Page Content -->
            <div class="content-wrapper">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // ✅ Responsive Sidebar Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const mobileToggle = document.getElementById('mobileToggle');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            // Mobile toggle
            if (mobileToggle) {
                mobileToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                });
            }
            
            // Desktop toggle (collapse/expand)
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                    
                    // Save state to localStorage
                    const isCollapsed = sidebar.classList.contains('collapsed');
                    localStorage.setItem('sidebarCollapsed', isCollapsed);
                });
                
                // Restore sidebar state
                const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (isCollapsed) {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('expanded');
                }
            }
            
            // Overlay click to close mobile sidebar
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                });
            }
            
            // Close mobile sidebar on window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                }
            });
            
            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
        
        // ✅ Table Responsive Helper
        function makeTableResponsive() {
            const tables = document.querySelectorAll('.table-responsive table');
            tables.forEach(table => {
                if (window.innerWidth < 768) {
                    table.classList.add('table-sm');
                } else {
                    table.classList.remove('table-sm');
                }
            });
        }
        
        // Run on load and resize
        window.addEventListener('load', makeTableResponsive);
        window.addEventListener('resize', makeTableResponsive);
    </script>
    
    @stack('scripts')
</body>
</html>
