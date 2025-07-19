<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\HkiSubmission;
use App\Notifications\SubmissionStatusChanged;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {user_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email notification';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user = User::find($this->argument('user_id'));
        $submission = $user->submissions()->first();

        if (!$user || !$submission) {
            $this->error('User atau submission tidak ditemukan');
            return;
        }

        $user->notify(new SubmissionStatusChanged(
            $submission, 
            'submitted', 
            'approved', 
            'Test notification email berhasil!'
        ));

        $this->info('Test email berhasil dikirim ke ' . $user->email);
    }
}
