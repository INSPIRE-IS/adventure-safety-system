<?php

namespace App\Console\Commands;

use App\Mail\CommunicationMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TestEmailSending extends Command
{
    protected $signature = 'test:send-email {email?} {user_id?} {application_id?} {subject?} {message?}';
    protected $description = 'Send a test email to the specified address';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        // examples:
        // php artisan test:send-email your-email@example.com
        // php artisan test:send-email your-email@example.com CustomeUserID CustomAppID "Custom Subject" "This is a custom test message"


        // $email = $this->argument('email') ?? 'jasonlong.inspire@gmail.com';
        // $user_id = $this->argument('user_id') ?? 1;
        // $application_id = $this->argument('application_id') ?? 'TestAppID';
        // $subject = $this->argument('subject') ?? 'Test Subject - ' . Carbon::now()->format('Y-m-d H:i:s');
        // $message = $this->argument('message') ?? 'This is a test message body.';

        // // Default values for additional parameters
        // $recipient_filter = 'test_filter';
        // $from_name = 'Test';
        // $from_email = 'no-reply@gmail.com';
        // $attachment = false; // Set this to true if you want to test with an attachment
        // $attachment_path = storage_path('app/test_attachment.pdf'); // Adjust the path if needed
        // $filename = 'TestAttachment.pdf';

        if (true) {
            $email = 'jasonlong.inspire@gmail.com';
            $mail_message = "This is a test email sent at " . Carbon::now()->format('Y-m-d H:i:s') . "\n\n";
            $mail_message .= "If you receive this email, the email sending feature is working properly.";

            try {
                $res =  Mail::to('jasonlong.inspire@gmail.com')->send(new \App\Mail\MessageMail('test', "Test Email - " . Carbon::now()->format('Y-m-d H:i:s')));
                // Mail::raw('Test email body', function ($message) {
                //     $message->to('test@example.com')->subject('Test Email');
                // });
                $this->info('Test email sent successfully to ' . $email);
            } catch (\Exception $e) {
                $this->error('Failed to send test email111111: ' . $e->getMessage());
                Log::error("Error sending test email222222222: " . $e->getMessage());
            }
        }
    }
}
