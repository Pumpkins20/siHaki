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

    private function getAdditionalData($request)
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
}