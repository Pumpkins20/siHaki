
<div class="d-flex flex-column h-100">
    <a href="{{ route('user.dashboard') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <i class="bi bi-shield-check fs-4 me-2"></i>
        <span class="fs-4">SiHaki User</span>
    </a>
    
    <hr>
    
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="{{ route('user.dashboard') }}" class="nav-link text-white {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i>
                Beranda
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('user.submissions.index') }}" class="nav-link text-white {{ request()->routeIs('user.submissions.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-plus me-2"></i>
                Pengajuan HKI
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('user.history') }}" class="nav-link text-white {{ request()->routeIs('user.history') ? 'active' : '' }}">
                <i class="bi bi-clock-history me-2"></i>
                Riwayat Pengajuan
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('user.panduan') }}" class="nav-link text-white {{ request()->routeIs('user.panduan') ? 'active' : '' }}">
                <i class="bi bi-book me-2"></i>
                Panduan
            </a>
        </li>
    </ul>
    
    <hr>
    
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle me-2"></i>
            <strong>{{ Auth::user()->nama }}</strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="{{ route('logout') }}"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
        </ul>
    </div>
</div>