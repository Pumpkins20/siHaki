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
        // Update validation rules
        $rules = [
            'title' => 'required|string|max:255',
            'creation_type' => 'required|in:program_komputer,sinematografi,buku,poster_fotografi,alat_peraga,basis_data',
            'description' => 'required|string|max:1000',
            'member_count' => 'required|integer|min:2|max:5',
            'members' => 'required|array',
            'members.*.name' => 'required|string|max:255',
            'members.*.whatsapp' => 'required|regex:/^[0-9]{10,13}$/',
            'members.*.email' => 'required|email',
            'members.*.ktp' => 'required|file|mimes:jpg,jpeg|max:2048', // ✅ CHANGED: File upload validation
        ];

        // Update error messages
        $request->validate($rules, [
            'members.*.name.required' => 'Nama pencipta harus diisi',
            'members.*.whatsapp.required' => 'No WhatsApp pencipta harus diisi',
            'members.*.whatsapp.regex' => 'No WhatsApp harus berupa 10-13 digit angka',
            'members.*.email.required' => 'Email pencipta harus diisi',
            'members.*.email.email' => 'Format email tidak valid',
            'members.*.ktp.required' => 'Scan foto KTP harus diupload',
            'members.*.ktp.mimes' => 'Foto KTP harus berformat JPG atau JPEG',
            'members.*.ktp.max' => 'Ukuran foto KTP maksimal 2MB',
            
            'manual_document.required' => 'Manual penggunaan program harus diupload',
            'manual_document.mimes' => 'Manual penggunaan harus berformat PDF',
            'manual_document.max' => 'Ukuran manual penggunaan maksimal 20MB',
            
            'video_file.required' => 'File video harus diupload',
            'video_file.mimes' => 'Video harus berformat MP4',
            'video_file.max' => 'Ukuran video maksimal 20MB',
            
            'ebook_file.required' => 'File e-book harus diupload',
            'ebook_file.mimes' => 'E-book harus berformat PDF',
            'ebook_file.max' => 'Ukuran e-book maksimal 20MB',
            
            'image_file.required' => 'File gambar harus diupload',
            'image_file.mimes' => 'Gambar harus berformat JPG, JPEG, atau PNG',
            'image_file.max' => 'Ukuran gambar maksimal 1MB',
            
            'tool_photo.required' => 'Foto alat peraga harus diupload',
            'tool_photo.mimes' => 'Foto harus berformat JPG, JPEG, atau PNG',
            'tool_photo.max' => 'Ukuran foto maksimal 1MB',
            
            'metadata_file.required' => 'File metadata harus diupload',
            'metadata_file.mimes' => 'Metadata harus berformat PDF',
            'metadata_file.max' => 'Ukuran metadata maksimal 20MB',
        ]);   

        // Validate member count matches actual members
        $memberCount = $request->input('member_count');
        $actualMembers = count($request->input('members', []));
        
        if ($memberCount != $actualMembers) {
            return back()->withErrors(['member_count' => 'Jumlah anggota tidak sesuai dengan yang dipilih'])
                        ->withInput();
        }

        // Dynamic validation based on creation type
        switch($request->creation_type) {
            case 'program_komputer':
                $rules = array_merge($rules, [
                    'manual_document' => 'required|file|mimes:pdf|max:20480',
                    'program_link' => 'required|url',
                ]);
                break;

            case 'sinematografi':
                $rules = array_merge($rules, [
                    'video_link' => 'required|url',
                ]);
                break;

            case 'buku':
                $rules = array_merge($rules, [
                    'ebook_file' => 'required|file|mimes:pdf|max:20480',
                ]);
                break;

            case 'poster_fotografi':
                $rules = array_merge($rules, [
                    'image_file' => 'required|file|mimes:jpg,jpeg,png|max:1024',
                    'image_type' => 'required|in:poster,fotografi,seni_gambar,karakter_animasi',
                ]);
                break;

            case 'alat_peraga':
                $rules = array_merge($rules, [
                    'tool_photo' => 'required|file|mimes:jpg,jpeg,png|max:1024',
                    'additional_photos.*' => 'nullable|file|mimes:jpg,jpeg,png|max:1024',
                ]);
                break;

            case 'basis_data':
                $rules = array_merge($rules, [
                    'metadata_file' => 'required|file|mimes:pdf|max:20480',
                ]);
                break;
        }

        $request->validate($rules, [
            'members.*.name.required' => 'Nama pencipta harus diisi',
            'members.*.whatsapp.required' => 'No WhatsApp pencipta harus diisi',
            'members.*.whatsapp.regex' => 'No WhatsApp harus berupa 10-13 digit angka',
            'members.*.email.required' => 'Email pencipta harus diisi',
            'members.*.email.email' => 'Format email tidak valid',
            'members.*.ktp.required' => 'No KTP pencipta harus diisi',
            'members.*.ktp.size' => 'No KTP harus 16 digit',
            'members.*.ktp.regex' => 'No KTP harus berupa 16 digit angka',
        ]);

        DB::beginTransaction();

        try {
            // Create submission record (always submitted, no more draft)
            $submission = HkiSubmission::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'type' => 'copyright', // Default type
                'creation_type' => $request->creation_type,
                'description' => $request->description,
                'member_count' => $request->member_count,
                'status' => 'submitted', // ✅ Always submitted now
                'submission_date' => now(),
            ]);

            // Save members with KTP files
            foreach ($request->input('members') as $index => $memberData) {
                // Handle KTP file upload
                $ktpPath = null;
                if ($request->hasFile("members.{$index}.ktp")) {
                    $ktpFile = $request->file("members.{$index}.ktp");
                    $ktpFileName = 'ktp_' . $submission->id . '_member_' . ($index + 1) . '_' . time() . '.' . $ktpFile->getClientOriginalExtension();
                    $ktpPath = $ktpFile->storeAs('ktp_scans', $ktpFileName, 'public');
                }

                SubmissionMember::create([
                    'submission_id' => $submission->id,
                    'name' => $memberData['name'],
                    'whatsapp' => $memberData['whatsapp'],
                    'email' => $memberData['email'],
                    'ktp' => $ktpPath, // ✅ CHANGED: Store file path instead of number
                    'position' => $index + 1,
                    'is_leader' => $index === 0,
                ]);
            }

            // Handle file uploads based on creation type
            $this->handleFileUploads($request, $submission);

            // Create history record
            SubmissionHistory::create([
                'submission_id' => $submission->id,
                'user_id' => Auth::id(),
                'action' => 'Submitted',
                'previous_status' => null,
                'new_status' => $submission->status,
                'notes' => 'Submission submitted for review',
            ]);

            DB::commit();

            return redirect()->route('user.submissions.show', $submission)
                ->with('success', 'Submission berhasil disubmit untuk review');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Submission creation failed: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan submission. Silakan coba lagi.']);
        }
    }

