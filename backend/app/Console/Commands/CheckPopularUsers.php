<?php

namespace App\Console\Commands;

use App\Models\Swipe;
use App\Models\User;
use App\Mail\PopularUserNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Exception;

class CheckPopularUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-popular-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for popular users who have received more than 50 likes and send admin notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Starting popular users check...');

            // Find users who have received more than 50 likes and haven't been notified yet
            $popularUsers = User::select('users.*')
            ->join('swipes', 'users.id', '=', 'swipes.swiped_id')
            ->where('swipes.action', 'like')
            ->whereNull('users.popular_notification_sent_at')
            ->groupBy('users.id')
            ->havingRaw('COUNT(*) > 50')
            ->withCount(['swipesReceived as likes_count' => function ($query) {
                $query->where('action', 'like');
            }])
            ->get();

            if ($popularUsers->isEmpty()) {
                $this->info('No popular users found that need notification.');
                return 0;
            }

            $this->info("Found {$popularUsers->count()} popular users to notify about.");

            $adminEmail = 'admin@example.com';
            $notifiedCount = 0;

            foreach ($popularUsers as $user) {
                try {
                    // Send email notification to admin
                    Mail::to($adminEmail)->send(new PopularUserNotification($user));

                    // Update the user's popular_notification_sent_at timestamp
                    $user->update([
                        'popular_notification_sent_at' => now()
                    ]);

                    $notifiedCount++;
                    $this->info("Notified admin about popular user: {$user->name} (ID: {$user->id}) - {$user->likes_count} likes");

                    Log::info("Popular user notification sent", [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'likes_count' => $user->likes_count,
                        'admin_email' => $adminEmail
                    ]);

                } catch (Exception $e) {
                    $this->error("Failed to notify about user {$user->name} (ID: {$user->id}): {$e->getMessage()}");

                    Log::error("Popular user notification failed", [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $this->info("Popular users check completed. {$notifiedCount} notifications sent.");
            Log::info("Popular users check completed", ['notified_count' => $notifiedCount]);

            return 0;

        } catch (Exception $e) {
            $this->error("Popular users check failed: {$e->getMessage()}");

            Log::error("Popular users check failed", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return 1;
        }
    }
}
