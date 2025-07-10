@extends('layouts.user')

@section('title', 'My Submissions')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">My Submissions</h1>
                    <p class="text-muted">Kelola semua submission HKI Anda</p>
                </div>
                <div>
                    <a href="{{ route('user.submissions.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Buat Submission Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form method="GET" action="{{ route('user.submissions.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                    <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                                    <option value="revision_needed" {{ request('status') == 'revision_needed' ? 'selected' : '' }}>Revision Needed</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="type" class="form-label">Tipe</label>
                                <select name="type" id="type" class="form-select">
                                    <option value="">Semua Tipe</option>
                                    <option value="copyright" {{ request('type') == 'copyright' ? 'selected' : '' }}>Copyright</option>
                                    <option value="patent" {{ request('type') == 'patent' ? 'selected' : '' }}>Patent</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="search" class="form-label">Cari</label>
                                <input type="text" name="search" id="search" class="form-control" placeholder="Cari berdasarkan judul..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Cari
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Submissions Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Submissions</h6>
                </div>
                <div class="card-body">
                    @if($submissions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="30%">Judul</th>
                                        <th width="10%">Tipe</th>
                                        <th width="15%">Status</th>
                                        <th width="15%">Tanggal Submit</th>
                                        <th width="15%">Reviewer</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submissions as $index => $submission)
                                    <tr>
                                        <td>{{ $submissions->firstItem() + $index }}</td>
                                        <td>
                                            <strong>{{ $submission->title }}</strong>
                                            <br>
                                            <small class="text-muted">{{ Str::limit($submission->description, 50) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ ucfirst($submission->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'draft' => 'secondary',
                                                    'submitted' => 'primary',
                                                    'under_review' => 'warning',
                                                    'revision_needed' => 'info',
                                                    'approved' => 'success',
                                                    'rejected' => 'danger'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$submission->status] ?? 'secondary' }}">
                                                {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $submission->submission_date ? $submission->submission_date->format('d M Y') : '-' }}
                                        </td>
                                        <td>
                                            {{ $submission->reviewer ? $submission->reviewer->nama : '-' }}
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('user.submissions.show', $submission) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                
                                                @if(in_array($submission->status, ['draft', 'revision_needed']))
                                                    <a href="{{ route('user.submissions.edit', $submission) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                @endif
                                                
                                                @if($submission->status === 'draft')
                                                    <form action="{{ route('user.submissions.destroy', $submission) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus submission ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $submissions->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="mt-2 text-muted">Belum ada submission</p>
                            <a href="{{ route('user.submissions.create') }}" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Buat Submission Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection