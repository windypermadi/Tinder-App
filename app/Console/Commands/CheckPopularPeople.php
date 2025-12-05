<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Person;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CheckPopularPeople extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'people:check-popular';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for people with more than 50 likes and send email to admin';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking for popular people...');

        // Find people with more than 50 likes who haven't had email sent yet
        $popularPeople = Person::where('likes_count', '>', 50)
            ->where('email_sent', false)
            ->get();

        if ($popularPeople->isEmpty()) {
            $this->info('No new popular people found.');
            return 0;
        }

        $this->info("Found {$popularPeople->count()} popular people.");

        foreach ($popularPeople as $person) {
            try {
                // Send email to admin
                $this->sendAdminEmail($person);
                
                // Mark email as sent
                $person->update(['email_sent' => true]);
                
                $this->info("Email sent for: {$person->name} ({$person->likes_count} likes)");
                
                // Log the event
                Log::info("Popular person notification sent", [
                    'person_id' => $person->id,
                    'person_name' => $person->name,
                    'likes_count' => $person->likes_count
                ]);
                
            } catch (\Exception $e) {
                $this->error("Failed to send email for: {$person->name}");
                $this->error("Error: " . $e->getMessage());
                
                Log::error("Failed to send popular person notification", [
                    'person_id' => $person->id,
                    'person_name' => $person->name,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info('Done checking popular people.');
        return 0;
    }

    /**
     * Send email to admin about popular person
     *
     * @param Person $person
     * @return void
     */
    private function sendAdminEmail(Person $person)
    {
        $adminEmail = config('mail.admin_email', 'admin@example.com');
        
        $subject = "Popular Person Alert: {$person->name}";
        
        $message = "
            <h2>Popular Person Alert</h2>
            <p>The following person has received more than 50 likes:</p>
            
            <ul>
                <li><strong>Name:</strong> {$person->name}</li>
                <li><strong>Age:</strong> {$person->age}</li>
                <li><strong>Location:</strong> {$person->location}</li>
                <li><strong>Total Likes:</strong> {$person->likes_count}</li>
            </ul>
            
            <p>This notification was sent at: " . now()->format('Y-m-d H:i:s') . "</p>
        ";
        
        Mail::html($message, function ($mail) use ($adminEmail, $subject) {
            $mail->to($adminEmail)
                 ->subject($subject);
        });
    }
}

