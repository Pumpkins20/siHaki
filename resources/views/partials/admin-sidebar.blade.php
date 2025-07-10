<div class="d-flex flex-column h-100 p-3 text-white">
    <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <i class="bi bi-shield-check fs-4 me-2"></i>
        <span class="fs-4">SiHaki Admin</span>
    </a>
    
    <hr>
    
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('admin.users.index') }}" class="nav-link text-white {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people me-2"></i>
                Kelola User
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('admin.submissions.index') }}" class="nav-link text-white {{ request()->routeIs('admin.submissions.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text me-2"></i>
                Kelola Submission
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('admin.departments.index') }}" class="nav-link text-white {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                <i class="bi bi-building me-2"></i>
                Kelola Departemen
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('admin.reports.index') }}" class="nav-link text-white {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart me-2"></i>
                Laporan
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