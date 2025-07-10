<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HkiSubmission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ReviewerDashboardController extends Controller
{
    public function index()
    {
        $reviewer = Auth::user();
        
        $stats = [
            'assigned_reviews' => HkiSubmission::where('reviewer_id', $reviewer->id)->count(),
            'pending_reviews' => HkiSubmission::where('reviewer_id', $reviewer->id)->where('status', 'under_review')->count(),
            'completed_reviews' => HkiSubmission::where('reviewer_id', $reviewer->id)->whereIn('status', ['approved', 'rejected'])->count(),
            'avg_review_time' => $this->calculateAverageReviewTime($reviewer->id),
        ];

        // Perbaikan: Hanya load relasi yang diperlukan
        $pending_reviews = HkiSubmission::where('reviewer_id', $reviewer->id)
            ->where('status', 'under_review')
            ->with(['user'])
            ->latest()
            ->limit(10)
            ->get();

        $recent_reviews = HkiSubmission::where('reviewer_id', $reviewer->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->with(['user'])
            ->latest()
            ->limit(5)
            ->get();

        return view('reviewer.dashboard', compact('stats', 'pending_reviews', 'recent_reviews'));
    }

    private function calculateAverageReviewTime($reviewerId)
    {
        $completedReviews = HkiSubmission::where('reviewer_id', $reviewerId)
            ->whereIn('status', ['approved', 'rejected'])
            ->whereNotNull('reviewed_at')
            ->whereNotNull('submission_date')
            ->get();

        if ($completedReviews->count() === 0) {
            return 0;
        }

        $totalDays = $completedReviews->sum(function ($submission) {
            return $submission->submission_date->diffInDays($submission->reviewed_at);
        });

        return round($totalDays / $completedReviews->count(), 1);
    }
}
