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
        $query = HkiSubmission::with(['reviewer'])
            ->where('user_id', Auth::id())
            ->latest();

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // ✅ NEW: Filter berdasarkan creation_type (replace type filter)
        if ($request->filled('creation_type')) {
            $query->where('creation_type', $request->creation_type);
        }

        // Search berdasarkan judul
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $submissions = $query->paginate(10);

        return view('user.submissions.index', compact('submissions'));
    }

    public function create()
    {
        return view('user.submissions.create');
    }

    public function store(Request $request)
    {
        try {
            // Validation rules
            $rules = [
                'title' => 'required|string|max:255',
                'creation_type' => 'required|in:program_komputer,sinematografi,buku,poster,fotografi,seni_gambar,karakter_animasi,alat_peraga,basis_data',
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
                'creation_type.in' => 'Jenis pengajuan tidak valid',
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

            // Check user role
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

            // Send notification
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
            Log::error('Submission creation failed: ' . $e->getMessage(), [
                'request_data' => $request->except(['_token', 'members']),
                'creation_type' => $request->creation_type,
                'stack_trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan submission: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * ✅ FIX: Complete getAdditionalData method
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
                
            case 'poster':
            case 'fotografi':
            case 'seni_gambar':
            case 'karakter_animasi':
                // ✅ UNIFIED: Data untuk semua jenis visual
                $data['width'] = $request->width;
                $data['height'] = $request->height;
                $data['image_description'] = $request->image_description;
                $data['visual_type'] = $request->creation_type; // Store specific type
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

        // ✅ Add debug logging
        Log::info('Submission show accessed', [
            'submission_id' => $submission->id,
            'user_id' => Auth::id(),
            'documents_count' => $submission->documents->count(),
            'documents' => $submission->documents->map(function($doc) {
                return [
                    'id' => $doc->id,
                    'type' => $doc->document_type,
                    'name' => $doc->file_name,
                    'uploaded_at' => $doc->uploaded_at ? $doc->uploaded_at->format('Y-m-d H:i:s') : null,
                    'file_exists' => file_exists(storage_path('app/public/' . $doc->file_path))
                ];
            })
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

        // ✅ Load relationships and log for debugging
        $submission->load(['documents', 'members']);

        Log::info('Edit form accessed', [
            'submission_id' => $submission->id,
            'user_id' => Auth::id(),
            'status' => $submission->status,
            'creation_type' => $submission->creation_type,
            'documents_count' => $submission->documents->count(),
            'main_documents_count' => $submission->documents->where('document_type', 'main_document')->count(),
            'supporting_documents_count' => $submission->documents->where('document_type', 'supporting_document')->count()
        ]);
        
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

        // ✅ Base validation rules
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'first_publication_date' => 'required|date|before_or_equal:today',
        ];

        // ✅ Dynamic validation based on existing creation_type (tidak bisa diubah)
        $this->addDynamicValidationForUpdate($rules, $submission->creation_type);

        $customMessages = [
            'title.required' => 'Judul HKI harus diisi',
            'description.required' => 'Deskripsi harus diisi',
            'description.max' => 'Deskripsi maksimal 1000 karakter',
            'first_publication_date.required' => 'Tanggal pertama kali diumumkan/digunakan/dipublikasikan harus diisi',
            'first_publication_date.date' => 'Format tanggal tidak valid',
            'first_publication_date.before_or_equal' => 'Tanggal tidak boleh lebih dari hari ini',
        ];

        $request->validate($rules, $customMessages);

        try {
            DB::beginTransaction();

            // Store old status for history
            $oldStatus = $submission->status;

            // Update basic submission data
            $submission->update([
                'title' => $request->title,
                'description' => $request->description,
                'first_publication_date' => $request->first_publication_date,
                'additional_data' => $this->getAdditionalDataForUpdate($request, $submission->creation_type),
                'status' => $request->has('save_as_draft') ? 'draft' : 'submitted',
                'submission_date' => $request->has('save_as_draft') ? null : now(),
            ]);

            // ✅ FIX: Handle file uploads using submission's creation_type
            $this->handleFileUploadsForUpdate($request, $submission);

            // Create history record
            SubmissionHistory::create([
                'submission_id' => $submission->id,
                'user_id' => Auth::id(),
                'action' => $request->has('save_as_draft') ? 'Updated as Draft' : 'Updated and Resubmitted',
                'previous_status' => $oldStatus,
                'new_status' => $request->has('save_as_draft') ? 'draft' : 'submitted',
                'notes' => $request->has('save_as_draft') ? 'Submission updated and saved as draft' : 'Submission updated and resubmitted for review'
            ]);

            // Send notification jika status berubah ke submitted
            if (!$request->has('save_as_draft')) {
                Auth::user()->notify(new SubmissionStatusChanged(
                    $submission,
                    $oldStatus,
                    'submitted',
                    $oldStatus === 'revision_needed' ? 
                        'Revisi Anda telah diterima dan submission akan direview kembali.' :
                        'Update submission Anda berhasil diterima dan menunggu review.'
                ));
            }

            DB::commit();

            $message = $request->has('save_as_draft') ? 
                'Submission berhasil diupdate dan disimpan sebagai draft.' :
                'Submission berhasil diupdate dan dikirim untuk review!';

            return redirect()->route('user.submissions.show', $submission)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Submission update failed: ' . $e->getMessage(), [
                'submission_id' => $submission->id,
                'user_id' => Auth::id(),
                'error' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Terjadi kesalahan saat mengupdate submission. Silakan coba lagi.'])->withInput();
        }
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
                
            case 'poster':
            case 'fotografi':
            case 'seni_gambar':
            case 'karakter_animasi':
                // ✅ UNIFIED: Semua jenis visual menggunakan rules yang sama
                $rules['image_files'] = 'required|array|min:1';
                $rules['image_files.*'] = 'file|mimes:jpg,jpeg,png|max:2048'; // 2MB per file
                $rules['width'] = 'nullable|integer|min:1';
                $rules['height'] = 'nullable|integer|min:1';
                $rules['image_description'] = 'nullable|string|max:500';
                break;
                
            case 'alat_peraga':
                $rules['subject'] = 'required|string|max:255';
                $rules['education_level'] = 'required|in:sd,smp,sma,kuliah';
                $rules['photo_files'] = 'required|array|min:1';
                $rules['photo_files.*'] = 'file|mimes:jpg,jpeg,png|max:2048'; // 2MB per file
                break;
                
            case 'basis_data':
                $rules['database_type'] = 'required|string|max:100';
                $rules['record_count'] = 'required|integer|min:1';
                $rules['documentation_file'] = 'required|file|mimes:pdf|max:20480'; // 20MB
                break;
        }
    }

    /**
     * Handle file uploads based on creation type
     */
    private function handleFileUploads(Request $request, HkiSubmission $submission)
    {
        try {
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
                    
                case 'poster':
                case 'fotografi':
                case 'seni_gambar':
                case 'karakter_animasi':
                    // ✅ UNIFIED: Handling untuk semua jenis visual
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
        } catch (\Exception $e) {
            Log::error('File upload failed', [
                'submission_id' => $submission->id,
                'creation_type' => $request->creation_type,
                'error' => $e->getMessage()
            ]);
            throw $e;
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
     * Download document method
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

    /**
     * ✅ NEW: Add dynamic validation rules for update (without required on existing docs)
     */
    private function addDynamicValidationForUpdate(&$rules, $creationType)
    {
        switch ($creationType) {
            case 'program_komputer':
                $rules['program_link'] = 'required|url';
                $rules['manual_document'] = 'nullable|file|mimes:pdf|max:20480'; // Optional untuk update
                break;
                
            case 'sinematografi':
                $rules['video_link'] = 'required|url';
                $rules['metadata_file'] = 'nullable|file|mimes:pdf|max:20480'; // Optional untuk update
                break;
                
            case 'buku':
                $rules['isbn'] = 'nullable|string|max:20';
                $rules['page_count'] = 'required|integer|min:1';
                $rules['ebook_file'] = 'nullable|file|mimes:pdf|max:20480'; // Optional untuk update
                break;
                
            case 'poster':
            case 'fotografi':
            case 'seni_gambar':
            case 'karakter_animasi':
                $rules['image_files'] = 'nullable|array';
                $rules['image_files.*'] = 'file|mimes:jpg,jpeg,png|max:2048'; // Optional untuk update
                break;
                
            case 'alat_peraga':
                $rules['subject'] = 'required|string|max:255';
                $rules['education_level'] = 'required|in:sd,smp,sma,kuliah';
                $rules['photo_files'] = 'nullable|array';
                $rules['photo_files.*'] = 'file|mimes:jpg,jpeg,png|max:2048'; // Optional untuk update
                break;
                
            case 'basis_data':
                $rules['database_type'] = 'required|string|max:100';
                $rules['record_count'] = 'required|integer|min:1';
                $rules['documentation_file'] = 'nullable|file|mimes:pdf|max:20480'; // Optional untuk update
                break;
        }
    }

    /**
     * ✅ NEW: Get additional data for update
     */
    private function getAdditionalDataForUpdate(Request $request, $creationType)
    {
        $data = [];
        
        switch ($creationType) {
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
     * ✅ NEW: Handle file uploads for update (using submission's creation_type)
     */
    private function handleFileUploadsForUpdate(Request $request, HkiSubmission $submission)
    {
        switch ($submission->creation_type) { // ✅ Use submission's creation_type, not request
            case 'program_komputer':
                if ($request->hasFile('manual_document')) {
                    // Delete old main document first
                    $this->deleteExistingDocuments($submission, 'main_document');
                    $this->uploadDocumentForUpdate($request, $submission, 'manual_document', 'main_document');
                }
                break;
                
            case 'sinematografi':
                if ($request->hasFile('metadata_file')) {
                    $this->deleteExistingDocuments($submission, 'main_document');
                    $this->uploadDocumentForUpdate($request, $submission, 'metadata_file', 'main_document');
                }
                break;
                
            case 'buku':
                if ($request->hasFile('ebook_file')) {
                    $this->deleteExistingDocuments($submission, 'main_document');
                    $this->uploadDocumentForUpdate($request, $submission, 'ebook_file', 'main_document');
                }
                break;
                
            case 'poster':
            case 'fotografi':
            case 'seni_gambar':
            case 'karakter_animasi':
                if ($request->hasFile('image_files')) {
                    $this->deleteExistingDocuments($submission, 'supporting_document');
                    foreach ($request->file('image_files') as $index => $file) {
                        $this->uploadDocumentForUpdate($request, $submission, 'image_files', 'supporting_document', $file, $index);
                    }
                }
                break;
                
            case 'alat_peraga':
                if ($request->hasFile('photo_files')) {
                    $this->deleteExistingDocuments($submission, 'supporting_document');
                    foreach ($request->file('photo_files') as $index => $file) {
                        $this->uploadDocumentForUpdate($request, $submission, 'photo_files', 'supporting_document', $file, $index);
                    }
                }
                break;
                
            case 'basis_data':
                if ($request->hasFile('documentation_file')) {
                    $this->deleteExistingDocuments($submission, 'main_document');
                    $this->uploadDocumentForUpdate($request, $submission, 'documentation_file', 'main_document');
                }
                break;
        }
    }

    /**
     * ✅ NEW: Upload document for update
     */
    private function uploadDocumentForUpdate(Request $request, HkiSubmission $submission, $fieldName, $documentType, $file = null, $index = null)
    {
        if ($file === null) {
            $file = $request->file($fieldName);
        }

        if (!$file || !$file->isValid()) {
            Log::warning('Invalid file upload attempted', [
                'submission_id' => $submission->id,
                'field_name' => $fieldName,
                'file_valid' => $file ? $file->isValid() : false
            ]);
            return;
        }

        // Generate unique filename
        $timestamp = time();
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $indexSuffix = $index !== null ? '_' . ($index + 1) : '';
        
        $fileName = 'updated_' . $submission->id . '/' . $timestamp . '_' . $fieldName . $indexSuffix . '_' . $originalName . '.' . $extension;
        
        try {
            // Store file
            $filePath = $file->storeAs('submissions', $fileName, 'public');
            
            // Create document record
            $document = SubmissionDocument::create([
                'submission_id' => $submission->id,
                'document_type' => $documentType,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_at' => now(),
            ]);

            Log::info('Document uploaded successfully during update', [
                'submission_id' => $submission->id,
                'document_id' => $document->id,
                'field_name' => $fieldName,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_size' => $file->getSize()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to upload document during update', [
                'submission_id' => $submission->id,
                'field_name' => $fieldName,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * ✅ NEW: Delete existing documents of specific type
     */
    private function deleteExistingDocuments(HkiSubmission $submission, $documentType)
    {
        try {
            $documents = $submission->documents()->where('document_type', $documentType)->get();
            
            foreach ($documents as $document) {
                // Delete physical file
                $filePath = storage_path('app/public/' . $document->file_path);
                if (file_exists($filePath)) {
                    unlink($filePath);
                    Log::info('Old document file deleted', ['file_path' => $filePath]);
                }
                
                // Delete database record
                $document->delete();
            }
            
            Log::info('Existing documents deleted', [
                'submission_id' => $submission->id,
                'document_type' => $documentType,
                'count' => $documents->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting existing documents', [
                'submission_id' => $submission->id,
                'document_type' => $documentType,
                'error' => $e->getMessage()
            ]);
            // Don't throw here, let the upload continue
        }
    }
}