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
            'pending_submissions' => $user->submissions()->whereIn('status', ['submitted', 'under_review'])->count(),
            'approved_submissions' => $user->submissions()->where('status', 'approved')->count(),
            'revision_needed' => $user->submissions()->where('status', 'revision_needed')->count(), // ✅ Add this
            'rejected_submissions' => $user->submissions()->where('status', 'rejected')->count(),
            // ✅ NEW: Certificate received (approved submissions that have certificate)
            'certificate_received' => $user->submissions()
                ->whereHas('documents', function($q) {
                    $q->where('document_type', 'certificate');
                })->count(),
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
        $notifications = $user->notifications()
                          ->orderBy('created_at', 'desc')
                          ->limit(5)
                          ->get()
                          ->map(function($notification) {
                              $data = $notification->data;
                              return (object) [
                                  'id' => $notification->id,
                                  'title' => $data['title'] ?? 'Notifikasi',
                                  'message' => $data['message'] ?? '',
                                  'type' => $data['type'] ?? 'info',
                                  'icon' => $data['icon'] ?? 'bell',
                                  'action_url' => $data['action_url'] ?? '#',
                                  'created_at' => $notification->created_at,
                                  'read_at' => $notification->read_at,
                              ];
                          });

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

            try {
                // ✅ ALTERNATIVE: Manual image processing with GD
                $this->processAndSaveImage($file, $filename);
                
                // Update user photo
                $user->update(['foto' => $filename]);

                return back()->with('success', 'Foto profile berhasil diperbarui!');

            } catch (\Exception $e) {
                Log::error('Photo upload failed', [
                    'error' => $e->getMessage(),
                    'user_id' => $user->id
                ]);

                return back()->withErrors(['photo' => 'Gagal memproses foto. Silakan coba lagi.']);
            }
        }

        return back()->withErrors(['photo' => 'Gagal mengupload foto']);
    }

    /**
     * ✅ NEW: Process image manually using GD library
     */
    private function processAndSaveImage($file, $filename)
    {
        $imagePath = $file->getPathname();
        $mimeType = $file->getMimeType();
        
        // Create image resource based on type
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                $sourceImage = imagecreatefromjpeg($imagePath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($imagePath);
                break;
            default:
                throw new \Exception('Unsupported image type');
        }
        
        if (!$sourceImage) {
            throw new \Exception('Failed to create image resource');
        }
        
        // Get original dimensions
        $originalWidth = imagesx($sourceImage);
        $originalHeight = imagesy($sourceImage);
        
        // Calculate dimensions for square crop
        $size = min($originalWidth, $originalHeight);
        $x = ($originalWidth - $size) / 2;
        $y = ($originalHeight - $size) / 2;
        
        // Create new image (300x300)
        $newImage = imagecreatetruecolor(300, 300);
        
        // Preserve transparency for PNG
        if ($mimeType === 'image/png') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefill($newImage, 0, 0, $transparent);
        }
        
        // Crop and resize
        imagecopyresampled(
            $newImage, $sourceImage,
            0, 0, $x, $y,
            300, 300, $size, $size
        );
        
        // Save the processed image
        $savePath = storage_path('app/public/profile_photos/' . $filename);
        
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                imagejpeg($newImage, $savePath, 90);
                break;
            case 'image/png':
                imagepng($newImage, $savePath, 8);
                break;
        }
        
        // Clean up memory
        imagedestroy($sourceImage);
        imagedestroy($newImage);
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