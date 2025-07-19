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

class AdminController extends Controller
{

    // ================= DASHBOARD ================
    public function dashboard()
    {
        $stats = [
            'total_users' => User::where('role','user')->count(),
            'total_submissions' => HkiSubmission::count(),
            'pending_reviews' => HkiSubmission::where('status','submitted')->count(),
            'under_review' => HkiSubmission::where('status','under_review')->count(),
            'approved_today' => HkiSubmission::where('status','approved')
                ->whereDate('reviewed_at', today())->count(),
            'my_reviews' => HkiSubmission::where('reviewer_id', Auth::id())->count()
        ];

        // Recent submissions for review
        $recent_submissions = HkiSubmission::with(['user'])
            ->whereIn('status', ['submitted', 'under_review'])
            ->latest()
            ->limit(5)
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

    public function storeUser(request $request)
    {
        $request->validate([
            'nidn' => 'required|unique:users',
            'nama' => 'required|string|max:255',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'program_studi' => 'required|in:D3 Manajemen Informatika,S1 Informatika,S1 Sistem Informasi,S1 Teknologi Informasi',
            'department_id' => 'required|exists:departments,id',
            'phone' => 'nullable|string',
        ]);

        // ✅ NEW: Create user with NIDN as default password
        User::create([
            'nidn' => $request->nidn,
            'nama' => $request->nama,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->nidn), // ✅ Password = NIDN
            'program_studi' => $request->program_studi,
            'foto' => 'default.png',
            'role' => 'user', // Always create as user/dosen
            'phone' => $request->phone,
            'department_id' => $request->department_id,
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('Success', 'User berhasil ditambahkan dengan password default: '. $request->nidn);
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
            'total' => HkiSubmission::count(),
            'need_review' => HkiSubmission::where('status', 'submitted')->count(),
            'under_review' => HkiSubmission::where('status', 'under_review')->count(),
            'completed' => HkiSubmission::whereIn('status', ['approved', 'rejected'])->count(),
        ];

        $query = HkiSubmission::with(['user', 'reviewer']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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
            if ($request->assignment === 'unassigned') {
                $query->whereNull('reviewer_id');
            } elseif ($request->assignment === 'my_reviews') {
                $query->where('reviewer_id', Auth::id());
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
}