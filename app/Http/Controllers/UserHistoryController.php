<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HkiSubmission;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class UserHistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = HkiSubmission::with(['reviewer', 'members', 'documents'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->latest('reviewed_at');

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $allowedStatuses = ['approved', 'rejected'];
            if (in_array($request->status, $allowedStatuses)) {
                $query->where('status', $request->status);
            }
        }

        // Filter berdasarkan tahun
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        // Filter berdasarkan creation_type
        if ($request->filled('creation_type')) {
            $query->where('creation_type', $request->creation_type);
        }

        // Search berdasarkan judul
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $submissions = $query->paginate(10);

        // Data untuk filter dropdown
        $years = HkiSubmission::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $creationTypes = HkiSubmission::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->distinct()
            ->pluck('creation_type');

        // Statistics untuk cards
        $stats = [
            'approved' => HkiSubmission::where('user_id', $user->id)->where('status', 'approved')->count(),
            'rejected' => HkiSubmission::where('user_id', $user->id)->where('status', 'rejected')->count(),
            'pending' => HkiSubmission::where('user_id', $user->id)->whereIn('status', ['submitted', 'under_review'])->count(),
            'draft' => HkiSubmission::where('user_id', $user->id)->where('status', 'draft')->count(),
            'revision' => HkiSubmission::where('user_id', $user->id)->where('status', 'revision_needed')->count(),
        ];

        return view('user.history.index', compact(
            'submissions', 
            'years', 
            'creationTypes', 
            'stats'
        ));
    }

    public function show(HkiSubmission $submission)
    {
        // Authorize user can only view their own submissions
        if ($submission->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to submission.');
        }

        $submission->load(['documents', 'histories.user', 'reviewer', 'members']);
        
        return view('user.submissions.show', compact('submission'));
    }

    public function downloadCertificate(HkiSubmission $submission)
    {
        // Only allow download for approved submissions
        if ($submission->status !== 'approved') {
            return back()->withErrors(['error' => 'Sertifikat hanya tersedia untuk submission yang sudah disetujui.']);
        }

        // Authorize user can only download their own certificates
        if ($submission->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to certificate.');
        }

        // Check if certificate exists
        $certificateDoc = $submission->documents()->where('document_type', 'certificate')->first();
        
        if (!$certificateDoc) {
            return back()->withErrors(['error' => 'Sertifikat belum tersedia. Silakan hubungi admin.']);
        }

        $filePath = storage_path('app/public/' . $certificateDoc->file_path);
        
        if (!file_exists($filePath)) {
            return back()->withErrors(['error' => 'File sertifikat tidak ditemukan.']);
        }

        return response()->download($filePath, 'Sertifikat_' . $submission->title . '.pdf');
    }

    public function downloadDocument(HkiSubmission $submission, $documentId)
    {
        // Authorize user can only download their own documents
        if ($submission->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to document.');
        }

        $document = $submission->documents()->findOrFail($documentId);
        
        $filePath = storage_path('app/public/' . $document->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->download($filePath, $document->file_name);
    }

    public function exportHistory(Request $request)
    {
        $user = Auth::user();
        
        // Get submissions based on current filters
        $query = HkiSubmission::where('user_id', $user->id)
            ->with(['documents', 'histories.user', 'reviewer', 'members']);

        // Apply same filters as index
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('creation_type')) {
            $query->where('creation_type', $request->creation_type);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $submissions = $query->orderBy('created_at', 'desc')->get();

        // Generate CSV
        $filename = 'riwayat_pengajuan_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($submissions) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fputs($file, "\xEF\xBB\xBF");
            
            // CSV Headers
            fputcsv($file, [
                'No',
                'Judul',
                'Jenis Ciptaan',
                'Status',
                'Tanggal Submit',
                'Reviewer',
                'Tanggal Review',
                'Anggota Tim'
            ]);

            foreach ($submissions as $index => $submission) {
                $members = $submission->members->pluck('name')->implode(', ');
                
                fputcsv($file, [
                    $index + 1,
                    $submission->title,
                    $submission->creation_type_name ?? $submission->type,
                    $submission->status_name,
                    $submission->submission_date ? $submission->submission_date->format('d/m/Y H:i') : '-',
                    $submission->reviewer ? $submission->reviewer->nama : '-',
                    $submission->reviewed_at ? $submission->reviewed_at->format('d/m/Y H:i') : '-',
                    $members
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
