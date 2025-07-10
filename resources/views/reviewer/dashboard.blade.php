<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviewer Dashboard - SiHaki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-info">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-eye"></i> SiHaki Reviewer
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('reviewer.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reviewer.reviews.index') }}">
                            <i class="bi bi-file-earmark-check"></i> Review Queue
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->nama }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('logout') }}"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row">
            <!-- Welcome Card -->
            <div class="col-12 mb-4">
                <div class="card bg-gradient-info text-white">
                    <div class="card-body">
                        <h4 class="card-title">Welcome, Dr. {{ Auth::user()->nama }}!</h4>
                        <p class="card-text">Review HKI submissions and provide valuable feedback to applicants.</p>
                        <a href="{{ route('reviewer.reviews.index') }}" class="btn btn-light">
                            <i class="bi bi-file-earmark-check"></i> Start Reviewing
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Assigned Reviews
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['assigned_reviews'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-file-earmark-text fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Pending Reviews
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_reviews'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-hourglass-split fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Completed Reviews
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['completed_reviews'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Avg Review Time
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['avg_review_time'] }} days</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-stopwatch fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Priority Queue -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Priority Review Queue</h6>
                        <a href="{{ route('reviewer.reviews.index') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        @if($pending_reviews->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Applicant</th>
                                            <th>Type</th>
                                            <th>Priority</th>
                                            <th>Submitted</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pending_reviews as $submission)
                                        <tr>
                                            <td>{{ Str::limit($submission->title, 30) }}</td>
                                            <td>{{ $submission->user->nama }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ ucfirst($submission->type) }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $days = $submission->submission_date->diffInDays(now());
                                                    $priority = $days > 7 ? 'High' : ($days > 3 ? 'Medium' : 'Normal');
                                                    $color = $days > 7 ? 'danger' : ($days > 3 ? 'warning' : 'success');
                                                @endphp
                                                <span class="badge bg-{{ $color }}">{{ $priority }}</span>
                                            </td>
                                            <td>{{ $submission->submission_date->format('d M Y') }}</td>
                                            <td>
                                                <a href="{{ route('reviewer.reviews.show', $submission) }}" class="btn btn-sm btn-outline-primary">Review</a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-inbox fa-3x text-gray-400"></i>
                                <p class="mt-2 text-gray-600">No pending reviews</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Guidelines -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                    </div>
                    <div class="card-body">
                        @if($recent_reviews->count() > 0)
                            @foreach($recent_reviews as $review)
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <i class="bi bi-check-circle text-success"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ Str::limit($review->title, 25) }}</h6>
                                    <small class="text-muted">{{ $review->reviewed_at->format('d M Y') }}</small>
                                </div>
                                <div>
                                    <span class="badge bg-{{ $review->status == 'approved' ? 'success' : 'danger' }}">
                                        {{ ucfirst($review->status) }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">No recent activity</p>
                        @endif
                    </div>
                </div>

                <!-- Review Guidelines -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Review Guidelines</h6>
                    </div>
                    <div class="card-body">
                        <div class="small">
                            <p><strong>Evaluation Criteria:</strong></p>
                            <ul class="mb-3">
                                <li>Originality and novelty</li>
                                <li>Technical feasibility</li>
                                <li>Commercial potential</li>
                                <li>Documentation completeness</li>
                            </ul>
                            <p><strong>Review Timeline:</strong></p>
                            <ul class="mb-3">
                                <li>Standard review: 7-14 days</li>
                                <li>Priority review: 3-7 days</li>
                                <li>Complex cases: 14-21 days</li>
                            </ul>
                            <a href="#" class="btn btn-sm btn-outline-info">Download Manual</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }
        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }
        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }
        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }
        .bg-gradient-info {
            background: linear-gradient(87deg, #11cdef 0, #1171ef 100%) !important;
        }
    </style>
</body>
</html>