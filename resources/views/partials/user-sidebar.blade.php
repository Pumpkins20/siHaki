<<<<<<< Updated upstream
<div class="sidebar-header">
    <div class="d-flex align-items-center justify-content-center">
        <div class="sidebar-brand">
            <span class="fs-4">SiHaki</span>
=======
<div class="sidebar-header p-3 border-light border-opacity-25">
    <div class="d-flex align-items-center justify-content-center">
        <div class="sidebar-brand">
            <h2 class="align-text-center">SiHaki</h2>
>>>>>>> Stashed changes
        </div>
    </div>
</div>
<nav class="sidebar-nav p-2">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('user.dashboard') }}" 
               class="nav-link text-white {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('user.submissions.index') }}" 
               class="nav-link text-white {{ request()->routeIs('user.submissions.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-plus me-2"></i>
                <span class="sidebar-text">Pengajuan HKI</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('user.history.index') }}" 
               class="nav-link text-white {{ request()->routeIs('user.history.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history me-2"></i>
                <span class="sidebar-text">Riwayat Pengajuan</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('user.panduan.index') }}" 
               class="nav-link text-white {{ request()->routeIs('user.panduan.*') ? 'active' : '' }}">
                <i class="bi bi-book me-2"></i>
                <span class="sidebar-text">Panduan</span>
            </a>
        </li>
        
        <li class="nav-item mt-3">
            <small class="text-white-50 text-uppercase px-3 sidebar-text">Account</small>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('user.profile') }}" 
               class="nav-link text-white {{ request()->routeIs('user.profile.*') ? 'active' : '' }}">
                <i class="bi bi-person me-2"></i>
                <span class="sidebar-text">Profil</span>
            </a>
        </li>
        
        <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                @csrf
                <button type="submit" class="nav-link text-white bg-transparent border-0 w-100 text-start">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    <span class="sidebar-text">Keluar</span>
                </button>
            </form>
        </li>
    </ul>
</nav>

<style>
/* User Sidebar specific responsive styles */
.sidebar .nav-link {
    padding: 0.75rem 1rem;
    border-radius: 0.35rem;
    margin-bottom: 0.25rem;
    transition: all 0.3s;
    position: relative;
    overflow: hidden;
    white-space: nowrap;
}

.sidebar .nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(5px);
}

.sidebar .nav-link.active {
    background: rgba(255, 255, 255, 0.2);
    font-weight: 600;
}

.sidebar.collapsed .sidebar-text {
    opacity: 0;
    transition: opacity 0.3s;
}

.sidebar.collapsed .nav-link {
    text-align: center;
    padding: 0.75rem 0.5rem;
}

@media (max-width: 991.98px) {
    .sidebar:not(.show) .sidebar-text {
        display: none;
    }
    
    .sidebar:not(.show) .nav-link {
        text-align: center;
        padding: 0.75rem 0.5rem;
    }
    
    .sidebar:not(.show) .sidebar-header {
        text-align: center;
    }
    
    .sidebar:not(.show) .sidebar-brand span {
        display: none;
    }
}

@media (max-width: 767.98px) {
    .sidebar.show .sidebar-text {
        display: inline !important;
    }
    
    .sidebar.show .nav-link {
        text-align: left !important;
        padding: 0.75rem 1rem !important;
    }
    
    .sidebar.show .sidebar-brand span {
        display: inline !important;
    }
}
</style>