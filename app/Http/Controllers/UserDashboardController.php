<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HkiSubmission;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $stats = [
            'total_submissions' => $user->submissions()->count(),
            'draft_submissions' => $user->submissions()->where('status', 'draft')->count(),
            'pending_submissions' => $user->submissions()->whereIn('status', ['submitted'])->count(),
            'review_submissions' => $user->submissions()->where('status', 'under_review')->count(),
            'revision_submissions' => $user->submissions()->where('status', 'revision_needed')->count(),
            'approved_submissions' => $user->submissions()->where('status', 'approved')->count(),
        ];

        // Calculate progress percentage
        $totalNonDraft = $stats['total_submissions'] - $stats['draft_submissions'];
        $progress = $totalNonDraft > 0 ? 
            round(($stats['approved_submissions'] / $totalNonDraft) * 100) : 0;

        $recent_submissions = $user->submissions()
            ->latest()
            ->limit(5)
            ->get();

        // Get submissions that need revision (reminders)
        $reminders = $user->submissions()
            ->where('status', 'revision_needed')
            ->latest()
            ->limit(3)
            ->get();

        // Get notifications (you'll need to create this model)
        $notifications = collect([
            (object)[
                'title' => 'Submission Approved',
                'message' => 'Pengajuan "Aplikasi Mobile" telah disetujui',
                'type' => 'success',
                'icon' => 'check-circle',
                'created_at' => now()->subHours(2)
            ],
            (object)[
                'title' => 'Revision Required',
                'message' => 'Dokumen perlu diperbaiki',
                'type' => 'warning',
                'icon' => 'exclamation-triangle',
                'created_at' => now()->subDays(1)
            ]
        ]);

        return view('user.dashboard', compact(
            'stats', 
            'progress', 
            'recent_submissions', 
            'reminders', 
            'notifications'
        ));
    }
}