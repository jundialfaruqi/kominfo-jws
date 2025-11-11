<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class BackfillUserLastActivity extends Command
{
    /** @var string */
    protected $signature = 'users:backfill-last-activity {--chunk=200 : Process users in chunks}';

    /** @var string */
    protected $description = 'Recalculate and persist users.last_activity_at from related tables';

    public function handle(): int
    {
        $chunk = (int) $this->option('chunk');
        $count = 0;

        User::select('id')
            ->orderBy('id')
            ->chunk($chunk, function ($users) use (&$count) {
                foreach ($users as $user) {
                    $user->recalculateLastActivity();
                    $count++;
                }
                $this->output->write(".");
            });

        $this->newLine();
        $this->info("Processed {$count} users.");
        return self::SUCCESS;
    }
}