<<<<<<< Updated upstream
    private function getAdditionalData($request)
=======
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
>>>>>>> Stashed changes
    {
        $data = [];
        
        switch($request->creation_type) {
            case 'program_komputer':
                $data['program_link'] = $request->program_link;
                break;

            case 'sinematografi':
                $data['video_description'] = $request->video_description;
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
                $data['materials'] = $request->materials;
                $data['usage_instructions'] = $request->usage_instructions;
                break;

            case 'basis_data':
                $data['database_type'] = $request->database_type;
                $data['record_count'] = $request->record_count;
                $data['database_purpose'] = $request->database_purpose;
                break;
        }

        return $data;
    }

    private function handleFileUploads($request, $submission)
    {
        $fileFields = [
            'program_komputer' => [
                'main' => ['manual_document'], // Main required files
                'supporting' => [] // Optional supporting files
            ],
            'sinematografi' => [
                'main' => ['video_file'],
                'supporting' => []
            ],
            'buku' => [
                'main' => ['ebook_file'],
                'supporting' => []
            ],
            'poster_fotografi' => [
                'main' => ['image_file'],
                'supporting' => []
            ],
            'alat_peraga' => [
                'main' => ['tool_photo'],
                'supporting' => ['additional_photos']
            ],
            'basis_data' => [
                'main' => ['metadata_file'],
                'supporting' => []
            ],
        ];

        $creationType = $request->creation_type;
        $fields = $fileFields[$creationType] ?? ['main' => [], 'supporting' => []];

        // Handle main documents
        foreach ($fields['main'] as $field) {
            if ($request->hasFile($field)) {
                $this->uploadDocument($request->file($field), $submission, $field);
            }
        }

        // Handle supporting documents
        foreach ($fields['supporting'] as $field) {
            if ($request->hasFile($field)) {
                if ($field === 'additional_photos') {
                    // Handle multiple files
                    foreach ($request->file($field) as $index => $file) {
                        $this->uploadDocument($file, $submission, $field, $index);
                    }
                } else {
                    $this->uploadDocument($request->file($field), $submission, $field);
                }
            }
        }
    }

    private function uploadDocument($file, $submission, $type, $index = null)
    {
        $prefix = $index !== null ? $type . '_' . $index : $type;
        $fileName = time() . '_' . $prefix . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('submissions/' . $submission->id, $fileName, 'public');

        $documentType = $this->mapToDocumentType($type);

        SubmissionDocument::create([
            'submission_id' => $submission->id,
            'document_type' => $documentType,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'uploaded_at' => now(),
        ]);
    }

    private function mapToDocumentType($type)
    {
        // List of main document types (required files)
        $mainDocumentTypes = [
            'manual_document',
            'video_file', 
            'ebook_file',
            'image_file',
            'tool_photo',
            'metadata_file'
        ];

        // If it's a main required document, return 'main_document'
        if (in_array($type, $mainDocumentTypes)) {
            return 'main_document';
        }

        // Everything else is supporting document
        return 'supporting_document';
    }

    public function show(HkiSubmission $submission)
    {
        $this->authorize('view', $submission);
        
        // $submission->load(['documents', 'histories.user', 'reviewer']);
        
        return view('user.submissions.show', compact('submission'));
    }


    public function edit(HkiSubmission $submission)
    {
        $this->authorize('update', $submission);
        
        // Only allow editing if status is draft or revision_needed
        if (!in_array($submission->status, ['draft', 'revision_needed'])) {
            return redirect()->route('user.submissions.show', $submission)
                ->withErrors(['error' => 'Submission tidak dapat diedit pada status saat ini.']);
        }
        
        $submission->load(['documents']);
        
        return view('user.submissions.edit', compact('submission'));
    }

    public function update(Request $request, HkiSubmission $submission)
    {
        $this->authorize('update', $submission);
        
        // Only allow updating if status is draft or revision_needed
        if (!in_array($submission->status, ['draft', 'revision_needed'])) {
            return redirect()->route('user.submissions.show', $submission)
                ->withErrors(['error' => 'Submission tidak dapat diupdate pada status saat ini.']);
        }
        
        $hasMainDocument = $submission->documents()->where('document_type', 'main_document')->exists();
        
        $oldStatus = $submission->status;

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:copyright,patent',
            'description' => 'required|string|min:50|max:1000',
            'main_document' => $hasMainDocument ? 'nullable|file|mimes:pdf,doc,docx|max:10240' : 'required|file|mimes:pdf,doc,docx|max:10240',
            'supporting_documents.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $oldStatus = $submission->status;
        $newStatus = $request->has('save_as_draft') ? 'draft' : 'submitted';
        
        // If it was revision_needed and not saving as draft, change to submitted
        if ($oldStatus === 'revision_needed' && !$request->has('save_as_draft')) {
            $newStatus = 'submitted';
        }

        // Update submission basic info
        $submission->update([
            'title' => $request->title,
            'type' => $request->type,
            'description' => $request->description,
            'status' => $newStatus,
            'submission_date' => $newStatus === 'submitted' ? now() : $submission->submission_date,
            'reviewer_id' => $newStatus === 'submitted' ? null : $submission->reviewer_id, // Reset reviewer for resubmission
            'review_notes' => $newStatus === 'submitted' ? null : $submission->review_notes, // Clear old review notes
            'reviewed_at' => null, // Reset review date
        ]);

        // Handle new main document if uploaded
        if ($request->hasFile('main_document')) {
            // Delete old main document
            $oldMainDoc = $submission->documents()->where('document_type', 'main_document')->first();
            if ($oldMainDoc) {
                Storage::disk('public')->delete($oldMainDoc->file_path);
                $oldMainDoc->delete();
            }

            // Upload new main document
            $this->uploadDocument($request->file('main_document'), $submission, 'main_document');
        }

        // Handle new supporting documents
        if ($request->hasFile('supporting_documents')) {
            foreach ($request->file('supporting_documents') as $index => $file) {
                $this->uploadDocument($file, $submission, 'supporting_document', $index);
            }
        }

        // Create history record
        $action = '';
        $notes = '';
        
        if ($oldStatus === 'revision_needed' && $newStatus === 'submitted') {
            $action = 'Revision Submitted';
            $notes = 'User submitted revision based on reviewer feedback';
        } elseif ($request->has('save_as_draft')) {
            $action = 'Updated as Draft';
            $notes = 'Submission updated and saved as draft';
        } else {
            $action = 'Updated and Submitted';
            $notes = 'Submission updated and resubmitted for review';
        }

        SubmissionHistory::create([
            'submission_id' => $submission->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'previous_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes' => $notes,
        ]);

        // Determine success message
        if ($oldStatus === 'revision_needed' && $newStatus === 'submitted') {
            $message = 'Revisi berhasil disubmit dan akan direview kembali';
        } elseif ($request->has('save_as_draft')) {
            $message = 'Submission berhasil diupdate dan disimpan sebagai draft';
        } else {
            $message = 'Submission berhasil diupdate dan disubmit untuk review';
        }

        return redirect()->route('user.submissions.show', $submission)
            ->with('success', $message);
    }

    public function destroy(HkiSubmission $submission)
    {
        $this->authorize('delete', $submission);
        
        // Delete associated files
        foreach ($submission->documents as $document) {
            Storage::disk('public')->delete($document->file_path);
        }

        // Delete submission (will cascade delete documents and histories)
        $submission->delete();

        return redirect()->route('user.submissions.index')
            ->with('success', 'Submission berhasil dihapus');
    }

    public function downloadDocument(SubmissionDocument $document)
    {
        $submission = $document->submission;
        $this->authorize('view', $submission);
        
        $filePath = storage_path('app/public/' . $document->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download($filePath, $document->file_name);
    }

    public function deleteDocument(SubmissionDocument $document)
    {
        $submission = $document->submission;
        $this->authorize('update', $submission);
        
        // Don't allow deleting main document
        if ($document->document_type === 'main_document') {
            return back()->withErrors(['error' => 'Dokumen utama tidak dapat dihapus']);
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Dokumen berhasil dihapus');
    }

    public function downloadKtp(HkiSubmission $submission, SubmissionMember $member)
    {
        // Authorize user can only download their own submission KTPs
        $this->authorize('view', $submission);
        
        if ($member->submission_id !== $submission->id) {
            abort(404, 'KTP tidak ditemukan.');
        }
        
        if (!$member->ktp) {
            abort(404, 'File KTP tidak tersedia.');
        }
        
        $filePath = storage_path('app/public/' . $member->ktp);
        
        if (!file_exists($filePath)) {
            abort(404, 'File KTP tidak ditemukan di server.');
        }
        
        $fileName = 'KTP_' . str_replace(' ', '_', $member->name) . '_' . $submission->id . '.jpg';
        
        return response()->download($filePath, $fileName);
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
                
            case 'poster_fotografi':
                $rules['image_files'] = 'nullable|array';
                $rules['image_files.*'] = 'file|mimes:jpg,jpeg,png|max:1024'; // Optional untuk update
                break;
                
            case 'alat_peraga':
                $rules['subject'] = 'required|string|max:255';
                $rules['education_level'] = 'required|in:sd,smp,sma,kuliah';
                $rules['photo_files'] = 'nullable|array';
                $rules['photo_files.*'] = 'file|mimes:jpg,jpeg,png|max:1024'; // Optional untuk update
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
                
            case 'poster_fotografi':
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