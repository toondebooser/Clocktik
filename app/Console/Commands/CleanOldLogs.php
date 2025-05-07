<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanOldLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clean-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logPath = storage_path('logs/laravel.log');
    
        if (!file_exists($logPath)) {
            $this->info('Log file does not exist.');
            return;
        }
    
        $lines = file($logPath);
        $thresholdDate = now()->subMonths(3);
    
        $filteredLines = array_filter($lines, function ($line) use ($thresholdDate) {
            // Try to extract date from line like: [2025-05-07 14:48:32]
            if (preg_match('/\[(\d{4}-\d{2}-\d{2})/', $line, $matches)) {
                $lineDate = \Carbon\Carbon::parse($matches[1]);
                return $lineDate->greaterThanOrEqualTo($thresholdDate);
            }
            return true; // keep lines without date
        });
    
        file_put_contents($logPath, implode('', $filteredLines));
    
        $this->info('Old logs cleaned up.');
    }
    
}
