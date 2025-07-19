<?php
// filepath: app/Console/Commands/TestGmailSMTP.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestGmailSMTP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:gmail-smtp {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Gmail SMTP connection';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $testEmail = $this->argument('email');
        
        $this->info('Testing Gmail SMTP connection...');
        $this->info('To: ' . $testEmail);
        $this->info('From: ' . config('mail.from.address'));
        
        try {
            Mail::raw('Test email dari SiHaki menggunakan Gmail SMTP.', function($message) use ($testEmail) {
                $message->to($testEmail)
                       ->subject('Test Email SiHaki - Gmail SMTP');
            });
            
            $this->info('✅ Email berhasil dikirim!');
            $this->info('Check inbox email: ' . $testEmail);
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error('Line: ' . $e->getLine());
            $this->error('File: ' . $e->getFile());
        }

        return Command::SUCCESS;
    }
}
