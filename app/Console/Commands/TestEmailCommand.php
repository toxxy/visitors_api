<?php

namespace App\Console\Commands;

use App\Models\Visit;
use App\Notifications\VisitConfirmationNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email} {--visit-id=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test visit confirmation email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $visitId = $this->option('visit-id');

        $visit = Visit::with(['department', 'site'])->find($visitId);

        if (!$visit) {
            $this->error("Visit with ID {$visitId} not found");
            return;
        }

        $this->info("Sending test email to: {$email}");
        $this->info("Using visit ID: {$visitId}");
        $this->info("Visit details: {$visit->visitor_name} - {$visit->site->name}");

        try {
            Notification::route('mail', $email)
                ->notify(new VisitConfirmationNotification($visit));

            $this->info("✅ Email sent successfully!");
            $this->info("Check your inbox and spam folder at: {$email}");
        } catch (\Exception $e) {
            $this->error("❌ Failed to send email: " . $e->getMessage());
        }
    }
}
