<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\HkiSubmission;
use App\Models\User;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ✅ UPDATED: Statistics with new categories
        $stats = [
            'total_submissions' => $user->submissions()->count(),
            'approved_submissions' => $user->submissions()->where('status', 'approved')->count(),
            'pending_submissions' => $user->submissions()->whereIn('status', ['submitted', 'under_review'])->count(),
            'rejected_submissions' => $user->submissions()->where('status', 'rejected')->count(),
            'revision_submissions' => $user->submissions()->where('status', 'revision_needed')->count(),
            // ✅ NEW: Certificate received (approved submissions that have certificate)
            'certificate_received' => $user->submissions()
                ->where('status', 'approved')
                ->whereNotNull('certificate_path') // Assuming you have certificate_path column
                ->count(),
        ];

        // Progress calculation (approved / total)
        $totalSubmissions = $stats['total_submissions'];
        $progress = $totalSubmissions > 0 ? ($stats['approved_submissions'] / $totalSubmissions) * 100 : 0;

        // Recent submissions
        $recent_submissions = $user->submissions()
            ->latest()
            ->limit(5)
            ->get();

        // Sample reminders and notifications
        $reminders = collect([]);
        $notifications = collect([]);

        return view('user.dashboard', compact(
            'stats', 
            'progress', 
            'recent_submissions', 
            'reminders', 
            'notifications'
        ));
    }

    // Profile methods
    public function profile()
    {
        $user = Auth::user();
        $user->load('department');
        
        return view('user.profile.index', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'nama' => $request->nama,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return back()->with('success', 'Profile berhasil diperbarui!');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,jpg,png|max:2048', // Max 2MB
        ]);

        $user = Auth::user();

        if ($request->hasFile('photo')) {
            // Delete old photo if exists (except default)
            if ($user->foto && $user->foto !== 'default.png') {
                Storage::disk('public')->delete('profile_photos/' . $user->foto);
            }

            $file = $request->file('photo');
            $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Create directory if not exists
            $directory = storage_path('app/public/profile_photos');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Save original file
            $path = $file->storeAs('profile_photos', $filename, 'public');

            // Create resized version (optional)
            try {
                $image = Image::make($file);
                $image->fit(300, 300, function ($constraint) {
                    $constraint->upsize();
                });
                $image->save(storage_path('app/public/profile_photos/' . $filename));
            } catch (\Exception $e) {
                // If Image intervention fails, just use the original
            }

            // Update user photo
            $user->update(['foto' => $filename]);

            return back()->with('success', 'Foto profile berhasil diperbarui!');
        }

        return back()->withErrors(['photo' => 'Gagal mengupload foto']);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Password saat ini harus diisi',
            'new_password.required' => 'Password baru harus diisi',
            'new_password.min' => 'Password baru minimal 6 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak sesuai',
        ]);

        $user = Auth::user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password berhasil diubah!');
    }
}