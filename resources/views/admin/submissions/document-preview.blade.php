
@extends('layouts.admin')

@section('title', 'Preview Document')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.submissions.index') }}" class="text-decoration-none">Submissions</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.submissions.show', $submission) }}" class="text-decoration-none">{{ Str::limit($submission->title, 30) }}</a></li>
                    <li class="breadcrumb-item active">Preview Document</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">Document Preview</h1>
                    <p class="text-muted mb-0">{{ $document->file_name }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.submissions.document-download', [$submission, $document]) }}" class="btn btn-success me-2">
                        <i class="bi bi-download"></i> Download
                    </a>
                    <a href="{{ route('admin.submissions.show', $submission) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-file-earmark-text me-2"></i>{{ $document->file_name }}
                    </h6>
                </div>
                <div class="card-body">
                    <!-- File Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td><strong>File Name:</strong></td>
                                    <td>{{ $fileInfo['name'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>File Size:</strong></td>
                                    <td>{{ number_format($fileInfo['size'] / 1024, 2) }} KB</td>
                                </tr>
                                <tr>
                                    <td><strong>File Type:</strong></td>
                                    <td>
                                        <span class="badge bg-info">{{ strtoupper($fileInfo['extension']) }}</span>
                                        <small class="text-muted ms-2">{{ $fileInfo['mime_type'] }}</small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td><strong>Document Type:</strong></td>
                                    <td>
                                        @if($document->document_type === 'main_document')
                                            <span class="badge bg-primary">Main Document</span>
                                        @else
                                            <span class="badge bg-secondary">Supporting Document</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Upload Date:</strong></td>
                                    <td>{{ $fileInfo['upload_date']->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Submission:</strong></td>
                                    <td>
                                        <a href="{{ route('admin.submissions.show', $submission) }}" class="text-decoration-none">
                                            {{ Str::limit($submission->title, 40) }}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Preview Area -->
                    <div class="text-center py-5">
                        @if(in_array($fileInfo['extension'], ['jpg', 'jpeg', 'png', 'gif']))
                            <!-- Image Preview -->
                            <div class="mb-3">
                                <img src="{{ route('admin.submissions.document-preview', [$submission, $document]) }}" 
                                     alt="{{ $document->file_name }}" 
                                     class="img-fluid border rounded shadow"
                                     style="max-height: 600px;">
                            </div>
                        @elseif($fileInfo['extension'] === 'pdf')
                            <!-- PDF Preview -->
                            <div class="mb-3">
                                <iframe src="{{ route('admin.submissions.document-preview', [$submission, $document]) }}" 
                                        width="100%" 
                                        height="600px" 
                                        class="border rounded">
                                </iframe>
                            </div>
                        @else
                            <!-- Non-previewable files -->
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle fs-1 mb-3"></i>
                                <h5>Preview Not Available</h5>
                                <p class="mb-3">This file type ({{ strtoupper($fileInfo['extension']) }}) cannot be previewed in the browser.</p>
                                <a href="{{ route('admin.submissions.document-download', [$submission, $document]) }}" class="btn btn-primary">
                                    <i class="bi bi-download"></i> Download to View
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('admin.submissions.document-download', [$submission, $document]) }}" class="btn btn-success">
                            <i class="bi bi-download"></i> Download File
                        </a>
                        <a href="{{ route('admin.submissions.show', $submission) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Submission
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.img-fluid {
    transition: transform 0.3s ease;
}

.img-fluid:hover {
    transform: scale(1.05);
}

iframe {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}

.alert {
    border: none;
    border-radius: 0.5rem;
}
</style>
@endpush