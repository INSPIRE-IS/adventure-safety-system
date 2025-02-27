<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CommunicationTemplate;
use App\Models\User;
use App\Traits\MailTrait;

class TestWhatsAppSend extends Command
{
    //use MailTrait;

    protected $signature = 'test:send-whatsapp 
                            {template_id? : The ID of the template to use} 
                            {recipient? : The recipient\'s phone number}
                            {message? : The message to send}';

    protected $description = 'Send a test WhatsApp message using the bird_whatsapp_send method from MailTrait';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Usage examples: default values or specific values
        // php artisan test:send-whatsapp
        // php artisan test:send-whatsapp 2 "27891234567" "This is a specific test message"
        // php artisan test:send-whatsapp 2 "+27736574498" "This is a specific test message"

        // Fetching arguments
        // 2,Application Approved
        // 3,Application Denied
        // 4,Emergency Contact
        // 25,Guarantor Sign Lease
        // 117,Message Error 2025
        // 132,Premises Departure
        // 21,Student Sign Lease
        // 136,Test user_confirmation
        // 90,UJ NSFAS & BURSAR FUNDED STUDENTS ADMINISTERED THROUGH HIVE PORTAL
        // 113,Verify Whatsapp

        $template_id = $this->argument('template_id') ?? 136; // Default to template ID 1 if none provided
        $recipient = $this->argument('recipient') ?? '+27736574498'; // Default recipient phone number for testing
        $message = $this->argument('message') ?? 'This is a test WhatsApp message';
        $debug = 1;

        // Fetching from the CommunicationTemplate table if not hardcoded
        $template = null; //CommunicationTemplate::find($template_id);
        if (!$template && $template_id > 0) {
            $this->error('Template with ID ' . $template_id . ' not found.');
            return;
        }
        // Dummy data for testing if not provided
        // send to a specific user 1 admin
        $user = User::find(1); // Assuming you are testing with an authenticated user, or set it to null
        $bulk_id = 0; // Set to whatever value makes sense for testing
        $sent_by = 0; // Set to the default user or any user ID for testing

        try {
            // Using the bird_whatsapp_send method from the trait
            $debug == 1 && $this->info('Template ID ' . $template_id);
            $this->bird_whatsapp_send($user, $template_id, $recipient, $message, $bulk_id, $sent_by);
            $debug == 1 && $this->info('WhatsApp message sent successfully to ' . $recipient);
        } catch (\Exception $e) {
            $this->error('Failed to send WhatsApp message: ' . $e->getMessage());
        }
    }
}
