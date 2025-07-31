<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\HkiSubmission;
use App\Models\Department;
use App\Models\SubmissionHistory;
use App\Models\SubmissionDocument;
use App\Models\SubmissionMember;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Exports\SubmissionsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Notifications\SubmissionStatusChanged;
use App\Notifications\CertificateSent;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;

class AdminController extends Controller
{

    // ================= DASHBOARD ================
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_submissions' => HkiSubmission::count(),
            'pending_reviews' => HkiSubmission::where('status', 'submitted')->count(),
            'under_review' => HkiSubmission::where('status', 'under_review')->count(),
            
            // ✅ UNIFIED: Tambah statistik untuk semua status
            'approved' => HkiSubmission::where('status', 'approved')->count(),
            'rejected' => HkiSubmission::where('status', 'rejected')->count(),
            'revision_needed' => HkiSubmission::where('status', 'revision_needed')->count(),
            'submitted' => HkiSubmission::where('status', 'submitted')->count(),
            
            // Today's activity
            'approved_today' => HkiSubmission::where('status', 'approved')
                                       ->whereDate('reviewed_at', today())
                                       ->count(),
            'my_reviews' => HkiSubmission::where('reviewer_id', Auth::id())
                                    ->whereNotNull('reviewed_at')
                                    ->count(),
        ];

        $recent_submissions = HkiSubmission::with(['user'])
            ->whereIn('status', ['submitted', 'under_review'])
            ->orderBy('submission_date', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_submissions'));
    }


    // ================== USER MANAGEMENT ======================

    public function users(Request $request)
    {
        // Statistics
        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'admin' => User::where('role', 'admin')->count(),
            'user' => User::where('role', 'user')->count(),
        ];

        // Build query with filters
        $query = User::with('department');

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        // Filter by program studi
        if ($request->filled('program_studi')) {
            $query->where('program_studi', $request->program_studi);
        }
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                ->orWhere('nidn', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('nama')->paginate(20);

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function storeUser(Request $request)
    {
        try {
            Log::info('Store user request received', [
                'request_data' => $request->except(['password']),
                'user_agent' => $request->userAgent()
            ]);

            $request->validate([
                'nidn' => 'required|string|unique:users,nidn',
                'nama' => 'required|string|max:255',
                'username' => 'required|string|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'program_studi' => 'required|in:D3 Manajemen Informatika,S1 Informatika,S1 Sistem Informasi,S1 Teknologi Informasi',
                'department_id' => 'required|exists:departments,id',
                'phone' => 'nullable|string|max:20',
                'role' => 'required|in:user,admin', // ✅ Add role validation
                'is_active' => 'nullable|boolean',
            ], [
                'nidn.required' => 'NIDN harus diisi',
                'nidn.unique' => 'NIDN sudah digunakan',
                'nama.required' => 'Nama harus diisi',
                'username.required' => 'Username harus diisi',
                'username.unique' => 'Username sudah digunakan',
                'email.required' => 'Email harus diisi',
                'email.unique' => 'Email sudah digunakan',
                'program_studi.required' => 'Program studi harus dipilih',
                'department_id.required' => 'Departemen harus dipilih',
                'role.required' => 'Role harus dipilih',
            ]);

            Log::info('Validation passed, creating user');

            // ✅ FIXED: Create user with all required fields
            $user = User::create([
                'nidn' => $request->nidn,
                'nama' => $request->nama,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->nidn), // ✅ Password = NIDN
                'program_studi' => $request->program_studi,
                'foto' => 'default.png',
                'role' => $request->role ?? 'user', // ✅ Use form input or default to user
                'phone' => $request->phone,
                'department_id' => $request->department_id,
                'is_active' => $request->boolean('is_active', true), // ✅ Default to true
                'email_verified_at' => now(), // ✅ Mark as verified
            ]);

            Log::info('User created successfully', [
                'user_id' => $user->id,
                'username' => $user->username,
                'email' => $user->email
            ]);

            // ✅ FIXED: Proper success message with typo fix
            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil ditambahkan dengan password default: ' . $request->nidn);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed', [
                'errors' => $e->errors(),
                'input' => $request->except(['password'])
            ]);
            
            return back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('User creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except(['password'])
            ]);

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan user: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // Show user details
    public function showUser(User $user)
    {
        $user->load(['department', 'submissions']);
        return view('admin.users.show', compact('user'));
    }

    // Edit user form
    public function editUser(User $user)
    {
        $departments = Department::orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'departments'));
    }

    // Update user
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'program_studi' => 'required|in:D3 Manajemen Informatika,S1 Informatika,S1 Sistem Informasi,S1 Teknologi Informasi',
            'department_id' => 'required|exists:departments,id',
            'role' => 'required|in:user,admin',
            'is_active' => 'nullable|boolean',
            'reset_password' => 'nullable|boolean',
        ], [
            'nama.required' => 'Nama harus diisi',
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.required' => 'Email harus diisi',
            'email.unique' => 'Email sudah digunakan',
            'program_studi.required' => 'Program studi harus dipilih',
            'department_id.required' => 'Departemen harus dipilih',
            'role.required' => 'Role harus dipilih',
        ]);

        // Prevent user from changing their own role or deactivating themselves
        if ($user->id === Auth::id()) {
            $request->merge([
                'role' => $user->role,
                'is_active' => true
            ]);
        }

        // Update user data
        $updateData = [
            'nama' => $request->nama,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'program_studi' => $request->program_studi,
            'department_id' => $request->department_id,
            'role' => $request->role,
            'is_active' => $request->boolean('is_active'),
        ];

        // Reset password if requested
        if ($request->boolean('reset_password')) {
            $updateData['password'] = Hash::make($user->nidn);
        }
    
        $user->update($updateData);

        $message = 'User berhasil diupdate';
        if ($request->boolean('reset_password')) {
            $message .= ' dan password telah direset ke NIDN default';
        }

        return redirect()->route('admin.users.show', $user)
            ->with('success', $message);
    }

    // Delete user
    public function destroyUser(User $user)
    {
        // Prevent deleting own account
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun sendiri']);
        }

        // Check if user has submissions
        if ($user->submissions()->count() > 0) {
            return back()->withErrors(['error' => 'User tidak dapat dihapus karena memiliki submission. Nonaktifkan user sebagai gantinya.']);
        }

        // Delete user profile photo if exists
        if ($user->foto && $user->foto !== 'default.png') {
            Storage::disk('public')->delete('profile_photos/' . $user->foto);
        }

        $userName = $user->nama;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$userName}' berhasil dihapus");
    }

    // Reset user password
    public function resetPassword(User $user)
    {
        $user->update([
            'password' => Hash::make($user->nidn)
        ]);

        return back()->with('success', "Password user '{$user->nama}' berhasil direset ke NIDN default");
    }

    // Toggle user status (active/inactive)
    public function toggleStatus(User $user)
    {
        // Prevent deactivating own account
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menonaktifkan akun sendiri']);
        }

        $newStatus = !$user->is_active;
        $user->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "User '{$user->nama}' berhasil {$statusText}");
    }


    // Update createUser method to include departments
    public function createUser()
    {
        $departments = Department::orderBy('name')->get();
        return view('admin.users.create', compact('departments'));
    }

    // ================== SUBMISSION MANAGEMENT & REVIEW ==================
    public function submissions(Request $request)
    {
        // Calculate statistics
        $stats = [
            'total' => HkiSubmission::whereIn('status', ['submitted', 'under_review', 'revision_needed'])->count(),
            'need_review' => HkiSubmission::where('status', 'submitted')->count(),
            'under_review' => HkiSubmission::where('status', 'under_review')->count(),
            'completed' => HkiSubmission::whereIn('status', ['approved', 'rejected'])->count(),
        ];

        $query = HkiSubmission::with(['user', 'reviewer'])
            ->whereIn('status', ['submitted', 'under_review', 'revision_needed']);

        // Filter by status
        if ($request->filled('status')) {
            $allowedStatuses = ['submitted', 'under_review', 'revision_needed'];
            if (in_array($request->status, $allowedStatuses)) {
                $query->where('status', $request->status);
            }
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by creation_type
        if ($request->filled('creation_type')) {
            $query->where('creation_type', $request->creation_type);
        }

        // Filter by review assignment
        if ($request->filled('assignment')) {
            switch ($request->assignment) {
                case 'unassigned':
                    $query->where('status', 'submitted')->whereNull('reviewer_id');
                    break;
                case 'my_reviews':
                    $query->where('reviewer_id', Auth::id());
                    break;
            }
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('nama', 'like', "%{$search}%")
                               ->orWhere('nidn', 'like', "%{$search}%");
                  });
            });
        }

        $submissions = $query->latest()->paginate(15);

        return view('admin.submissions.index', compact('submissions', 'stats'));
    }

    /**
     * Show submission detail for admin review
     */
    public function showSubmission(HkiSubmission $submission)
    {
        $submission->load([
            'user.department',
            'members',
            'documents',
            'histories.user',
            'reviewer'
        ]);

        return view('admin.submissions.show', compact('submission'));
    }

    /**
     * Approve submission
     */
    public function approveSubmission(Request $request, HkiSubmission $submission)
    {
        $request->validate([
            'review_notes' => 'required|string|min:10|max:1000'
        ], [
            'review_notes.required' => 'Catatan approval harus diisi',
            'review_notes.min' => 'Catatan approval minimal 10 karakter',
            'review_notes.max' => 'Catatan approval maksimal 1000 karakter'
        ]);

        // Check authorization
        if ($submission->status !== 'under_review' || $submission->reviewer_id !== Auth::id()) {
            return back()->withErrors(['error' => 'Anda tidak memiliki akses untuk approve submission ini.']);
        }

        $previousStatus = $submission->status;

        $submission->update([
            'status' => 'approved',
            'review_notes' => $request->review_notes,
            'reviewed_at' => now()
        ]);

        // Create history record
        SubmissionHistory::create([
            'submission_id' => $submission->id,
            'user_id' => Auth::id(),
            'action' => 'Approved',
            'previous_status' => $previousStatus,
            'new_status' => 'approved',
            'notes' => $request->review_notes
        ]);

        // ✅ Send notification to user
        $submission->user->notify(new SubmissionStatusChanged(
            $submission, 
            $previousStatus, 
            'approved', 
            $request->review_notes
        ));

        return redirect()->route('admin.submissions.index')
            ->with('success', "Submission '{$submission->title}' berhasil diapprove dan notifikasi telah dikirim.");
    }

    /**
     * Request revision for submission
     */
    public function revisionSubmission(Request $request, HkiSubmission $submission)
    {
        $request->validate([
            'review_notes' => 'required|string|min:20|max:1000'
        ], [
            'review_notes.required' => 'Catatan revisi harus diisi',
            'review_notes.min' => 'Catatan revisi minimal 20 karakter untuk memberikan panduan yang jelas',
            'review_notes.max' => 'Catatan revisi maksimal 1000 karakter'
        ]);

        // Check authorization
        if ($submission->status !== 'under_review' || $submission->reviewer_id !== Auth::id()) {
            return back()->withErrors(['error' => 'Anda tidak memiliki akses untuk request revision submission ini.']);
        }

        $previousStatus = $submission->status;

        $submission->update([
            'status' => 'revision_needed',
            'review_notes' => $request->review_notes,
            'reviewed_at' => now()
        ]);

        // Create history record
        SubmissionHistory::create([
            'submission_id' => $submission->id,
            'user_id' => Auth::id(),
            'action' => 'Revision Requested',
            'previous_status' => $previousStatus,
            'new_status' => 'revision_needed',
            'notes' => $request->review_notes
        ]);

        // ✅ Send notification to user
        $submission->user->notify(new SubmissionStatusChanged(
            $submission, 
            $previousStatus, 
            'revision_needed', 
            $request->review_notes
        ));

        return redirect()->route('admin.submissions.index')
            ->with('success', "Revision berhasil di-request dan notifikasi telah dikirim.");
    }

    /**
     * Reject submission
     */
    public function rejectSubmission(Request $request, HkiSubmission $submission)
    {
        $request->validate([
            'review_notes' => 'required|string|min:20|max:1000'
        ], [
            'review_notes.required' => 'Alasan penolakan harus diisi',
            'review_notes.min' => 'Alasan penolakan minimal 20 karakter',
            'review_notes.max' => 'Alasan penolakan maksimal 1000 karakter'
        ]);

        // Check authorization
        if ($submission->status !== 'under_review' || $submission->reviewer_id !== Auth::id()) {
            return back()->withErrors(['error' => 'Anda tidak memiliki akses untuk reject submission ini.']);
        }

        $previousStatus = $submission->status;

        $submission->update([
            'status' => 'rejected',
            'review_notes' => $request->review_notes,
            'reviewed_at' => now()
        ]);

        // Create history record
        SubmissionHistory::create([
            'submission_id' => $submission->id,
            'user_id' => Auth::id(),
            'action' => 'Rejected',
            'previous_status' => $previousStatus,
            'new_status' => 'rejected',
            'notes' => $request->review_notes
        ]);

        // ✅ Send notification to user
        $submission->user->notify(new SubmissionStatusChanged(
            $submission, 
            $previousStatus, 
            'rejected', 
            $request->review_notes
        ));

        return redirect()->route('admin.submissions.index')
            ->with('success', "Submission '{$submission->title}' berhasil ditolak dan notifikasi telah dikirim.");
    }

    /**
     * Assign submission to current admin for review
     */
    public function assignToSelf(HkiSubmission $submission)
    {
        if ($submission->status !== 'submitted') {
            return back()->withErrors(['error' => 'Submission ini tidak dapat di-assign karena statusnya sudah berubah.']);
        }

        $previousStatus = $submission->status;

        $submission->update([
            'status' => 'under_review',
            'reviewer_id' => Auth::id(),
        ]);

        // ✅ FIX: Create history with correct field names
        SubmissionHistory::create([
            'submission_id' => $submission->id,
            'user_id' => Auth::id(),
            'action' => 'Assigned for Review',
            'previous_status' => $previousStatus,   // ✅ Add this
            'new_status' => 'under_review',        // ✅ Fix field name
            'notes' => 'Submission di-assign ke ' . Auth::user()->nama . ' untuk review'
        ]);

        return back()->with('success', 'Submission berhasil di-assign untuk Anda review.');
    }

    /**
     * Download submission document
     */
    public function downloadDocument(HkiSubmission $submission, SubmissionDocument $document)
    {
        // Verify document belongs to submission
        if ($document->submission_id !== $submission->id) {
            abort(404);
        }

        // ✅ FIX: Update path sesuai dengan storage actual
        $filePath = storage_path('app/public/' . $document->file_path);
        
        if (!file_exists($filePath)) {
            // Try alternative paths
            $alternativePaths = [
                storage_path('app/private/' . $document->file_path),
                storage_path('app/' . $document->file_path),
            ];
            
            foreach ($alternativePaths as $altPath) {
                if (file_exists($altPath)) {
                    $filePath = $altPath;
                    break;
                }
            }
            
            if (!file_exists($filePath)) {
                return back()->withErrors(['error' => 'File tidak ditemukan.']);
            }
        }

        return response()->download($filePath, $document->file_name);
    }

    /**
     * Preview submission document
     */
    public function previewDocument(HkiSubmission $submission, SubmissionDocument $document)
    {
        Log::info('Preview document called', [
            'submission_id' => $submission->id,
            'document_id' => $document->id,
            'document_path' => $document->file_path
        ]);

        // Verify document belongs to submission
        if ($document->submission_id !== $submission->id) {
            Log::error('Document does not belong to submission', [
                'document_submission_id' => $document->submission_id,
                'expected_submission_id' => $submission->id
            ]);
            abort(404, 'Document not found');
        }

        // ✅ FIX: Remove duplicate 'submissions/' from path
        $filePath = storage_path('app/public/' . $document->file_path);
        
        Log::info('Fixed file path check', [
            'original_file_path' => storage_path('app/private/submissions/' . $document->file_path),
            'fixed_file_path' => $filePath,
            'file_exists' => file_exists($filePath),
            'document_file_path' => $document->file_path
        ]);
        
        if (!file_exists($filePath)) {
            // Try alternative paths
            $alternativePaths = [
                storage_path('app/private/' . $document->file_path),
                storage_path('app/' . $document->file_path),
            ];
            
            foreach ($alternativePaths as $altPath) {
                Log::info('Trying alternative path', [
                    'path' => $altPath,
                    'exists' => file_exists($altPath)
                ]);
                
                if (file_exists($altPath)) {
                    $filePath = $altPath;
                    break;
                }
            }
            
            if (!file_exists($filePath)) {
                Log::error('File not found in any location', [
                    'tried_paths' => array_merge([$filePath], $alternativePaths)
                ]);
                abort(404, 'File not found on server');
            }
        }

        $mimeType = mime_content_type($filePath);
        $extension = strtolower(pathinfo($document->file_name, PATHINFO_EXTENSION));
        
        // For PDF files, display directly in browser
        if ($extension === 'pdf' || $mimeType === 'application/pdf') {
            return response()->file($filePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $document->file_name . '"'
            ]);
        }

        // For image files
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']) || strpos($mimeType, 'image/') === 0) {
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $document->file_name . '"'
            ]);
        }
        
        // For other file types, show a preview page
        return $this->showDocumentPreviewPage($submission, $document, $filePath, $mimeType);
}

    /**
     * Preview member KTP
     */
    public function previewMemberKtp(HkiSubmission $submission, SubmissionMember $member)
    {
        Log::info('Preview KTP called', [
            'submission_id' => $submission->id,
            'member_id' => $member->id,
            'ktp_file' => $member->ktp
        ]);

        // Verify member belongs to submission
        if ($member->submission_id !== $submission->id) {
            abort(404, 'Member not found');
        }

        if (!$member->ktp) {
            abort(404, 'KTP file not available');
        }

        // ✅ FIX: Update path sesuai dengan storage actual
        $filePath = storage_path('app/public/' . $member->ktp);
        
        Log::info('KTP file path check', [
            'expected_path' => $filePath,
            'file_exists' => file_exists($filePath),
            'ktp_value' => $member->ktp
        ]);
        
        if (!file_exists($filePath)) {
            Log::error('KTP file not found', [
                'expected_path' => $filePath,
                'member_ktp' => $member->ktp
            ]);
            abort(404, 'KTP file not found on server');
        }

        $mimeType = mime_content_type($filePath);
        
        // KTP should be image, display inline
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="KTP_' . str_replace(' ', '_', $member->name) . '.jpg"'
        ]);
    }

    /**
     * Enhanced view member KTP with better error handling
     */
    public function viewMemberKtp(HkiSubmission $submission, SubmissionMember $member)
    {
        // Verify member belongs to submission
        if ($member->submission_id !== $submission->id) {
            abort(404, 'Member not found');
        }

        if (!$member->ktp) {
            return back()->withErrors(['error' => 'KTP file not available for this member']);
        }

        // ✅ FIX: Update path sesuai dengan storage actual
        $filePath = storage_path('app/public/' . $member->ktp);
        
        if (!file_exists($filePath)) {
            Log::error('KTP download - file not found', [
                'expected_path' => $filePath,
                'member_ktp' => $member->ktp
            ]);
            return back()->withErrors(['error' => 'KTP file not found on server']);
        }

        $mimeType = mime_content_type($filePath);
        
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="KTP_' . str_replace(' ', '_', $member->name) . '.jpg"'
        ]);
    }

    // ================== DEPARTMENT MANAGEMENT ==================
    public function departments()
    {
        $departments = Department::withCount('users')->paginate(10);
        return view('admin.departments.index', compact('departments'));
    }

    public function createDepartment()
    {
        return view('admin.departments.create');
    }

    public function storeDepartment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:departments',
        ]);

        Department::create($request->only(['name', 'code']));

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department berhasil ditambahkan');
    }

    public function editDepartment(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:departments,code,' . $department->id,
        ]);

        $department->update($request->only(['name', 'code']));

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department berhasil diupdate');
    }

    public function destroyDepartment(Department $department)
    {
        if ($department->users()->count() > 0) {
            return back()->withErrors(['error' => 'Department tidak dapat dihapus karena masih memiliki users']);
        }

        $department->delete();
        return redirect()->route('admin.departments.index')
            ->with('success', 'Department berhasil dihapus');
    }

    // ================== REPORTS ==================
    public function reports(Request $request)
    {
        // Basic Statistics
        $stats = [
            'total_submissions' => HkiSubmission::count(),
            'approved_submissions' => HkiSubmission::where('status', 'approved')->count(),
            'rejected_submissions' => HkiSubmission::where('status', 'rejected')->count(),
            'pending_submissions' => HkiSubmission::whereIn('status', ['submitted', 'under_review'])->count(),
            'revision_submissions' => HkiSubmission::where('status', 'revision_needed')->count(),
            'total_users' => User::where('role', 'user')->count(),
            'active_users' => User::where('role', 'user')->where('is_active', true)->count(),
            'total_departments' => Department::count(),
        ];

        // Filter by date range
        $dateFrom = $request->get('date_from', now()->subMonths(6)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        // Monthly submission trends
        $monthlyStats = HkiSubmission::selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as month,
                COUNT(*) as total,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN status IN ("submitted", "under_review") THEN 1 ELSE 0 END) as pending
            ')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        // Submission by type
        $submissionsByType = HkiSubmission::selectRaw('
                type,
                creation_type,
                COUNT(*) as total,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved
            ')
            ->groupBy('type', 'creation_type')
            ->get();

        // Top performing departments
        $departmentStats = Department::withCount([
                'users',
                'users as active_users_count' => function($query) {
                    $query->where('is_active', true);
                }
            ])
            ->with(['users.submissions' => function($query) {
                $query->selectRaw('user_id, COUNT(*) as total_submissions, 
                                   SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_submissions');
            }])
            ->get()
            ->map(function($dept) {
                $totalSubmissions = $dept->users->sum(function($user) {
                    return $user->submissions->count();
                });
                $approvedSubmissions = $dept->users->sum(function($user) {
                    return $user->submissions->where('status', 'approved')->count();
                });
                
                $dept->total_submissions = $totalSubmissions;
                $dept->approved_submissions = $approvedSubmissions;
                $dept->approval_rate = $totalSubmissions > 0 ? round(($approvedSubmissions / $totalSubmissions) * 100, 1) : 0;
                
                return $dept;
            })
            ->sortByDesc('total_submissions');

        // Average review time
        $avgReviewTime = HkiSubmission::whereNotNull('reviewed_at')
            ->whereNotNull('submission_date')
            ->selectRaw('AVG(DATEDIFF(reviewed_at, submission_date)) as avg_days')
            ->value('avg_days');

        // Recent activities
        $recentActivities = SubmissionHistory::with(['submission', 'user'])
            ->latest()
            ->limit(10)
            ->get();

        // User performance (top submitters)
        $topUsers = User::withCount(['submissions as total_submissions'])
            ->withCount(['submissions as approved_submissions' => function($query) {
                $query->where('status', 'approved');
            }])
            ->where('role', 'user')
            ->having('total_submissions', '>', 0)
            ->orderBy('total_submissions', 'desc')
            ->limit(10)
            ->get();

        return view('admin.reports.index', compact(
            'stats', 
            'monthlyStats', 
            'submissionsByType', 
            'departmentStats', 
            'avgReviewTime',
            'recentActivities',
            'topUsers',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Export detailed reports
     */
    public function exportReports(Request $request)
    {
        $type = $request->get('type', 'summary');
        $format = $request->get('format', 'excel');
        
        switch($type) {
            case 'summary':
                return $this->exportSummaryReport($format);
            case 'detailed':
                return $this->exportDetailedReport($format);
            case 'department':
                return $this->exportDepartmentReport($format);
            case 'user':
                return $this->exportUserReport($format);
            default:
                return back()->withErrors(['error' => 'Invalid report type']);
        }
    }

    private function exportSummaryReport($format)
    {
        $data = [
            'total_submissions' => HkiSubmission::count(),
            'approved' => HkiSubmission::where('status', 'approved')->count(),
            'rejected' => HkiSubmission::where('status', 'rejected')->count(),
            'pending' => HkiSubmission::whereIn('status', ['submitted', 'under_review'])->count(),
        ];

        if ($format === 'excel') {
            // Use Excel export
            return Excel::download(new SummaryReportExport($data), 'summary_report_' . now()->format('Y-m-d') . '.xlsx');
        } else {
            // CSV export
            return $this->exportCSV($data, 'summary_report');
        }
    }

    /**
     * Analytics API for charts
     */
    public function analyticsApi(Request $request)
    {
        $type = $request->get('type');
        
        switch($type) {
            case 'monthly_trends':
                return $this->getMonthlyTrends($request);
            case 'status_distribution':
                return $this->getStatusDistribution();
            case 'type_distribution':
                return $this->getTypeDistribution();
            case 'department_performance':
                return $this->getDepartmentPerformance();
            default:
                return response()->json(['error' => 'Invalid analytics type'], 400);
        }
    }

    private function getMonthlyTrends($request)
    {
        $months = $request->get('months', 12);
        
        $trends = HkiSubmission::selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as month,
                DATE_FORMAT(created_at, "%M %Y") as month_name,
                COUNT(*) as total,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected
            ')
            ->where('created_at', '>=', now()->subMonths($months))
            ->groupBy('month', 'month_name')
            ->orderBy('month')
            ->get();

        return response()->json($trends);
    }

    private function getStatusDistribution()
    {
        $distribution = HkiSubmission::selectRaw('
                status,
                COUNT(*) as count
            ')
            ->groupBy('status')
            ->get()
            ->map(function($item) {
                return [
                    'status' => ucfirst(str_replace('_', ' ', $item->status)),
                    'count' => $item->count,
                    'color' => $this->getStatusColor($item->status)
                ];
            });

        return response()->json($distribution);
    }

    private function getTypeDistribution()
    {
        $distribution = HkiSubmission::selectRaw('
                creation_type,
                COUNT(*) as count
            ')
            ->groupBy('creation_type')
            ->get()
            ->map(function($item) {
                return [
                    'type' => $this->formatCreationType($item->creation_type),
                    'count' => $item->count
                ];
            });

        return response()->json($distribution);
    }

    private function getDepartmentPerformance()
    {
        $performance = Department::withCount(['users'])
            ->get()
            ->map(function($dept) {
                $submissions = HkiSubmission::whereHas('user', function($query) use ($dept) {
                    $query->where('department_id', $dept->id);
                })->count();
                
                $approved = HkiSubmission::whereHas('user', function($query) use ($dept) {
                    $query->where('department_id', $dept->id);
                })->where('status', 'approved')->count();
                
                return [
                    'name' => $dept->name,
                    'users' => $dept->users_count,
                    'submissions' => $submissions,
                    'approved' => $approved,
                    'approval_rate' => $submissions > 0 ? round(($approved / $submissions) * 100, 1) : 0
                ];
            });

        return response()->json($performance);
    }

    private function getStatusColor($status)
    {
        return match($status) {
            'approved' => '#28a745',
            'rejected' => '#dc3545',
            'under_review' => '#17a2b8',
            'submitted' => '#ffc107',
            'revision_needed' => '#fd7e14',
            'draft' => '#6c757d',
            default => '#6c757d'
        };
    }

    /**
     * Certificate Management - Index
     */
    public function certificatesIndex(Request $request)
    {
        $query = HkiSubmission::with(['user', 'documents', 'reviewer'])
            ->where('status', 'approved');

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('nama', 'like', "%{$search}%")
                               ->orWhere('nidn', 'like', "%{$search}%");
                  });
            });
        }

        // Filter berdasarkan status sertifikat
        if ($request->filled('certificate_status')) {
            if ($request->certificate_status === 'sent') {
                $query->whereHas('documents', function($q) {
                    $q->where('document_type', 'certificate');
                });
            } elseif ($request->certificate_status === 'pending') {
                $query->whereDoesntHave('documents', function($q) {
                    $q->where('document_type', 'certificate');
                });
            }
        }

        $submissions = $query->orderBy('reviewed_at', 'desc')->paginate(15);

        $stats = [
            'total_approved' => HkiSubmission::where('status', 'approved')->count(),
            'certificates_sent' => HkiSubmission::whereHas('documents', function($q) {
                $q->where('document_type', 'certificate');
            })->count(),
            'certificates_pending' => HkiSubmission::where('status', 'approved')
                ->whereDoesntHave('documents', function($q) {
                    $q->where('document_type', 'certificate');
                })->count(),
        ];

        return view('admin.certificates.index', compact('submissions', 'stats'));
    }

    /**
     * Certificate Management - Show
     */
    public function certificatesShow(HkiSubmission $submission)
    {
        if ($submission->status !== 'approved') {
            return back()->withErrors(['error' => 'Sertifikat hanya dapat dikirim untuk submission yang sudah approved.']);
        }

        $submission->load([
            'user',
            'documents',
            'members',
            'reviewer',
            'histories.user'
        ]);

        // Check if certificate already sent
        $certificateSent = $submission->documents()->where('document_type', 'certificate')->exists();

        return view('admin.certificates.show', compact('submission', 'certificateSent'));
    }

    /**
     * Send Certificate
     */
    public function sendCertificate(Request $request, HkiSubmission $submission)
    {
        $request->validate([
            'certificate_file' => 'required|file|mimes:pdf|max:10240', // Max 10MB
            'notes' => 'nullable|string|max:500'
        ], [
            'certificate_file.required' => 'File sertifikat harus diupload',
            'certificate_file.mimes' => 'File sertifikat harus dalam format PDF',
            'certificate_file.max' => 'Ukuran file sertifikat maksimal 10MB'
        ]);

        if ($submission->status !== 'approved') {
            return back()->withErrors(['error' => 'Sertifikat hanya dapat dikirim untuk submission yang sudah approved.']);
        }

        try {
            DB::beginTransaction();

            // Upload certificate file
            $file = $request->file('certificate_file');
            $fileName = 'certificate_' . $submission->id . '_' . time() . '.pdf';
            $filePath = $file->storeAs('certificates', $fileName, 'public');

            // Create document record
            $certificate = SubmissionDocument::create([
                'submission_id' => $submission->id,
                'document_type' => 'certificate',
                'file_name' => 'Sertifikat_' . $submission->title . '.pdf',
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_at' => now(),
            ]);

            // Create history record
            SubmissionHistory::create([
                'submission_id' => $submission->id,
                'user_id' => Auth::id(),
                'action' => 'Certificate Sent',
                'previous_status' => 'approved',
                'new_status' => 'approved',
                'notes' => $request->notes ?: 'Sertifikat HKI telah dikirim ke user'
            ]);

            // ✅ Send notification to user
            $submission->user->notify(new CertificateSent(
                $submission, 
                $certificate, 
                $request->notes
            ));

            DB::commit();

            return redirect()->route('admin.certificates.index')
                ->with('success', "Sertifikat berhasil dikirim dan notifikasi telah dikirim ke user.");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Certificate sending failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat mengirim sertifikat. Silakan coba lagi.']);
        }
    }

    /**
     * Download submission document for certificate management
     */
    public function downloadSubmissionDocument(HkiSubmission $submission, SubmissionDocument $document)
    {
        if ($document->submission_id !== $submission->id) {
            abort(404);
        }

        $filePath = storage_path('app/public/' . $document->file_path);
        
        if (!file_exists($filePath)) {
            return back()->withErrors(['error' => 'File tidak ditemukan.']);
        }

        return response()->download($filePath, $document->file_name);
    }

    /**
     * Review History - Index
     */
    public function reviewHistoryIndex(Request $request)
    {
        $query = HkiSubmission::with(['user', 'reviewer'])
            ->whereNotNull('reviewed_at')
            ->whereIn('status', ['approved', 'rejected']);

        // Filter berdasarkan reviewer
        if ($request->filled('reviewer_id')) {
            $query->where('reviewer_id', $request->reviewer_id);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('reviewed_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('reviewed_at', '<=', $request->date_to);
        }

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $submissions = $query->orderBy('reviewed_at', 'desc')->paginate(20);

        // Get reviewers for filter
        $reviewers = User::where('role', 'admin')->get();

        $stats = [
            'total_reviewed' => HkiSubmission::whereNotNull('reviewed_at')->count(),
            'approved_count' => HkiSubmission::where('status', 'approved')->count(),
            'rejected_count' => HkiSubmission::where('status', 'rejected')->count(),
            'my_reviews' => HkiSubmission::where('reviewer_id', Auth::id())->whereNotNull('reviewed_at')->count(),
        ];

        return view('admin.review-history.index', compact('submissions', 'reviewers', 'stats'));
    }

    /**
     * Export Review History
     */
    public function exportReviewHistory(Request $request)
    {
        return Excel::download(new ReviewHistoryExport($request->all()), 
            'review-history-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * ✅ ADDED: Generate document template for approved submission
     */
    public function generateTemplate(Request $request, HkiSubmission $submission)
    {
        $request->validate([
            'template_type' => 'required|in:surat_ktp,surat_pengalihan,surat_pernyataan'
        ]);

        if ($submission->status !== 'approved') {
            return back()->withErrors(['error' => 'Template hanya dapat dibuat untuk submission yang sudah approved.']);
        }

        try {
            $templateType = $request->template_type;
            $templateData = $this->prepareTemplateData($submission);
            
            switch ($templateType) {
                case 'surat_ktp':
                    $filePath = $this->generateSuratKtpTemplate($submission, $templateData);
                    break;
                case 'surat_pengalihan':
                    $filePath = $this->generateSuratPengalihanTemplate($submission, $templateData);
                    break;
                case 'surat_pernyataan':
                    $filePath = $this->generateSuratPernyataanTemplate($submission, $templateData);
                    break;
                default:
                    return back()->withErrors(['error' => 'Template tidak valid.']);
            }

            return response()->download($filePath)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Template generation failed: ' . $e->getMessage(), [
                'submission_id' => $submission->id,
                'template_type' => $templateType ?? 'unknown'
            ]);
            return back()->withErrors(['error' => 'Terjadi kesalahan saat membuat template: ' . $e->getMessage()]);
        }
    }

    /**
     * ✅ ADDED: Prepare template data from submission
     */
    private function prepareTemplateData(HkiSubmission $submission)
    {
        // ✅ UPDATED: Sort members dengan leader first, kemudian by position
        $members = $submission->members->sortBy(function($member) {
            return $member->is_leader ? 0 : $member->position;
        })->values(); // Reset array keys
        
        $leader = $members->first(); // Leader is always first after sorting
        
        return [
            'submission_id' => str_pad($submission->id, 4, '0', STR_PAD_LEFT),
            'title' => $submission->title,
            'description' => $submission->description,
            'type' => ucfirst($submission->type),
            'creation_type' => ucfirst(str_replace('_', ' ', $submission->creation_type)),
            'user_name' => $submission->user->nama,
            'user_nidn' => $submission->user->nidn,
            'user_email' => $submission->user->email,
            'user_program_studi' => $submission->user->program_studi,
            'user_department' => $submission->user->department->name ?? 'N/A',
            'member_count' => $submission->member_count,
            'leader_name' => $leader ? $leader->name : $submission->user->nama,
            'leader_email' => $leader ? $leader->email : $submission->user->email,
            'leader_whatsapp' => $leader ? $leader->whatsapp : '',

            // ✅ ADDED: Alamat data untuk surat pengalihan
            'alamat' => $submission->alamat ?: '',
            'kode_pos' => $submission->kode_pos ?: '',
            'alamat_lengkap' => $this->getFormattedAlamat($submission),
            
            'submission_date' => $submission->submission_date->format('d M Y'),
            'reviewed_at' => $submission->reviewed_at->format('d M Y'),
            'current_date' => now()->format('d M Y'),
            'current_year' => now()->format('Y'),
            'members_list' => $members->map(function($member, $index) {
                return [
                    'no' => $index + 1,
                    'name' => $member->name,
                    'email' => $member->email,
                    'whatsapp' => $member->whatsapp,
                    'role' => $index === 0 ? 'Ketua Tim/Pencipta Utama' : "Anggota Pencipta " . ($index + 1),
                    'position_number' => $index + 1
                ];
            })->toArray()
        ];
    }

    /**
     * ✅ UPDATED: Generate Surat KTP Template dengan urutan pencipta yang benar
     */
    private function generateSuratKtpTemplate(HkiSubmission $submission, $templateData)
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        
        // ✅ Define table style tanpa border
        $phpWord->addTableStyle('CleanTable', [
            'borderSize' => null,
            'borderColor' => 'ffffff',
            'cellMargin' => 100,
            'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
        ], [
            'borderSize' => null,
            'borderColor' => 'ffffff',
        ]);

        // Set page orientation to landscape for better layout
        $section = $phpWord->addSection([
            'orientation' => 'landscape',
            'marginLeft' => 720,
            'marginRight' => 720,
            'marginTop' => 720,
            'marginBottom' => 720,
        ]);

        $headerTable = $section->addTable('CleanTable');

        // ✅ UPDATED: Ambil members dengan urutan yang benar (leader first, then by position)
        $allMembers = $submission->members->sortBy(function($member) {
            // Leader selalu di posisi pertama (0), anggota lain berdasarkan position
            return $member->is_leader ? 0 : $member->position;
        })->values(); // Reset array keys

        // Header dengan info submission
        $headerTable = $section->addTable([
            'width' => 100 * 50,
            'borderSize' => null,
        ]);
        $headerTable->addRow();

        $section->addTextBreak(2);

        // ✅ UPDATED: Layout dengan urutan yang benar
        // Pencipta Utama (Pencipta 1) di atas
        $this->addPenciptaUtama($section, $allMembers->first());
        
        // Add spacing
        $section->addTextBreak(1);
        
        // Pencipta 2-5 dalam format 2x2 grid
        $this->addPenciptaGrid($section, $allMembers->slice(1)); // Skip first member (leader)

        // Footer
        $section->addTextBreak(2);
        $section->addText('Generated by SiHaki System - ' . now()->format('d M Y H:i'), 
            ['size' => 8, 'italic' => true, 'color' => '666666'], 
            ['alignment' => 'center']
        );

        // Save file
        $fileName = 'Layout_KTP_' . $templateData['submission_id'] . '_' . time() . '.docx';
        $filePath = storage_path('app/temp/' . $fileName);
        
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($filePath);
        
        return $filePath;
    }


    /**
     * ✅ NEW: Helper method untuk format alamat lengkap
     */
    private function getFormattedAlamat(HkiSubmission $submission)
    {
        // Jika alamat dan kode pos ada, gabungkan
        if ($submission->alamat && $submission->kode_pos) {
            return $submission->alamat . ' ' . $submission->kode_pos;
        }
        
        // Jika hanya alamat ada
        if ($submission->alamat) {
            return $submission->alamat;
        }
        
        // Jika hanya kode pos ada (unlikely)
        if ($submission->kode_pos) {
            return 'Kode Pos: ' . $submission->kode_pos;
        }
        
        // Fallback jika tidak ada data alamat
        return '_____________________________________';
    }

    /**
     * ✅ UPDATED: Add Pencipta Utama (Pencipta 1) section
     */
    private function addPenciptaUtama($section, $pencipta1)
    {
        // Container table untuk pencipta utama
        $ketuaTable = $section->addTable([
            'borderSize' => null, 
            'borderColor' => 'ffffff',
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);
        
        $ketuaTable->addRow();
        $ketuaCell = $ketuaTable->addCell(12000, [
            'alignment' => 'center',
            'bgColor' => 'f8f9fa',
            'borderSize' => null
        ]);
        
        // ✅ UPDATED: Title yang konsisten
        $ketuaCell->addText('KTP Pencipta Utama', 
            ['bold' => true, 'size' => 14], 
            ['alignment' => 'center']);
        
        if ($pencipta1) {
            $ketuaCell->addText($pencipta1->name, 
                ['bold' => true, 'size' => 12], 
                ['alignment' => 'center']);
            $ketuaCell->addTextBreak(1);
            
            // Insert KTP image atau placeholder
            $this->insertKtpImageOrPlaceholder($ketuaCell, $pencipta1, 400, 250);
            
        } else {
            $ketuaCell->addTextBreak(1);
            $ketuaCell->addText('[ FOTO KTP ]', 
                ['bold' => true, 'size' => 20, 'color' => 'cccccc'], 
                ['alignment' => 'center']);
            $ketuaCell->addText('Data pencipta utama tidak tersedia', 
                ['italic' => true, 'color' => 'red'], 
                ['alignment' => 'center']);
        }
    }

    /**
     * ✅ UPDATED: Add Pencipta 2-5 dalam grid 2x2
     */
    private function addPenciptaGrid($section, $anggotaCollection)
    {
        $anggota = $anggotaCollection->values()->all(); // Reset keys
        
        // Buat grid 2x2 untuk pencipta 2-5 (4 slot)
        for ($row = 0; $row < 2; $row++) {
            $gridTable = $section->addTable([
                'borderSize' => null,
                'borderColor' => 'ffffff',
                'width' => 100 * 50,
                'unit' => 'pct'
            ]);
            
            $gridTable->addRow();
            
            // Kolom kiri
            $leftIndex = ($row * 2);
            $leftCell = $gridTable->addCell(6000, [
                'alignment' => 'center',
                'borderSize' => null,
            ]);
            
            if (isset($anggota[$leftIndex])) {
                // ✅ UPDATED: Pencipta nomor dimulai dari 2 (karena pencipta 1 sudah di atas)
                $this->addPenciptaToCell($leftCell, $anggota[$leftIndex], $leftIndex + 2);
            } else {
                $this->addEmptyPenciptaCell($leftCell, $leftIndex + 2);
            }
            
            // Kolom kanan
            $rightIndex = ($row * 2) + 1;
            $rightCell = $gridTable->addCell(6000, [
                'alignment' => 'center',
                'borderSize'=> null,
            ]);
            
            if (isset($anggota[$rightIndex])) {
                // ✅ UPDATED: Pencipta nomor dimulai dari 2 (karena pencipta 1 sudah di atas)
                $this->addPenciptaToCell($rightCell, $anggota[$rightIndex], $rightIndex + 2);
            } else {
                $this->addEmptyPenciptaCell($rightCell, $rightIndex + 2);
            }
            
            // Add spacing between rows
            if ($row < 1) {
                $section->addTextBreak(1);
            }
        }
    }

    /**
     * ✅ UPDATED: Add pencipta to table cell dengan nomor yang benar
     */
    private function addPenciptaToCell($cell, $member, $number)
    {
        // ✅ UPDATED: Label yang konsisten dengan urutan pencipta
        $cell->addText("KTP Pencipta {$number}", 
            ['bold' => true, 'size' => 12], 
            ['alignment' => 'center']);
    
        $cell->addText($member->name, 
            ['bold' => true, 'size' => 10], 
            ['alignment' => 'center']);
    
        $cell->addTextBreak(1);
        
        // Insert KTP image atau placeholder
        $this->insertKtpImageOrPlaceholder($cell, $member, 300, 180);
    }

    /**
     * ✅ UPDATED: Add empty pencipta cell dengan nomor yang benar
     */
    private function addEmptyPenciptaCell($cell, $number)
    {
        // ✅ UPDATED: Label yang konsisten dengan urutan pencipta
        $cell->addText("KTP Pencipta {$number}", 
            ['bold' => true, 'size' => 12], 
            ['alignment' => 'center']);
    
        $cell->addTextBreak(2);
    
        $cell->addText('[ FOTO KTP ]', 
            ['bold' => true, 'size' => 16, 'color' => 'cccccc'], 
            ['alignment' => 'center']);
    
        $cell->addTextBreak(1);
    
        $cell->addText('Tidak ada pencipta', 
            ['italic' => true, 'size' => 10, 'color' => '999999'], 
            ['alignment' => 'center']);
    }

    /**
     * ✅ NEW: Insert KTP image atau placeholder dengan error handling
     */
    private function insertKtpImageOrPlaceholder($cell, $member, $width = 300, $height = 200)
    {
        try {
            if (!$member->ktp) {
                $this->addKtpPlaceholderToCell($cell, 'KTP BELUM DIUPLOAD', $width, $height);
                return;
            }

            $ktpPath = storage_path('app/public/' . $member->ktp);
            
            if (!file_exists($ktpPath)) {
                Log::warning('KTP file not found for member', [
                    'member_id' => $member->id,
                    'member_name' => $member->name,
                    'ktp_path' => $ktpPath
                ]);
                $this->addKtpPlaceholderToCell($cell, 'FILE KTP TIDAK DITEMUKAN', $width, $height);
                return;
            }

            // Process dan insert image
            $processedImagePath = $this->processKtpImage($ktpPath, $member->id);
            
            // Insert actual KTP image
            $cell->addImage($processedImagePath, [
                'width' => $width * 0.75, // Convert points to pixels roughly
                'height' => $height * 0.75,
                'alignment' => 'center'
            ]);
            
            // Add member info below image
            $cell->addTextBreak(1);
            $this->addMemberInfoToCell($cell, $member);
            
            Log::info('KTP successfully inserted for member', [
                'member_id' => $member->id,
                'member_name' => $member->name
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to insert KTP for member', [
                'member_id' => $member->id,
                'member_name' => $member->name,
                'error' => $e->getMessage()
            ]);
            
            $this->addKtpPlaceholderToCell($cell, 'ERROR: ' . $e->getMessage(), $width, $height);
        }
    }

    /**
     * ✅ NEW: Add KTP placeholder to cell
     */
    private function addKtpPlaceholderToCell($cell, $message, $width, $height)
    {
        // Create bordered placeholder
        $placeholderTable = $cell->addTable([
            'borderSize' => null,
            'borderColor' => 'ffffff',
            'width' => $width,
        ]);
        
        $placeholderTable->addRow($height);
        $placeholderCell = $placeholderTable->addCell($width, [
            'bgColor' => 'f8f9fa',
            'alignment' => 'center',
            'valign' => 'center',
            'borderSize' => null,
        ]);
        
        $placeholderCell->addText('[ FOTO KTP ]', 
            ['bold' => true, 'size' => 16, 'color' => 'cccccc'], 
            ['alignment' => 'center']);
        
        $placeholderCell->addTextBreak(1);
        
        $placeholderCell->addText($message, 
            ['bold' => true, 'size' => 10, 'color' => 'red'], 
            ['alignment' => 'center']);
    }

    /**
     * ✅ NEW: Add member info to cell
     */
    private function addMemberInfoToCell($cell, $member)
    {
        $infoTable = $cell->addTable([
            'borderSize' => null,
            'borderColor' => '999999',
        ]);
        
        $memberInfo = [
            ['Email', $member->email],
            ['WhatsApp', $member->whatsapp],
        ];
    }

    /**
     * ✅ UPDATED: Process KTP image dengan better quality
     */
    private function processKtpImage($ktpPath, $memberId)
    {
        try {
            $tempDir = storage_path('app/temp/processed_ktp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $processedPath = $tempDir . '/ktp_' . $memberId . '_' . time() . '.jpg';

            // Check if Intervention Image is available
            if (class_exists('\Intervention\Image\ImageManagerStatic')) {
                $image = \Intervention\Image\ImageManagerStatic::make($ktpPath);
                
                // Resize dengan maintain aspect ratio
                $image->resize(800, 500, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                
                // Enhance untuk readability KTP
                $image->sharpen(15);
                $image->contrast(8);
                $image->brightness(5);
                
                $image->save($processedPath, 95); // High quality
                
                return $processedPath;
            } else {
                // Fallback: copy original
                copy($ktpPath, $processedPath);
                return $processedPath;
            }

        } catch (\Exception $e) {
            Log::warning('Image processing failed, using original', [
                'error' => $e->getMessage(),
                'original_path' => $ktpPath
            ]);
            
            return $ktpPath;
        }
    }

    /**
     * ✅ UPDATED: Generate Surat Pengalihan Template sesuai format yang diminta
     */
    private function generateSuratPengalihanTemplate(HkiSubmission $submission, $templateData)
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        
        // Set page setup
        $section = $phpWord->addSection([
            'orientation' => 'portrait',
            'marginLeft' => 720,   // 1 inch
            'marginRight' => 720,
            'marginTop' => 720,
            'marginBottom' => 720,
        ]);

        // Define styles
        $titleStyle = ['bold' => true, 'size' => 14, 'name' => 'Times New Roman'];
        $headerStyle = ['bold' => true, 'size' => 12, 'name' => 'Times New Roman'];
        $textStyle = ['size' => 11, 'name' => 'Times New Roman'];
        $centerAlign = ['alignment' => 'center'];
        $justifyAlign = ['alignment' => 'both', 'lineHeight' => 1.5];

        // ✅ TITLE
        $section->addText('SURAT PENGALIHAN HAK CIPTA', $titleStyle, $centerAlign);
        $section->addTextBreak(2);

        // ✅ OPENING
        $section->addText('Yang bertanda tangan di bawah ini :', $textStyle, $justifyAlign);
        $section->addTextBreak(1);

        // ✅ PIHAK I (PENCIPTA)
        $pihak1Table = $section->addTable([
            'borderSize' => null,
            'width' => 100 * 50,
        ]);
        
        $pihak1Table->addRow();
        $pihak1Table->addCell(2000)->addText('N a m a', $textStyle);
        $pihak1Table->addCell(300)->addText(':', $textStyle);
        $pihak1Table->addCell(6000)->addText($templateData['leader_name'], $textStyle);
        
        $pihak1Table->addRow();
        $pihak1Table->addCell(2000)->addText('Alamat', $textStyle);
        $pihak1Table->addCell(300)->addText(':', $textStyle);
        // ✅ UPDATED: Alamat diisi otomatis dari data submission
        $alamatLengkap = $this->getFormattedAlamat($submission);
        $pihak1Table->addCell(6000)->addText($alamatLengkap, $textStyle);

        $section->addTextBreak(1);

        // ✅ PENJELASAN PIHAK I
        $section->addText('Adalah Pihak I selaku pencipta, dengan ini menyerahkan karya ciptaan saya kepada :', 
            $textStyle, $justifyAlign);
        $section->addTextBreak(1);

        // ✅ PIHAK II (INSTITUSI)
        $pihak2Table = $section->addTable([
            'borderSize' => null,
            'width' => 100 * 50,
        ]);
        
        $pihak2Table->addRow();
        $pihak2Table->addCell(2000)->addText('N a m a', $textStyle);
        $pihak2Table->addCell(300)->addText(':', $textStyle);
        $pihak2Table->addCell(6000)->addText('STMIK AMIKOM Surakarta', $textStyle);
        
        $pihak2Table->addRow();
        $pihak2Table->addCell(2000)->addText('Alamat', $textStyle);
        $pihak2Table->addCell(300)->addText(':', $textStyle);
        $pihak2Table->addCell(6000)->addText('Jl. Veteran Notosuman Singopuran Kartasura Sukoharjo 57164', $textStyle);

        $section->addTextBreak(1);

        // ✅ PENJELASAN KARYA
        $karyaText = 'Adalah Pihak II selaku Pemegang Hak Cipta berupa karya Jenis Ciptaan ' . 
                    ucfirst(str_replace('_', ' ', $templateData['creation_type'])) . 
                    ' yang berjudul "' . $templateData['title'] . 
                    '" untuk didaftarkan di Direktorat Hak Cipta dan Desain Industri, ' .
                    'Direktorat Jenderal Kekayaan Intelektual, Kementerian Hukum dan Hak Asasi Manusia Republik Indonesia.';
        
        $section->addText($karyaText, $textStyle, $justifyAlign);
        $section->addTextBreak(1);

        // ✅ PENUTUP
        $section->addText('Demikianlah surat pengalihan hak ini kami buat, agar dapat dipergunakan sebagaimana mestinya.', 
            $textStyle, $justifyAlign);
        $section->addTextBreak(3);

        // ✅ TANGGAL DAN TANDA TANGAN
        $section->addText('Sukoharjo, ' . $templateData['current_date'], $textStyle, 
            ['alignment' => 'right']);
        $section->addTextBreak(2);

        // ✅ TABEL TANDA TANGAN
        $ttdTable = $section->addTable([
            'borderSize' => null,
            'width' => 100 * 50,
        ]);
        
        // Header tanda tangan
        $ttdTable->addRow();
        $leftTtdCell = $ttdTable->addCell(4500, ['alignment' => 'center']);
        $leftTtdCell->addText('Pemegang Hak Cipta', $textStyle, $centerAlign);
        
        $rightTtdCell = $ttdTable->addCell(4500, ['alignment' => 'center']);
        $rightTtdCell->addText('Pencipta', $textStyle, $centerAlign);

        // Space untuk materai
        $ttdTable->addRow();
        
        $leftTtdCell2 = $ttdTable->addCell(4500, ['alignment' => 'center']);
        $leftTtdCell2->addTextBreak(4); // Space untuk tanda tangan
        
        $rightTtdCell2 = $ttdTable->addCell(4500, ['alignment' => 'center']);
        $rightTtdCell2->addTextBreak(1);
        $rightTtdCell2->addText('Materai 10.000', ['size' => 10, 'italic' => true], $centerAlign);
        $rightTtdCell2->addTextBreak(2);

        // Nama penandatangan
        $ttdTable->addRow();
        $leftTtdCell3 = $ttdTable->addCell(4500, ['alignment' => 'center']);
        $leftTtdCell3->addText('(Moch. Hari Purwidiantoro, S.T., M.M., M.Kom.)', $textStyle, $centerAlign);
        

        $rightTtdCell3 = $ttdTable->addCell(4500, ['alignment' => 'center']);
        $rightTtdCell3->addText('(' . $templateData['leader_name'] . ')', $textStyle, $centerAlign);

        // Save file
        $fileName = 'Surat_Pengalihan_' . $templateData['submission_id'] . '_' . time() . '.docx';
        $filePath = storage_path('app/temp/' . $fileName);
        
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($filePath);
        
        return $filePath;
    }

    /**
     * ✅ UPDATED: Generate Surat Pernyataan Template sesuai format resmi
     */
    private function generateSuratPernyataanTemplate(HkiSubmission $submission, $templateData)
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        
        // Set page setup
        $section = $phpWord->addSection([
            'orientation' => 'portrait',
            'marginLeft' => 720,
            'marginRight' => 720,
            'marginTop' => 720,
            'marginBottom' => 720,
        ]);

        // Define styles
        $titleStyle = ['bold' => true, 'size' => 14, 'name' => 'Times New Roman'];
        $headerStyle = ['bold' => true, 'size' => 12, 'name' => 'Times New Roman'];
        $textStyle = ['size' => 11, 'name' => 'Times New Roman'];
        $centerAlign = ['alignment' => 'center'];
        $justifyAlign = ['alignment' => 'both', 'lineHeight' => 1.5];

        // ✅ TITLE
        $section->addText('SURAT PERNYATAAN', $titleStyle, $centerAlign);
        $section->addTextBreak(2);

        // ✅ OPENING
        $section->addText('Yang bertanda tangan dibawah ini, pemegang hak cipta:', $textStyle, $justifyAlign);
        $section->addTextBreak(1);

        // ✅ DATA PEMEGANG HAK CIPTA (INSTITUSI)
        $dataTable = $section->addTable([
            'borderSize' => null,
            'width' => 100 * 50,
        ]);
        
        $dataTable->addRow();
        $dataTable->addCell(2000)->addText('N a m a', $textStyle);
        $dataTable->addCell(300)->addText(':', $textStyle);
        $dataTable->addCell(6000)->addText('Sekolah Tinggi Manajemen Informatika dan Komputer AMIKOM Surakarta', $textStyle);
        
        $dataTable->addRow();
        $dataTable->addCell(2000)->addText('Alamat', $textStyle);
        $dataTable->addCell(300)->addText(':', $textStyle);
        $dataTable->addCell(6000)->addText('Jl. Veteran Notosuman Singopuran Kartasura Sukoharjo 57164', $textStyle);

        $section->addTextBreak(2);

        // ✅ PERNYATAAN UTAMA
        $section->addText('Dengan ini menyatakan bahwa:', $textStyle, $justifyAlign);
        $section->addTextBreak(1);

        // ✅ POIN 1 - KARYA CIPTA
        $section->addText('1. Karya Cipta yang saya mohonkan:', $textStyle, $justifyAlign);

        // ✅ DATA KARYA CIPTA
        $karyaTable = $section->addTable([
            'borderSize' => null,
            'width' => 100 * 50,
        ]);
        
        $karyaTable->addRow();
        $karyaTable->addCell(2000)->addText('Berupa', $textStyle);
        $karyaTable->addCell(300)->addText(':', $textStyle);
        $karyaTable->addCell(6000)->addText(ucfirst(str_replace('_', ' ', $templateData['creation_type'])), $textStyle);
        
        $karyaTable->addRow();
        $karyaTable->addCell(2000)->addText('Berjudul', $textStyle);
        $karyaTable->addCell(300)->addText(':', $textStyle);
        $karyaTable->addCell(6000)->addText($templateData['title'], $textStyle);

        $section->addTextBreak(1);

        // ✅ POIN-POIN PERNYATAAN SESUAI UU HAK CIPTA
        $poinPernyataan = [
            'Tidak meniru dan tidak sama secara esensial dengan Karya Cipta milik pihak lain atau obyek kekayaan intelektual lainnya sebagaimana dimaksud dalam Pasal 68 ayat (2);',
            'Bukan merupakan Ekspresi Budaya Tradisional sebagaimana dimaksud dalam Pasal 38;',
            'Bukan merupakan Ciptaan yang tidak diketahui penciptanya sebagaimana dimaksud dalam Pasal 39;',
            'Bukan merupakan hasil karya yang tidak dilindungi Hak Cipta sebagaimana dimaksud dalam Pasal 41 dan 42;',
            'Bukan merupakan Ciptaan seni lukis yang berupa logo atau tanda pembeda yang digunakan sebagai merek dalam perdagangan barang/jasa atau digunakan sebagai lambang organisasi, badan usaha, atau badan hukum sebagaimana dimaksud dalam Pasal 65 dan;',
            'Bukan merupakan Ciptaan yang melanggar norma agama, norma susila, ketertiban umum, pertahanan dan keamanan negara atau melanggar peraturan perundang-undangan sebagaimana dimaksud dalam Pasal 74 ayat (1) huruf d Undang-Undang Nomor 28 Tahun 2014 tentang Hak Cipta.'
        ];

        // Create list dengan bullet points menggunakan tab
        foreach ($poinPernyataan as $poin) {
            $section->addText('•	' . $poin, $textStyle, $justifyAlign);
        }

        $section->addTextBreak(1);

        // ✅ POIN 2 - KEWAJIBAN MENYIMPAN
        $section->addText('2. Sebagai pemohon mempunyai kewajiban untuk menyimpan asli contoh ciptaan yang dimohonkan dan harus memberikan apabila dibutuhkan untuk kepentingan penyelesaian sengketa perdata maupun pidana sesuai dengan ketentuan perundang-undangan.', $textStyle, $justifyAlign);
        $section->addTextBreak(1);

        // ✅ POIN 3 - TIDAK DALAM SENGKETA
        $section->addText('3. Karya Cipta yang saya mohonkan pada Angka 1 tersebut di atas tidak pernah dan tidak sedang dalam sengketa pidana dan/atau perdata di Pengadilan.', $textStyle, $justifyAlign);

        $section->addTextBreak(1);

        // ✅ POIN 4 - KONSEKUENSI PELANGGARAN
        $section->addText('4. Dalam hal ketentuan sebagaimana dimaksud dalam Angka 1 dan Angka 3 tersebut di atas saya / kami langgar, maka saya / kami bersedia secara sukarela bahwa:', $textStyle, $justifyAlign);
        
        $konsekuensiList = [
            'permohonan karya cipta yang saya ajukan dianggap ditarik kembali; atau',
            'Karya Cipta yang telah terdaftar dalam Daftar Umum Ciptaan Direktorat Hak Cipta, Direktorat Jenderal Hak Kekayaan Intelektual, Kementerian Hukum Dan Hak Asasi Manusia R.I dihapuskan sesuai dengan ketentuan perundang-undangan yang berlaku.',
            'Dalam hal kepemilikan Hak Cipta yang dimohonkan secara elektronik sedang dalam berperkara dan/atau sedang dalam gugatan di Pengadilan maka status kepemilikan surat pencatatan elektronik tersebut ditangguhkan menunggu putusan Pengadilan yang berkekuatan hukum tetap.'
        ];

        foreach ($konsekuensiList as $index => $konsekuensi) {
            $huruf = chr(97 + $index); // a, b, c
            $section->addText($huruf . '.	' . $konsekuensi, $textStyle, $justifyAlign);
        }

        $section->addTextBreak(2);

        // ✅ PENUTUP
        $section->addText('Demikian Surat pernyataan ini saya/kami buat dengan sebenarnya dan untuk dipergunakan sebagimana mestinya.', 
            $textStyle, $justifyAlign);
        $section->addTextBreak(3);

        // ✅ TANGGAL DAN TANDA TANGAN
        $section->addText('Sukoharjo, ' . $templateData['current_date'], $textStyle, 
            ['alignment' => 'right']);
        $section->addTextBreak(6); // Space untuk tanda tangan
        
        $section->addText('(Moch. Hari Purwidiantoro, S.T., M.M., M.Kom.)', $textStyle, ['alignment' => 'right']);
        $section->addText('Ketua STMIK AMIKOM Surakarta', $textStyle, ['alignment' => 'right']);
        $section->addText('Pemegang Hak Cipta', $textStyle, ['alignment' => 'right']);

        // Save file
        $fileName = 'Surat_Pernyataan_' . $templateData['submission_id'] . '_' . time() . '.docx';
        $filePath = storage_path('app/temp/' . $fileName);
        
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($filePath);
        
        return $filePath;
    }
}