<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderSuccessMail;
use App\Models\Order;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email functionality with Mailtrap';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Send a simple test email
            Mail::raw('This is a test email from Laravel to verify Mailtrap SMTP configuration.', function ($message) {
                $message->to('test@example.com')
                        ->subject('Laravel Mailtrap Test Email');
            });
            
            $this->info('Test email sent successfully to Mailtrap!');
            $this->info('Check your Mailtrap inbox for the test email.');
            
        } catch (\Exception $e) {
            $this->error('Failed to send test email: ' . $e->getMessage());
        }
    }
}
