<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HkiSubmission;
use App\Models\SubmissionHistory;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        $submissions = HkiSubmission::where('reviewer_id', Auth::id())
            ->where('status', 'under_review')
            ->with(['user']) // Hapus 'documents' untuk sementara
            ->orderBy('submission_date', 'asc')
            ->paginate(10);

        return view('reviewer.reviews.index', compact('submissions'));
    }

    public function show(HkiSubmission $submission)
    {
        $this->authorize('review', $submission);
        
        $submission->load(['user', 'histories.user']); // Load tanpa 'documents' dulu
        
        return view('reviewer.reviews.show', compact('submission'));
    }

    public function approve(Request $request, HkiSubmission $submission)
    {
        $this->authorize('review', $submission);
        
        $request->validate([
            'review_notes' => 'required|string|max:1000',
        ]);

        $submission->update([
            'status' => 'approved',
            'review_notes' => $request->review_notes,
            'reviewed_at' => now(),
        ]);

        // Create history record
        SubmissionHistory::create([
            'submission_id' => $submission->id,
            'user_id' => Auth::id(),
            'action' => 'Approved',
            'previous_status' => 'under_review',
            'new_status' => 'approved',
            'notes' => $request->review_notes,
        ]);

        return redirect()->route('reviewer.reviews.index')
            ->with('success', 'Submission approved successfully');
    }

    public function reject(Request $request, HkiSubmission $submission)
    {
        $this->authorize('review', $submission);
        
        $request->validate([
            'review_notes' => 'required|string|max:1000',
        ]);

        $submission->update([
            'status' => 'rejected',
            'review_notes' => $request->review_notes,
            'reviewed_at' => now(),
        ]);

        // Create history record
        SubmissionHistory::create([
            'submission_id' => $submission->id,
            'user_id' => Auth::id(),
            'action' => 'Rejected',
            'previous_status' => 'under_review',
            'new_status' => 'rejected',
            'notes' => $request->review_notes,
        ]);

        return redirect()->route('reviewer.reviews.index')
            ->with('success', 'Submission rejected');
    }

    public function requestRevision(Request $request, HkiSubmission $submission)
    {
        $this->authorize('review', $submission);
        
        $request->validate([
            'review_notes' => 'required|string|max:1000',
        ]);

        $submission->update([
            'status' => 'revision_needed',
            'review_notes' => $request->review_notes,
            'reviewed_at' => now(),
        ]);

        // Create history record
        SubmissionHistory::create([
            'submission_id' => $submission->id,
            'user_id' => Auth::id(),
            'action' => 'Revision Requested',
            'previous_status' => 'under_review',
            'new_status' => 'revision_needed',
            'notes' => $request->review_notes,
        ]);

        return redirect()->route('reviewer.reviews.index')
            ->with('success', 'Revision requested');
    }
}
