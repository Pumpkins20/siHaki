<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\HkiSubmission;
use App\Models\Department;
use App\Models\SubmissionHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Exports\SubmissionsExport;
use Maatwebsite\Excel\Facades\Excel;

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
     * Assign submission to current admin for review
     */
    public function assignToSelf(HkiSubmission $submission)
    {
        if ($submission->status !== 'submitted') {
            return back()->withErrors(['error' => 'Submission ini tidak dapat di-assign karena statusnya sudah berubah.']);
        }

        $submission->update([
            'status' => 'under_review',
            'reviewer_id' => Auth::id(),
            'assigned_at' => now()
        ]);

        // Create history
        $submission->histories()->create([
            'user_id' => Auth::id(),
            'action' => 'Assigned for Review',
            'notes' => 'Submission di-assign ke ' . Auth::user()->nama . ' untuk review',
            'status' => 'under_review'
        ]);

        return back()->with('success', 'Submission berhasil di-assign untuk Anda review.');
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

        $submission->update([
            'status' => 'approved',
            'review_notes' => $request->review_notes,
            'reviewed_at' => now()
        ]);

        // Create history
        $submission->histories()->create([
            'user_id' => Auth::id(),
            'action' => 'Approved',
            'notes' => $request->review_notes,
            'status' => 'approved'
        ]);

        // TODO: Send notification email to user

        return redirect()->route('admin.submissions.index')
            ->with('success', "Submission '{$submission->title}' berhasil diapprove.");
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

        $submission->update([
            'status' => 'revision_needed',
            'review_notes' => $request->review_notes,
            'reviewed_at' => now()
        ]);

        // Create history
        $submission->histories()->create([
            'user_id' => Auth::id(),
            'action' => 'Revision Requested',
            'notes' => $request->review_notes,
            'status' => 'revision_needed'
        ]);

        // TODO: Send notification email to user

        return redirect()->route('admin.submissions.index')
            ->with('success', "Revision berhasil di-request untuk submission '{$submission->title}'.");
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

        $submission->update([
            'status' => 'rejected',
            'review_notes' => $request->review_notes,
            'reviewed_at' => now()
        ]);

        // Create history
        $submission->histories()->create([
            'user_id' => Auth::id(),
            'action' => 'Rejected',
            'notes' => $request->review_notes,
            'status' => 'rejected'
        ]);

        // TODO: Send notification email to user

        return redirect()->route('admin.submissions.index')
            ->with('success', "Submission '{$submission->title}' berhasil ditolak.");
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

        $filePath = storage_path('app/private/submissions/' . $document->file_path);
        
        if (!file_exists($filePath)) {
            return back()->withErrors(['error' => 'File tidak ditemukan.']);
        }

        return response()->download($filePath, $document->file_name);
    }

    /**
     * Preview submission document
     */
    public function previewDocument(HkiSubmission $submission, SubmissionDocument $document)
    {
        // Verify document belongs to submission
        if ($document->submission_id !== $submission->id) {
            abort(404);
        }

        $filePath = storage_path('app/private/submissions/' . $document->file_path);
        
        if (!file_exists($filePath)) {
            abort(404);
        }

        $mimeType = mime_content_type($filePath);
        
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $document->file_name . '"'
        ]);
    }

    /**
     * View member KTP
     */
    public function viewMemberKtp(HkiSubmission $submission, SubmissionMember $member)
    {
        // Verify member belongs to submission
        if ($member->submission_id !== $submission->id) {
            abort(404);
        }

        if (!$member->ktp) {
            abort(404, 'KTP tidak tersedia');
        }

        $filePath = storage_path('app/private/ktp/' . $member->ktp);
        
        if (!file_exists($filePath)) {
            abort(404);
        }

        $mimeType = mime_content_type($filePath);
        
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="KTP_' . $member->name . '"'
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
}