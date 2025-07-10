<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\HkiSubmission;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_submissions' => HkiSubmission::count(),
            'active_reviews' => HkiSubmission::where('status', 'under_review')->count(),
            'pending_submissions' => HkiSubmission::where('status', 'submitted')->count(),
        ];

        $recent_submissions = HkiSubmission::with(['user'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_submissions'));
    }

    // User Management
    public function users()
    {
        $users = User::with('department')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        $departments = Department::all();
        return view('admin.users.create', compact('departments'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'nidn' => 'required|unique:users',
            'nama' => 'required|string|max:255',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'program_studi' => 'required|in:D3 Manajemen Informatika,S1 Informatika,S1 Sistem Informasi,S1 Teknologi Informasi',
            'role' => 'required|in:admin,user,reviewer',
            'department_id' => 'required|exists:departments,id',
            'phone' => 'nullable|string',
        ]);

        User::create([
            'nidn' => $request->nidn,
            'nama' => $request->nama,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'program_studi' => $request->program_studi,
            'foto' => 'default.png',
            'role' => $request->role,
            'phone' => $request->phone,
            'department_id' => $request->department_id,
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    public function showUser(User $user)
    {
        $user->load(['department', 'submissions']);
        return view('admin.users.show', compact('user'));
    }

    public function editUser(User $user)
    {
        $departments = Department::all();
        return view('admin.users.edit', compact('user', 'departments'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'nidn' => 'required|unique:users,nidn,' . $user->id,
            'nama' => 'required|string|max:255',
            'username' => 'required|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'program_studi' => 'required|in:D3 Manajemen Informatika,S1 Informatika,S1 Sistem Informasi,S1 Teknologi Informasi',
            'role' => 'required|in:admin,user,reviewer',
            'department_id' => 'required|exists:departments,id',
            'phone' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data = $request->only([
            'nidn', 'nama', 'username', 'email', 'program_studi', 
            'role', 'phone', 'department_id', 'is_active'
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diupdate');
    }

    public function destroyUser(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus');
    }

    // Submission Management
    public function submissions()
    {
        $submissions = HkiSubmission::with(['user', 'reviewer'])
            ->latest()
            ->paginate(10);
        
        return view('admin.submissions.index', compact('submissions'));
    }

    public function showSubmission(HkiSubmission $submission)
    {
        $submission->load(['user', 'reviewer', 'documents', 'histories.user']);
        return view('admin.submissions.show', compact('submission'));
    }

    public function assignReviewer(Request $request, HkiSubmission $submission)
    {
        $request->validate([
            'reviewer_id' => 'required|exists:users,id',
        ]);

        $reviewer = User::where('id', $request->reviewer_id)
            ->where('role', 'reviewer')
            ->first();

        if (!$reviewer) {
            return back()->withErrors(['reviewer_id' => 'Reviewer tidak valid']);
        }

        $submission->update([
            'reviewer_id' => $request->reviewer_id,
            'status' => 'under_review',
        ]);

        return back()->with('success', 'Reviewer berhasil ditugaskan');
    }

    // Department Management
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

    // Reports
    public function reports()
    {
        $stats = [
            'total_submissions' => HkiSubmission::count(),
            'submissions_by_status' => HkiSubmission::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
            'submissions_by_type' => HkiSubmission::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type'),
            'submissions_by_month' => HkiSubmission::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->pluck('count', 'month'),
        ];

        return view('admin.reports.index', compact('stats'));
    }

    public function exportReport()
    {
        // Implementation for export report (Excel/PDF)
        return response()->download(storage_path('app/reports/submissions.xlsx'));
    }
}