<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserStatus;
use Carbon\Carbon;

class DeleteExpiredStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statuses:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired user statuses and their associated files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = 0;
        // Iterate to trigger model events for file deletion
        $expiredStatuses = UserStatus::where('expires_at', '<', Carbon::now())->get();

        foreach ($expiredStatuses as $status) {
            $status->delete();
            $count++;
        }

        $this->info("Deleted {$count} expired statuses.");
    }
}
