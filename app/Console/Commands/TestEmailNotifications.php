<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\HkiSubmission;
use App\Notifications\SubmissionStatusChanged;
use App\Notifications\CertificateSent;

class TestEmailNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notifications {--user=1} {--submission=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email notifications system';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userId = $this->option('user');
        $submissionId = $this->option('submission');

        $user = User::find($userId);
        $submission = HkiSubmission::find($submissionId);

        if (!$user) {
            $this->error("User with ID {$userId} not found");
            return;
        }

        if (!$submission) {
            $this->error("Submission with ID {$submissionId} not found");
            return;
        }

        $this->info("Testing notifications for:");
        $this->info("User: {$user->nama} ({$user->email})");
        $this->info("Submission: {$submission->title}");
        $this->info("");

        // Test 1: Status Changed Notification
        $this->info("1. Testing SubmissionStatusChanged notification...");
        $user->notify(new SubmissionStatusChanged(
            $submission, 
            'submitted', 
            'approved', 
            'Test approval notification - submission berhasil disetujui!'
        ));
        $this->info("✅ SubmissionStatusChanged notification sent");

        // Test 2: Certificate Sent Notification
        if ($submission->documents()->where('document_type', 'certificate')->exists()) {
            $this->info("2. Testing CertificateSent notification...");
            $certificate = $submission->documents()->where('document_type', 'certificate')->first();
            $user->notify(new CertificateSent(
                $submission, 
                $certificate, 
                'Test certificate notification - sertifikat sudah tersedia!'
            ));
            $this->info("✅ CertificateSent notification sent");
        } else {
            $this->warn("2. Skipping CertificateSent - no certificate found");
        }

        $this->info("");
        $this->info("🎉 All notifications sent! Check your Mailtrap inbox.");
        
        // Show database notifications
        $this->info("");
        $this->info("Recent database notifications for this user:");
        $notifications = $user->notifications()->latest()->take(3)->get();
        foreach ($notifications as $notification) {
            $data = $notification->data;
            $this->line("- {$data['title']}: {$data['message']}");
        }

        return Command::SUCCESS;
    }
}
