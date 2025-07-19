<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HkiSubmission;
use App\Models\SubmissionDocument;
use App\Models\SubmissionHistory;
use App\Models\SubmissionMember;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\SubmissionStatusChanged;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::user()->submissions();

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $submissions = $query->latest()->paginate(10);

        return view('user.submissions.index', compact('submissions'));
    }

    public function create()
    {
        return view('user.submissions.create');
    }

    public function store(Request $request)
    {
        try {
            // ✅ REMOVE: Don't use $this->authorize() here
            // $this->authorize('create', HkiSubmission::class);

            // Validation rules
            $rules = [
                'title' => 'required|string|max:255',
                'creation_type' => 'required|in:program_komputer,sinematografi,buku,poster_fotografi,alat_peraga,basis_data',
                'description' => 'required|string|max:1000',
                'first_publication_date' => 'required|date|before_or_equal:today',
                'member_count' => 'required|integer|min:2|max:5',
                'members' => 'required|array',
                'members.*.name' => 'required|string|max:255',
                'members.*.whatsapp' => 'required|regex:/^[0-9]{10,13}$/',
                'members.*.email' => 'required|email',
                'members.*.ktp' => 'required|file|mimes:jpg,jpeg|max:2048',
            ];

            // Dynamic validation based on creation type
            $this->addDynamicValidation($rules, $request->creation_type);

            $customMessages = [
                'title.required' => 'Judul HKI harus diisi',
                'creation_type.required' => 'Jenis pengajuan harus dipilih',
                'description.required' => 'Deskripsi harus diisi',
                'first_publication_date.required' => 'Tanggal pertama kali diumumkan/digunakan/dipublikasikan harus diisi',
                'first_publication_date.date' => 'Format tanggal tidak valid',
                'first_publication_date.before_or_equal' => 'Tanggal tidak boleh lebih dari hari ini',
                'member_count.required' => 'Jumlah anggota harus diisi',
                'member_count.min' => 'Minimal 2 anggota pencipta',
                'member_count.max' => 'Maksimal 5 anggota pencipta',
                'members.*.name.required' => 'Nama anggota harus diisi',
                'members.*.whatsapp.required' => 'No. WhatsApp anggota harus diisi',
                'members.*.whatsapp.regex' => 'Format No. WhatsApp tidak valid (10-13 digit)',
                'members.*.email.required' => 'Email anggota harus diisi',
                'members.*.email.email' => 'Format email tidak valid',
                'members.*.ktp.required' => 'File KTP anggota harus diupload',
                'members.*.ktp.mimes' => 'File KTP harus dalam format JPG/JPEG',
                'members.*.ktp.max' => 'Ukuran file KTP maksimal 2MB',
            ];

            $request->validate($rules, $customMessages);

            // ✅ SIMPLE CHECK: Just check if user is authenticated and has role 'user'
            if (Auth::user()->role !== 'user') {
                return back()->withErrors(['error' => 'Hanya dosen yang dapat membuat submission.']);
            }

            DB::beginTransaction();

            // Create submission
            $submission = HkiSubmission::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'type' => 'copyright',
                'creation_type' => $request->creation_type,
                'description' => $request->description,
                'first_publication_date' => $request->first_publication_date,
                'member_count' => $request->member_count,
                'status' => 'submitted',
                'submission_date' => now(),
                'additional_data' => $this->getAdditionalData($request),
            ]);

            // Handle file uploads dan member data
            $this->handleFileUploads($request, $submission);
            $this->handleMemberData($request, $submission);

            // Create history record
            SubmissionHistory::create([
                'submission_id' => $submission->id,
                'user_id' => Auth::id(),
                'action' => 'Submitted',
                'previous_status' => null,
                'new_status' => 'submitted',
                'notes' => 'Submission berhasil dibuat dan diajukan'
            ]);

            // ✅ Send notification to user (confirmation)
            Auth::user()->notify(new SubmissionStatusChanged(
                $submission, 
                null, 
                'submitted', 
                'Pengajuan HKI Anda berhasil diterima dan menunggu review admin.'
            ));

            DB::commit();

            return redirect()->route('user.submissions.show', $submission)
                ->with('success', 'Submission berhasil dibuat dan diajukan! Silakan tunggu proses review dari admin.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Submission creation failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan submission. Silakan coba lagi.'])->withInput();
        }
    }

    /**
     * Display submission detail
     */
    public function show(HkiSubmission $submission)
    {
        // ✅ SIMPLE CHECK: Only check if user owns the submission
        if ($submission->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke submission ini.');
        }

        $submission->load([
            'documents',
            'members',
            'histories.user',
            'reviewer'
        ]);

        return view('user.submissions.show', compact('submission'));
    }

    /**
     * Show edit form
     */
    public function edit(HkiSubmission $submission)
    {
        // ✅ SIMPLE CHECK: Only check ownership and status
        if ($submission->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke submission ini.');
        }

        if (!in_array($submission->status, ['draft', 'revision_needed'])) {
            return back()->withErrors(['error' => 'Submission ini tidak dapat diedit karena statusnya sudah ' . $submission->status]);
        }

        $submission->load(['documents', 'members']);
        return view('user.submissions.edit', compact('submission'));
    }

    /**
     * Update submission
     */
    public function update(Request $request, HkiSubmission $submission)
    {
        // ✅ SIMPLE CHECK: Only check ownership and status
        if ($submission->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke submission ini.');
        }

        if (!in_array($submission->status, ['draft', 'revision_needed'])) {
            return back()->withErrors(['error' => 'Submission ini tidak dapat diedit karena statusnya sudah ' . $submission->status]);
        }

        // Rest of update logic...
    }

    /**
     * Delete submission
     */
    public function destroy(HkiSubmission $submission)
    {
        // ✅ SIMPLE CHECK: Only check ownership and status
        if ($submission->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke submission ini.');
        }

        if ($submission->status !== 'draft') {
            return back()->withErrors(['error' => 'Hanya submission dengan status draft yang dapat dihapus.']);
        }

        // Delete associated files and records
        $this->deleteSubmissionFiles($submission);
        $submission->delete();

        return redirect()->route('user.submissions.index')
            ->with('success', 'Submission berhasil dihapus.');
    }

    /**
     * Add dynamic validation rules based on creation type
     */
    private function addDynamicValidation(&$rules, $creationType)
    {
        switch ($creationType) {
            case 'program_komputer':
                $rules['program_link'] = 'required|url';
                $rules['manual_document'] = 'required|file|mimes:pdf|max:20480'; // 20MB
                break;
                
            case 'sinematografi':
                $rules['video_link'] = 'required|url';
                $rules['metadata_file'] = 'required|file|mimes:pdf|max:20480'; // 20MB
                break;
                
            case 'buku':
                $rules['isbn'] = 'nullable|string|max:20';
                $rules['page_count'] = 'required|integer|min:1';
                $rules['ebook_file'] = 'required|file|mimes:pdf|max:20480'; // 20MB
                break;
                
            case 'poster_fotografi':
                $rules['image_type'] = 'required|in:poster,fotografi,seni_gambar,karakter_animasi';
                $rules['width'] = 'required|integer|min:1';
                $rules['height'] = 'required|integer|min:1';
                $rules['image_files'] = 'required|array|min:1';
                $rules['image_files.*'] = 'file|mimes:jpg,jpeg,png|max:1024'; // 1MB per file
                break;
                
            case 'alat_peraga':
                $rules['subject'] = 'required|string|max:255';
                $rules['education_level'] = 'required|in:sd,smp,sma,kuliah';
                $rules['photo_files'] = 'required|array|min:1';
                $rules['photo_files.*'] = 'file|mimes:jpg,jpeg,png|max:1024'; // 1MB per file
                break;
                
            case 'basis_data':
                $rules['database_type'] = 'required|string|max:100';
                $rules['record_count'] = 'required|integer|min:1';
                $rules['documentation_file'] = 'required|file|mimes:pdf|max:20480'; // 20MB
                break;
        }
    }

    /**
     * Get additional data based on creation type
     */
    private function getAdditionalData(Request $request)
    {
        $data = [];
        
        switch ($request->creation_type) {
            case 'program_komputer':
                $data['program_link'] = $request->program_link;
                break;
                
            case 'sinematografi':
                $data['video_link'] = $request->video_link;
                break;
                
            case 'buku':
                $data['isbn'] = $request->isbn;
                $data['page_count'] = $request->page_count;
                break;
                
            case 'poster_fotografi':
                $data['image_type'] = $request->image_type;
                $data['width'] = $request->width;
                $data['height'] = $request->height;
                break;
                
            case 'alat_peraga':
                $data['subject'] = $request->subject;
                $data['education_level'] = $request->education_level;
                break;
                
            case 'basis_data':
                $data['database_type'] = $request->database_type;
                $data['record_count'] = $request->record_count;
                break;
        }
        
        return $data;
    }

    /**
     * Handle file uploads based on creation type
     */
    private function handleFileUploads(Request $request, HkiSubmission $submission)
    {
        switch ($request->creation_type) {
            case 'program_komputer':
                if ($request->hasFile('manual_document')) {
                    $this->uploadDocument($request, $submission, 'manual_document', 'main_document');
                }
                break;
                
            case 'sinematografi':
                if ($request->hasFile('metadata_file')) {
                    $this->uploadDocument($request, $submission, 'metadata_file', 'main_document');
                }
                break;
                
            case 'buku':
                if ($request->hasFile('ebook_file')) {
                    $this->uploadDocument($request, $submission, 'ebook_file', 'main_document');
                }
                break;
                
            case 'poster_fotografi':
                if ($request->hasFile('image_files')) {
                    foreach ($request->file('image_files') as $index => $file) {
                        $this->uploadDocument($request, $submission, 'image_files', 'supporting_document', $file, $index);
                    }
                }
                break;
                
            case 'alat_peraga':
                if ($request->hasFile('photo_files')) {
                    foreach ($request->file('photo_files') as $index => $file) {
                        $this->uploadDocument($request, $submission, 'photo_files', 'supporting_document', $file, $index);
                    }
                }
                break;
                
            case 'basis_data':
                if ($request->hasFile('documentation_file')) {
                    $this->uploadDocument($request, $submission, 'documentation_file', 'main_document');
                }
                break;
        }
    }

    /**
     * Upload a single document
     */
    private function uploadDocument(Request $request, HkiSubmission $submission, $fieldName, $documentType, $file = null, $index = null)
    {
        if ($file === null) {
            $file = $request->file($fieldName);
        }

        if (!$file || !$file->isValid()) {
            return;
        }

        // Generate unique filename
        $timestamp = time();
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $indexSuffix = $index !== null ? '_' . ($index + 1) : '';
        
        $fileName = $submission->id . '/' . $timestamp . '_' . $fieldName . $indexSuffix . '_' . $originalName . '.' . $extension;
        
        // Store file
        $filePath = $file->storeAs('submissions', $fileName, 'public');
        
        // Create document record
        SubmissionDocument::create([
            'submission_id' => $submission->id,
            'document_type' => $documentType,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_at' => now(),
        ]);

        Log::info('Document uploaded successfully', [
            'submission_id' => $submission->id,
            'field_name' => $fieldName,
            'file_name' => $fileName,
            'file_path' => $filePath
        ]);
    }

    /**
     * Handle member data and KTP uploads
     */
    private function handleMemberData(Request $request, HkiSubmission $submission)
    {
        if ($request->has('members')) {
            foreach ($request->members as $index => $memberData) {
                // Upload KTP file
                $ktpPath = null;
                if (isset($memberData['ktp']) && $memberData['ktp']->isValid()) {
                    $ktpFile = $memberData['ktp'];
                    $ktpFileName = $submission->id . '/ktp_' . ($index + 1) . '_' . time() . '.' . $ktpFile->getClientOriginalExtension();
                    $ktpPath = $ktpFile->storeAs('ktp_files', $ktpFileName, 'public');
                    
                    Log::info('KTP uploaded successfully', [
                        'submission_id' => $submission->id,
                        'member_index' => $index,
                        'ktp_path' => $ktpPath
                    ]);
                }
                
                // Create member record
                SubmissionMember::create([
                    'submission_id' => $submission->id,
                    'name' => $memberData['name'],
                    'email' => $memberData['email'],
                    'whatsapp' => $memberData['whatsapp'],
                    'position' => $index + 1,
                    'is_leader' => $index === 0, // First member is leader
                    'ktp' => $ktpPath,
                ]);
            }
        }
    }

    /**
     * Delete submission files when deleting submission
     */
    private function deleteSubmissionFiles(HkiSubmission $submission)
    {
        try {
            // Delete documents
            foreach ($submission->documents as $document) {
                $filePath = storage_path('app/public/' . $document->file_path);
                if (file_exists($filePath)) {
                    unlink($filePath);
                    Log::info('Document file deleted', ['file_path' => $filePath]);
                }
                $document->delete();
            }
            
            // Delete member KTPs
            foreach ($submission->members as $member) {
                if ($member->ktp) {
                    $ktpPath = storage_path('app/public/' . $member->ktp);
                    if (file_exists($ktpPath)) {
                        unlink($ktpPath);
                        Log::info('KTP file deleted', ['ktp_path' => $ktpPath]);
                    }
                }
                $member->delete();
            }
            
            // Delete histories
            $submission->histories()->delete();
            
            Log::info('All submission files deleted', ['submission_id' => $submission->id]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting submission files', [
                'submission_id' => $submission->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Add missing download document method
     */
    public function downloadDocument(SubmissionDocument $document)
    {
        // Check if user owns the submission
        if ($document->submission->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to document.');
        }

        $filePath = storage_path('app/public/' . $document->file_path);
        
        if (!file_exists($filePath)) {
            return back()->withErrors(['error' => 'File tidak ditemukan.']);
        }

        return response()->download($filePath, $document->file_name);
    }

    /**
     * Delete document (for edit mode)
     */
    public function deleteDocument(SubmissionDocument $document)
    {
        // Check if user owns the submission
        if ($document->submission->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to document.');
        }

        // Check if submission can be edited
        if (!in_array($document->submission->status, ['draft', 'revision_needed'])) {
            return back()->withErrors(['error' => 'Dokumen tidak dapat dihapus karena submission sudah ' . $document->submission->status]);
        }

        try {
            // Delete physical file
            $filePath = storage_path('app/public/' . $document->file_path);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete database record
            $document->delete();

            return back()->with('success', 'Dokumen berhasil dihapus.');
            
        } catch (\Exception $e) {
            Log::error('Error deleting document', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus dokumen.']);
        }
    }
}