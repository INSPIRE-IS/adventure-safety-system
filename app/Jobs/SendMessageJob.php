<?php

namespace App\Jobs;

use App\Models\BulkMessage;
use App\Models\BulkMessageLog;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $siteId;
    protected $message;
    protected $subject;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, int $siteId, string $subject, string $message)
    {
        $this->userId = $userId;
        $this->siteId = $siteId;
        $this->message = $message;
        $this->subject = $subject;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::find($this->userId);

        if (!$user) {
            Log::error("User ID {$this->userId} not found. Skipping email.");
            return;
        }

        $forcedRecipient = config('mail.force_to');
        $recipientEmail = $forcedRecipient ?: $user->email;

        if (!$recipientEmail) {
            Log::error("User ID {$this->userId} has no email. Skipping email.");
            return;
        }

        if ($forcedRecipient) {
            Log::warning("MAIL_FORCE_TO is set; forcing email recipient to {$forcedRecipient} for user ID {$user->id}");
        }

        Log::info("Preparing to send email to: {$recipientEmail} (user ID {$user->id})");

        $bulk_message = BulkMessage::create([
            'subject' => $this->subject,
            'message' => $this->message,
            'site_id' => $this->siteId,
            'user_id' => $this->userId,
        ]);

        $log = BulkMessageLog::create([
            'user_id' => $this->userId,
            'email' => $recipientEmail,
            'bulk_message_id' => $bulk_message->id,
            'status' => 'pending',
        ]);

        try {
            Mail::to($recipientEmail)->send(new \App\Mail\MessageMail($this->message, $this->subject));
            $log->update(['status' => 'sent']);
            Log::info("Email sent to: {$recipientEmail}");
        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            Log::error("Failed to send email to {$user->email}: " . $e->getMessage());
        }
    }
}
