<div class="d-flex flex-column h-100 p-3 text-white">
    <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <i class="bi bi-shield-check fs-4 me-2"></i>
        <span class="fs-4">SiHaki Admin</span>
    </a>
</div>

<nav class="sidebar-nav p-2">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" 
               class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('admin.submissions.index') }}" 
               class="nav-link text-white {{ request()->routeIs('admin.submissions.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-check me-2"></i>
                <span class="sidebar-text">Review Submissions</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('admin.users.index') }}" 
               class="nav-link text-white {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people me-2"></i>
                <span class="sidebar-text">Kelola Users</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('admin.certificates.index') }}" 
               class="nav-link text-white {{ request()->routeIs('admin.certificates.*') ? 'active' : '' }}">
                <i class="bi bi-award me-2"></i>
                <span class="sidebar-text">Kirim Sertifikat</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('admin.review-history.index') }}" 
               class="nav-link text-white {{ request()->routeIs('admin.review-history.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history me-2"></i>
                <span class="sidebar-text">Riwayat Peninjauan</span>
            </a>
        </li>
        
        <li class="nav-item mt-3">
            <small class="text-white-50 text-uppercase px-3 sidebar-text">Account</small>
        </li>
        
        <li class="nav-item">
            <a href="#" class="nav-link text-white">
                <i class="bi bi-person me-2"></i>
                <span class="sidebar-text">Profile</span>
            </a>
        </li>
        
        <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                @csrf
                <button type="submit" class="nav-link text-white bg-transparent border-0 w-100 text-start">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    <span class="sidebar-text">Logout</span>
                </button>
            </form>
        </li>
    </ul>
</nav>

<style>
/* Sidebar specific responsive styles */
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
    .sidebar-text {
        display: none;
    }
    
    .sidebar .nav-link {
        text-align: center;
        padding: 0.75rem 0.5rem;
    }
    
    .sidebar-header {
        text-align: center;
    }
    
    .sidebar-brand span {
        display: none;
    }
}
</style>