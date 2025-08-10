<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HkiSubmission;
use App\Models\SubmissionDocument;
use App\Models\SubmissionHistory;
use App\Models\SubmissionMember;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
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
            // ✅ UPDATED: Hanya tampilkan status aktif (submitted, under_review, revision_needed, draft)
            ->whereIn('status', ['draft', 'submitted', 'under_review', 'revision_needed'])
            ->latest();

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $allowedStatuses = ['draft', 'submitted', 'under_review', 'revision_needed'];
            if (in_array($request->status, $allowedStatuses)) {
                $query->where('status', $request->status);
            }
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

        return view('user.submissions.index', compact('submissions'));
    }

    public function create()
    {
        return view('user.submissions.create');
    }

    public function store(Request $request)
    {
        try {
            // ✅ UPDATED: Remove alamat and kode_pos from submission level, now per member
            $rules = [
                'title' => 'required|string|max:255',
                'creation_type' => 'required|in:program_komputer,sinematografi,buku,poster,fotografi,seni_gambar,karakter_animasi,alat_peraga,basis_data',
                'description' => 'required|string|max:1000',
                'first_publication_date' => 'required|date|before_or_equal:today',
                'member_count' => 'required|integer|min:2|max:6', // ✅ UPDATED: Max 6 anggota
                'members' => 'required|array|min:2|max:6', // ✅ UPDATED: Max 6 anggota
                'members.*.name' => 'required|string|max:255',
                'members.*.whatsapp' => 'required|string|regex:/^[0-9]{10,13}$/',
                'members.*.email' => 'required|email',
                'members.*.alamat' => 'required|string|max:500', // ✅ NEW: Alamat per anggota
                'members.*.kode_pos' => 'required|string|size:5|regex:/^[0-9]+$/', // ✅ NEW: Kode pos per anggota
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
                'member_count.max' => 'Maksimal 6 anggota pencipta', // ✅ UPDATED
                'members.*.name.required' => 'Nama anggota harus diisi',
                'members.*.whatsapp.required' => 'No. WhatsApp anggota harus diisi',
                'members.*.whatsapp.regex' => 'Format No. WhatsApp tidak valid (10-13 digit)',
                'members.*.email.required' => 'Email anggota harus diisi',
                'members.*.email.email' => 'Format email tidak valid',
                'members.*.alamat.required' => 'Alamat anggota harus diisi', // ✅ NEW
                'members.*.alamat.max' => 'Alamat anggota maksimal 500 karakter', // ✅ NEW
                'members.*.kode_pos.required' => 'Kode pos anggota harus diisi', // ✅ NEW
                'members.*.kode_pos.size' => 'Kode pos anggota harus 5 digit', // ✅ NEW
                'members.*.kode_pos.regex' => 'Kode pos anggota hanya boleh angka', // ✅ NEW
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

            // ✅ UPDATED: Remove alamat and kode_pos from submission creation
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
     * ✅ UPDATED: Update submission - remove alamat/kode_pos from submission level
     */
    public function update(Request $request, HkiSubmission $submission)
    {
        try {
            // Check authorization
            if ($submission->user_id !== Auth::id()) {
                abort(403, 'Anda tidak memiliki akses ke submission ini.');
            }

            // Check if submission can be edited
            if (!in_array($submission->status, ['draft', 'revision_needed'])) {
                return back()->withErrors(['error' => 'Submission ini tidak dapat diedit karena statusnya sudah ' . $submission->status]);
            }

            Log::info('Starting submission update', [
                'submission_id' => $submission->id,
                'user_id' => Auth::id(),
                'current_status' => $submission->status,
                'creation_type' => $submission->creation_type,
                'request_data' => $request->except(['_token', '_method', 'members'])
            ]);

            // ✅ UPDATED: Remove alamat/kode_pos from submission level validation
            $rules = [
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:1000',
                'first_publication_date' => 'required|date|before_or_equal:today',
                'members' => 'required|array|min:2|max:6', // ✅ UPDATED: Max 6 anggota
                'members.*.id' => 'required|exists:submission_members,id',
                'members.*.name' => 'required|string|max:255',
                'members.*.whatsapp' => 'required|string|regex:/^[0-9]{10,13}$/',
                'members.*.email' => 'required|email',
                'members.*.alamat' => 'required|string|max:500', // ✅ NEW: Alamat per anggota
                'members.*.kode_pos' => 'required|string|size:5|regex:/^[0-9]+$/', // ✅ NEW: Kode pos per anggota
                'members.*.ktp' => 'nullable|file|mimes:jpg,jpeg|max:2048', // Optional untuk update
            ];

            // Add dynamic validation based on creation type
            $this->addDynamicValidationForUpdate($rules, $submission->creation_type);

            $customMessages = [
                'title.required' => 'Judul harus diisi',
                'description.required' => 'Deskripsi harus diisi',
                'first_publication_date.required' => 'Tanggal publikasi harus diisi',
                'first_publication_date.before_or_equal' => 'Tanggal tidak boleh lebih dari hari ini',
                'members.*.name.required' => 'Nama anggota harus diisi',
                'members.*.whatsapp.required' => 'WhatsApp anggota harus diisi',
                'members.*.whatsapp.regex' => 'Format WhatsApp tidak valid (10-13 digit)',
                'members.*.email.required' => 'Email anggota harus diisi',
                'members.*.email.email' => 'Format email tidak valid',
                'members.*.alamat.required' => 'Alamat anggota harus diisi', // ✅ NEW
                'members.*.alamat.max' => 'Alamat anggota maksimal 500 karakter', // ✅ NEW
                'members.*.kode_pos.required' => 'Kode pos anggota harus diisi', // ✅ NEW
                'members.*.kode_pos.size' => 'Kode pos anggota harus 5 digit', // ✅ NEW
                'members.*.kode_pos.regex' => 'Kode pos anggota hanya boleh angka', // ✅ NEW
                'members.*.ktp.mimes' => 'File KTP harus dalam format JPG/JPEG',
                'members.*.ktp.max' => 'Ukuran file KTP maksimal 2MB',
            ];

            $validated = $request->validate($rules, $customMessages);

            Log::info('Validation passed', [
                'submission_id' => $submission->id,
                'validated_data' => $validated
            ]);

            DB::beginTransaction();

            try {
                $oldStatus = $submission->status;
                $newStatus = $request->has('save_as_draft') ? 'draft' : 'submitted';

                // ✅ FIXED: Get additional data for update (with null check)
                $additionalData = [];
                try {
                    $additionalData = $this->getAdditionalDataForUpdate($request, $submission->creation_type);
                } catch (\Exception $e) {
                    Log::warning('Failed to get additional data', [
                        'submission_id' => $submission->id,
                        'creation_type' => $submission->creation_type,
                        'error' => $e->getMessage()
                    ]);
                }
                
                Log::info('Updating submission basic data', [
                    'submission_id' => $submission->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'additional_data' => $additionalData
                ]);

                // ✅ UPDATED: Remove alamat/kode_pos from submission update
                $updateData = [
                    'title' => $request->title,
                    'description' => $request->description,
                    'first_publication_date' => $request->first_publication_date,
                    'status' => $newStatus,
                ];

                // Add additional_data if exists
                if (!empty($additionalData)) {
                    $updateData['additional_data'] = $additionalData;
                }

                // Clear review notes if resubmitting
                if ($oldStatus === 'revision_needed' && $newStatus === 'submitted') {
                    $updateData['review_notes'] = null;
                    $updateData['reviewed_at'] = null;
                    $updateData['reviewer_id'] = null;
                }

                // Update submission date for new submissions
                if ($newStatus === 'submitted' && $oldStatus === 'draft') {
                    $updateData['submission_date'] = now();
                } elseif ($newStatus === 'submitted' && $oldStatus === 'revision_needed') {
                    // Keep original submission date for revisions
                    $updateData['submission_date'] = $submission->submission_date ?? now();
                }

                $submission->update($updateData);

                Log::info('Submission basic data updated', [
                    'submission_id' => $submission->id,
                    'update_data' => $updateData
                ]);

                // ✅ UPDATED: Update members with alamat and kode_pos
                if ($request->has('members')) {
                    foreach ($request->members as $index => $memberData) {
                        try {
                            $member = SubmissionMember::find($memberData['id']);
                            
                            if ($member && $member->submission_id === $submission->id) {
                                $member->update([
                                    'name' => $memberData['name'],
                                    'whatsapp' => $memberData['whatsapp'],
                                    'email' => $memberData['email'],
                                    'alamat' => $memberData['alamat'], // ✅ NEW
                                    'kode_pos' => $memberData['kode_pos'], // ✅ NEW
                                ]);

                                // Handle KTP upload for this member
                                if (isset($memberData['ktp']) && $request->hasFile("members.{$index}.ktp")) {
                                    try {
                                        $ktpFile = $request->file("members.{$index}.ktp");
                                        
                                        // Delete old KTP if exists
                                        if ($member->ktp && Storage::disk('public')->exists($member->ktp)) {
                                            Storage::disk('public')->delete($member->ktp);
                                            Log::info('Old KTP deleted', [
                                                'member_id' => $member->id,
                                                'old_path' => $member->ktp
                                            ]);
                                        }

                                        // Upload new KTP
                                        $ktpPath = $this->uploadKtpFile($ktpFile, $submission->id, $member->id);
                                        if ($ktpPath) {
                                            $member->update(['ktp' => $ktpPath]);
                                            Log::info('KTP updated for member', [
                                                'member_id' => $member->id,
                                                'ktp_path' => $ktpPath
                                            ]);
                                        } else {
                                            Log::warning('Failed to upload KTP', [
                                                'member_id' => $member->id,
                                                'submission_id' => $submission->id
                                            ]);
                                        }
                                    } catch (\Exception $e) {
                                        Log::error('KTP upload error for member', [
                                            'member_id' => $member->id,
                                            'error' => $e->getMessage()
                                        ]);
                                        // Don't fail the whole update for KTP upload error
                                    }
                                }

                                Log::info('Member updated with address', [
                                    'member_id' => $member->id,
                                    'submission_id' => $submission->id,
                                    'has_alamat' => !empty($memberData['alamat']),
                                    'has_kode_pos' => !empty($memberData['kode_pos'])
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::error('Failed to update member', [
                                'index' => $index,
                                'member_data' => $memberData,
                                'error' => $e->getMessage()
                            ]);
                            // Continue with other members
                        }
                    }
                }

                // ✅ FIXED: Handle file uploads with better error handling
                try {
                    $this->handleFileUploadsForUpdate($request, $submission);
                    Log::info('File uploads handled successfully', [
                        'submission_id' => $submission->id
                    ]);
                } catch (\Exception $e) {
                    Log::error('File upload handling failed', [
                        'submission_id' => $submission->id,
                        'error' => $e->getMessage()
                    ]);
                    // Don't fail the whole update for file upload errors
                }

                // Create history record for status change
                if ($oldStatus !== $newStatus) {
                    SubmissionHistory::create([
                        'submission_id' => $submission->id,
                        'user_id' => Auth::id(),
                        'action' => $newStatus === 'draft' ? 'Updated as Draft' : 'Resubmitted for Review',
                        'previous_status' => $oldStatus,
                        'new_status' => $newStatus,
                        'notes' => $newStatus === 'submitted' ? 'Submission updated and resubmitted' : 'Submission updated and saved as draft'
                    ]);

                    // Send notification for resubmission
                    if ($newStatus === 'submitted' && $oldStatus === 'revision_needed') {
                        try {
                            // Import User model at the top of the file
                            $adminUsers = \App\Models\User::role('admin')->get();
                            \Illuminate\Support\Facades\Notification::send(
                                $adminUsers,
                                new SubmissionStatusChanged(
                                    $submission,
                                    $oldStatus,
                                    $newStatus,
                                    'Submission telah direvisi dan diresubmit untuk review ulang.'
                                )
                            );
                        } catch (\Exception $e) {
                            Log::error('Failed to send notification', [
                                'submission_id' => $submission->id,
                                'error' => $e->getMessage()
                            ]);
                            // Don't fail the update for notification error
                        }
                    }
                }

                DB::commit();

                Log::info('Submission update completed successfully', [
                    'submission_id' => $submission->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus
                ]);

                $message = $request->has('save_as_draft') ? 
                    'Submission berhasil diupdate dan disimpan sebagai draft.' :
                    ($oldStatus === 'revision_needed' ? 
                        'Revisi berhasil disubmit! Submission akan direview kembali.' :
                        'Submission berhasil diupdate dan diresubmit untuk review.');

                return redirect()->route('user.submissions.show', $submission)
                    ->with('success', $message);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e; // Re-throw to be caught by outer try-catch
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed', [
                'submission_id' => $submission->id,
                'errors' => $e->errors()
            ]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Submission update failed', [
                'submission_id' => $submission->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['_token', '_method'])
            ]);

            return back()->withErrors(['error' => 'Terjadi kesalahan saat mengupdate submission: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * ✅ UPDATED: Handle member data and KTP uploads with alamat/kode_pos
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
                
                // ✅ FIXED: Create member record dengan alamat dan kode_pos
                SubmissionMember::create([
                    'submission_id' => $submission->id,
                    'name' => $memberData['name'],
                    'email' => $memberData['email'],
                    'whatsapp' => $memberData['whatsapp'],
                    'alamat' => $memberData['alamat'] ?? '', // ✅ FIXED: Include alamat with fallback
                    'kode_pos' => $memberData['kode_pos'] ?? '', // ✅ FIXED: Include kode_pos with fallback
                    'position' => $index + 1,
                    'is_leader' => $index === 0, // ✅ UPDATED: First member (index 0) is leader
                    'ktp' => $ktpPath,
                ]);

                Log::info('Member created with address', [
                    'submission_id' => $submission->id,
                    'member_index' => $index,
                    'name' => $memberData['name'],
                    'is_leader' => $index === 0,
                    'has_alamat' => !empty($memberData['alamat'] ?? ''),
                    'has_kode_pos' => !empty($memberData['kode_pos'] ?? '')
                ]);
            }
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
                break;
                
            case 'buku':
                $rules['ebook_file'] = 'required|file|mimes:pdf|max:20480'; // 20MB
                break;
                
            case 'poster':
            case 'fotografi':
            case 'seni_gambar':
            case 'karakter_animasi':
                // ✅ UNIFIED: Semua jenis visual menggunakan rules yang sama
                $rules['image_files'] = 'required|array|min:1';
                $rules['image_files.*'] = 'file|mimes:jpg,jpeg,png|max:2048'; // 2MB per file
                break;
                
            case 'alat_peraga':
                $rules['photo_files.*'] = 'file|mimes:jpg,jpeg,png|max:2048'; // 2MB per file
                break;
                
            case 'basis_data':
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
     * ✅ ENHANCED: Get additional data for update
     */
    private function getAdditionalDataForUpdate(Request $request, $creationType)
    {
        $data = [];
        
        Log::info('Getting additional data for update', [
            'creation_type' => $creationType,
            'request_data' => $request->except(['_token', '_method', 'members'])
        ]);
        
        switch ($creationType) {
            case 'program_komputer':
                if ($request->filled('program_link')) {
                    $data['program_link'] = $request->program_link;
                }
                break;
                
            case 'sinematografi':
                if ($request->filled('video_link')) {
                    $data['video_link'] = $request->video_link;
                }
                break;
                
            case 'buku':
                if ($request->filled('isbn')) {
                    $data['isbn'] = $request->isbn;
                }
                if ($request->filled('page_count')) {
                    $data['page_count'] = $request->page_count;
                }
                break;
                
            // ✅ UNIFIED: Additional data untuk semua jenis visual
            case 'poster':
            case 'fotografi':
            case 'seni_gambar':
            case 'karakter_animasi':
                $data['visual_type'] = $creationType;
                if ($request->filled('width')) {
                    $data['width'] = $request->width;
                }
                if ($request->filled('height')) {
                    $data['height'] = $request->height;
                }
                if ($request->filled('image_description')) {
                    $data['image_description'] = $request->image_description;
                }
                break;
                
            case 'alat_peraga':
                if ($request->filled('subject')) {
                    $data['subject'] = $request->subject;
                }
                if ($request->filled('education_level')) {
                    $data['education_level'] = $request->education_level;
                }
                break;
                
            case 'basis_data':
                if ($request->filled('database_type')) {
                    $data['database_type'] = $request->database_type;
                }
                if ($request->filled('record_count')) {
                    $data['record_count'] = $request->record_count;
                }
                break;
        }
        
        Log::info('Additional data prepared for update', [
            'creation_type' => $creationType,
            'data' => $data
        ]);
        
        return $data;
    }

    /**
     * ✅ ENHANCED: Upload document for update with better error handling
     */
    private function uploadDocumentForUpdate(Request $request, HkiSubmission $submission, $fieldName, $documentType, $file = null, $index = null)
    {
        if ($file === null) {
            $file = $request->file($fieldName);
        }

        if (!$file || !$file->isValid()) {
            Log::warning('Invalid file upload attempted during update', [
                'submission_id' => $submission->id,
                'field_name' => $fieldName,
                'file_valid' => $file ? $file->isValid() : false,
                'file_error' => $file ? $file->getError() : 'No file'
            ]);
            return false;
        }

        // Generate unique filename
        $timestamp = time();
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $indexSuffix = $index !== null ? '_' . ($index + 1) : '';
        
        // Create directory structure
        $submissionDir = 'submissions/' . $submission->id;
        if (!Storage::disk('public')->exists($submissionDir)) {
            Storage::disk('public')->makeDirectory($submissionDir);
        }
        
        $fileName = 'updated_' . $timestamp . '_' . $fieldName . $indexSuffix . '_' . $originalName . '.' . $extension;
        $fullPath = $submissionDir . '/' . $fileName;
        
        try {
            // Store file
            $filePath = $file->storeAs($submissionDir, $fileName, 'public');
            
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
                'file_size' => $file->getSize(),
                'document_type' => $documentType
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to upload document during update', [
                'submission_id' => $submission->id,
                'field_name' => $fieldName,
                'file_name' => $fileName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Clean up partially stored file if exists
            if (Storage::disk('public')->exists($fullPath)) {
                Storage::disk('public')->delete($fullPath);
            }
            
            return false;
        }
    }

    /**
     * ✅ ENHANCED: Delete existing documents of specific type with better error handling
     */
    private function deleteExistingDocuments(HkiSubmission $submission, $documentType)
    {
        try {
            $documents = $submission->documents()->where('document_type', $documentType)->get();
            
            foreach ($documents as $document) {
                // Delete physical file
                if (Storage::disk('public')->exists($document->file_path)) {
                    Storage::disk('public')->delete($document->file_path);
                    Log::info('Old document file deleted during update', [
                        'submission_id' => $submission->id,
                        'document_id' => $document->id,
                        'file_path' => $document->file_path
                    ]);
                }
                
                // Delete database record
                $document->delete();
            }
            
            Log::info('Existing documents deleted successfully', [
                'submission_id' => $submission->id,
                'document_type' => $documentType,
                'count' => $documents->count()
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Error deleting existing documents during update', [
                'submission_id' => $submission->id,
                'document_type' => $documentType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }

    /**
     * ✅ UPDATED: Update KTP for submitted submissions (support alamat)
     */
    public function updateKtp(Request $request, HkiSubmission $submission)
    {
        // Check if user owns this submission
        if ($submission->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke submission ini.');
        }
        
        // Allow KTP update for any status except rejected (more flexible)
        if ($submission->status === 'rejected') {
            return back()->withErrors(['error' => 'KTP tidak dapat diupdate untuk submission yang ditolak.']);
        }

        $request->validate([
            'member_ids' => 'required|array',
            'member_ids.*' => 'required|exists:submission_members,id',
            'ktp_files' => 'required|array',
            'ktp_files.*' => 'required|file|mimes:jpg,jpeg|max:2048',
        ], [
            'ktp_files.*.required' => 'File KTP harus diupload',
            'ktp_files.*.mimes' => 'File KTP harus dalam format JPG/JPEG',
            'ktp_files.*.max' => 'Ukuran file KTP maksimal 2MB',
        ]);

        try {
            DB::beginTransaction();

            $updatedMembers = [];
            $memberIds = $request->member_ids;
            
            foreach ($request->file('ktp_files', []) as $memberId => $ktpFile) {
                // Verify member belongs to this submission
                $member = SubmissionMember::where('id', $memberId)
                    ->where('submission_id', $submission->id)
                    ->first();
                    
                if (!$member) {
                    continue; // Skip invalid member
                }

                // Delete old KTP file if exists
                if ($member->ktp) {
                    Storage::disk('public')->delete($member->ktp);
                    Log::info('Old KTP file deleted during update', [
                        'submission_id' => $submission->id,
                        'member_id' => $member->id,
                        'old_ktp_path' => $member->ktp
                    ]);
                }

                // Upload new KTP
                $ktpFileName = $submission->id . '/ktp_' . $member->id . '_' . time() . '.' . $ktpFile->getClientOriginalExtension();
                
                // Create directory if not exists
                $ktpDir = 'ktp_files/' . $submission->id;
                if (!Storage::disk('public')->exists($ktpDir)) {
                    Storage::disk('public')->makeDirectory($ktpDir);
                }
                
                $ktpPath = $ktpFile->storeAs('ktp_files', $ktpFileName, 'public');
                
                // Update member record
                $member->update(['ktp' => $ktpPath]);
                
                $updatedMembers[] = [
                    'id' => $member->id,
                    'name' => $member->name,
                    'new_ktp_path' => $ktpPath,
                    'file_size' => $ktpFile->getSize(),
                    'alamat' => $member->alamat ?: 'Tidak ada alamat', // ✅ NEW: Include alamat in log
                    'kode_pos' => $member->kode_pos ?: 'Tidak ada kode pos' // ✅ NEW: Include kode_pos in log
                ];

                Log::info('KTP updated via post-submit update', [
                    'submission_id' => $submission->id,
                    'member_id' => $member->id,
                    'member_name' => $member->name,
                    'member_alamat' => $member->alamat,
                    'member_kode_pos' => $member->kode_pos,
                    'new_ktp_path' => $ktpPath,
                    'file_size' => $ktpFile->getSize(),
                    'updated_by' => Auth::id(),
                    'submission_status' => $submission->status
                ]);
            }

            // Create history record
            SubmissionHistory::create([
                'submission_id' => $submission->id,
                'user_id' => Auth::id(),
                'action' => 'KTP Updated',
                'previous_status' => $submission->status,
                'new_status' => $submission->status, // Status remains same
                'notes' => 'KTP anggota diperbarui: ' . collect($updatedMembers)->pluck('name')->join(', ') . 
                          '. Total ' . count($updatedMembers) . ' file KTP berhasil diupdate.'
            ]);

            // ✅ Notify admin about KTP update (optional)
            $adminUsers = User::where('role', 'admin')->get();
            foreach ($adminUsers as $admin) {
                $admin->notify(new \App\Notifications\KtpUpdated(
                    $submission,
                    $updatedMembers,
                    Auth::user()
                ));
            }

            DB::commit();

            return back()->with('ktp_updated', true)
                ->with('success', 'KTP berhasil diperbarui untuk ' . count($updatedMembers) . ' anggota. File KTP baru telah mengganti yang lama.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('KTP update failed: ' . $e->getMessage(), [
                'submission_id' => $submission->id,
                'user_id' => Auth::id(),
                'error' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Terjadi kesalahan saat mengupdate KTP. Silakan coba lagi.']);
        }
    }

    /**
     * ✅ ENHANCED: Preview member KTP for user's own submission
     */
    public function previewMemberKtp(HkiSubmission $submission, SubmissionMember $member)
    {
        // Check if user owns this submission
        if ($submission->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke submission ini.');
        }
        
        // Verify member belongs to submission
        if ($member->submission_id !== $submission->id) {
            abort(404, 'Member not found');
        }

        if (!$member->ktp) {
            return back()->withErrors(['error' => 'KTP file not available for this member']);
        }

        $filePath = storage_path('app/public/' . $member->ktp);
        
        if (!file_exists($filePath)) {
            Log::error('KTP file not found for user preview', [
                'submission_id' => $submission->id,
                'member_id' => $member->id,
                'expected_path' => $filePath
            ]);
            return back()->withErrors(['error' => 'KTP file not found on server']);
        }

        $mimeType = mime_content_type($filePath);
        
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="KTP_' . str_replace(' ', '_', $member->name) . '.jpg"'
        ]);
    }

    /**
     * ✅ ADDED: Handle file uploads for update (using submission's creation_type)
     */
    private function handleFileUploadsForUpdate(Request $request, HkiSubmission $submission)
    {
        try {
            Log::info('Starting file upload handling for update', [
                'submission_id' => $submission->id,
                'creation_type' => $submission->creation_type,
                'files_in_request' => array_keys($request->allFiles())
            ]);

            switch ($submission->creation_type) {
                case 'program_komputer':
                    if ($request->hasFile('manual_document')) {
                        $this->deleteExistingDocuments($submission, 'main_document');
                        $this->uploadDocumentForUpdate($request, $submission, 'manual_document', 'main_document');
                    }
                    break;
                    
                case 'sinematografi':
                    if ($request->hasFile('video_metadata_file')) {
                        $this->deleteExistingDocuments($submission, 'main_document');
                        $this->uploadDocumentForUpdate($request, $submission, 'video_metadata_file', 'main_document');
                    }
                    break;
                    
                case 'buku':
                    if ($request->hasFile('ebook_file')) {
                        $this->deleteExistingDocuments($submission, 'main_document');
                        $this->uploadDocumentForUpdate($request, $submission, 'ebook_file', 'main_document');
                    }
                    break;
                    
                // ✅ FIXED: Handling untuk semua jenis visual dengan case yang tepat
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
                    
                default:
                    // ✅ ADDED: Generic handling untuk creation_type lainnya
                    if ($request->hasFile('main_document')) {
                        $this->deleteExistingDocuments($submission, 'main_document');
                        $this->uploadDocumentForUpdate($request, $submission, 'main_document', 'main_document');
                    }
                    break;
            }

            Log::info('File uploads handled successfully for update', [
                'submission_id' => $submission->id,
                'creation_type' => $submission->creation_type
            ]);

        } catch (\Exception $e) {
            Log::error('File upload handling failed during update', [
                'submission_id' => $submission->id,
                'creation_type' => $submission->creation_type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Don't throw here to prevent breaking the update process
            // Instead, log the error and continue
        }
    }

    /**
     * ✅ FIXED: Build validation rules with alamat and kode_pos for each member
     */
    private function buildValidationRules($creationType, $memberCount = null)
    {
        // Base validation rules
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'creation_type' => 'required|string',
            'first_publication_date' => 'required|date|before_or_equal:today',
            'member_count' => 'required|integer|min:2|max:6', // ✅ UPDATED: max 6
            'members' => 'required|array|min:2|max:6', // ✅ UPDATED: max 6
            'members.*.name' => 'required|string|max:255',
            'members.*.whatsapp' => 'required|string|regex:/^[0-9]{10,13}$/',
            'members.*.email' => 'required|email',
            'members.*.alamat' => 'required|string|max:500', // ✅ NEW: alamat required for each member
            'members.*.kode_pos' => 'required|string|size:5|regex:/^[0-9]+$/', // ✅ NEW: kode_pos required for each member
            'members.*.ktp' => 'required|file|mimes:jpg,jpeg|max:2048', // KTP required
        ];

        // Add dynamic validation based on creation type
        $this->addDynamicValidation($rules, $creationType);

        return $rules;
    }

    /**
     * ✅ FIXED: Store members with alamat and kode_pos
     */
    private function storeMembers(HkiSubmission $submission, $membersData)
    {
        $memberPosition = 1;
        
        foreach ($membersData as $memberData) {
            // Create member record with alamat and kode_pos
            $member = SubmissionMember::create([
                'submission_id' => $submission->id,
                'name' => $memberData['name'],
                'email' => $memberData['email'],
                'whatsapp' => $memberData['whatsapp'],
                'alamat' => $memberData['alamat'], // ✅ NEW: store alamat
                'kode_pos' => $memberData['kode_pos'], // ✅ NEW: store kode_pos
                'position' => $memberPosition,
                'is_leader' => $memberPosition === 1, // First member is leader
            ]);

            // Handle KTP upload
            if (isset($memberData['ktp'])) {
                $ktpFile = $memberData['ktp'];
                $ktpPath = $this->uploadKtpFile($ktpFile, $submission->id, $member->id);
                
                if ($ktpPath) {
                    $member->update(['ktp' => $ktpPath]);
                }
            }

            $memberPosition++;
        }
    }

    /**
     * ✅ UPDATED: Update validation rules for edit with alamat and kode_pos
     */
    private function addDynamicValidationForUpdate(&$rules, $creationType)
    {
        // Update member validation to include alamat and kode_pos
        $rules['members.*.alamat'] = 'required|string|max:500'; // ✅ NEW
        $rules['members.*.kode_pos'] = 'required|string|size:5|regex:/^[0-9]+$/'; // ✅ NEW
    
        try {
            switch ($creationType) {
                case 'program_komputer':
                    $rules['program_link'] = 'required|url';
                    $rules['manual_document'] = 'nullable|file|mimes:pdf|max:20480';
                    break;
                    
                case 'sinematografi':
                    $rules['video_link'] = 'required|url';
                    $rules['video_metadata_file'] = 'nullable|file|mimes:pdf|max:20480';
                    break;
                    
                case 'buku':
                    $rules['isbn'] = 'nullable|string|max:20';
                    $rules['page_count'] = 'nullable|integer|min:1|max:10000';
                    $rules['ebook_file'] = 'nullable|file|mimes:pdf|max:20480';
                    break;
                    
                case 'poster':
                case 'fotografi':
                case 'seni_gambar':
                case 'karakter_animasi':
                    $rules['image_files'] = 'nullable|array';
                    $rules['image_files.*'] = 'file|mimes:jpg,jpeg,png|max:1024';
                    $rules['width'] = 'nullable|numeric|min:1';
                    $rules['height'] = 'nullable|numeric|min:1';
                    $rules['image_description'] = 'nullable|string|max:500';
                    break;
                    
                case 'alat_peraga':
                    $rules['photo_files'] = 'nullable|array';
                    $rules['photo_files.*'] = 'file|mimes:jpg,jpeg,png|max:1024';
                    $rules['subject'] = 'nullable|string|max:255';
                    $rules['education_level'] = 'nullable|string|max:255';
                    break;
                    
                case 'basis_data':
                    $rules['documentation_file'] = 'nullable|file|mimes:pdf|max:20480';
                    $rules['database_type'] = 'nullable|string|max:255';
                    $rules['record_count'] = 'nullable|integer|min:1';
                    break;

                default:
                    $rules['main_document'] = 'nullable|file|mimes:pdf,doc,docx|max:10240';
                    break;
            }

            Log::info('Dynamic validation rules added', [
                'creation_type' => $creationType,
                'member_rules_added' => ['alamat', 'kode_pos']
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to add dynamic validation rules', [
                'creation_type' => $creationType,
                'error' => $e->getMessage()
            ]);
        }
    }
}