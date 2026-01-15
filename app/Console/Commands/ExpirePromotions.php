<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Promotion;
use Carbon\Carbon;

class ExpirePromotions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promos:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically expire promotions that have passed their end date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        
        $expiredCount = Promotion::whereNotNull('ends_at')
            ->where('ends_at', '<', $now)
            ->where('status', '!=', 'expired')
            ->where('is_active', true)
            ->update([
                'status' => 'expired',
                'is_active' => false,
            ]);

        if ($expiredCount > 0) {
            $this->info("Expired {$expiredCount} promotion(s) successfully.");
        } else {
            $this->info('No promotions to expire.');
        }

        // Also update status for upcoming promotions that should be active
        $activatedCount = Promotion::where('status', 'upcoming')
            ->where('starts_at', '<=', $now)
            ->where(function($q) use ($now) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', $now);
            })
            ->update([
                'status' => 'active',
                'is_active' => true,
            ]);

        if ($activatedCount > 0) {
            $this->info("Activated {$activatedCount} promotion(s) successfully.");
        }

        return 0;
    }
}
