<?php

namespace App\Http\Controllers;

use App\Helpers\UserActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SauronsEyeController extends Controller
{
    public function index()
    {
        $this->middleware('admin');

        try {
            $logDir = storage_path('logs');
            $logFile = $logDir . '/laravel.log';
            $logs = [];
            $threeWeeksAgo = Carbon::now()->subWeeks(3);

            // Delete old daily log files
            $logFiles = File::glob($logDir . '/laravel-*.log');
            foreach ($logFiles as $file) {
                $fileDate = Carbon::createFromFormat('Y-m-d', substr(basename($file), 8, 10));
                if ($fileDate->lessThan($threeWeeksAgo)) {
                    File::delete($file);
                    UserActivityLogger::log('Deleted old log file', [
                        'file' => $file,
                        'user_id' => auth()->user()->id ?? null,
                    ]);
                }
            }

            // Read current log file
            if (File::exists($logFile)) {
                $content = File::get($logFile);
                $logEntries = preg_split('/\n(?=\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\])/', $content);

                foreach ($logEntries as $entry) {
                    if (trim($entry)) {
                        preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\].*?\.(\w+): (.*?)(?={|\n|$)/s', $entry, $matches);
                        $timestamp = $matches[1] ?? 'Unknown';
                        $level = $matches[2] ?? 'Unknown';
                        $message = $matches[3] ?? 'No message';
                        $context = [];

                        if (preg_match('/{.*}/s', $entry, $contextMatch)) {
                            $context = json_decode($contextMatch[0], true) ?? [];
                        }

                        $logs[] = [
                            'timestamp' => $timestamp,
                            'level' => strtoupper($level),
                            'message' => trim($message),
                            'context' => $context,
                        ];
                    }
                }
            }

            UserActivityLogger::log('Log file retrieved successfully', [
                'log_file' => $logFile,
                'log_count' => count($logs),
                'user_id' => auth()->user()->id ?? null,
            ]);

            return view('sauronsEye', ['logs' => array_reverse($logs)]);
        } catch (\Exception $e) {
            Log::error('Failed to read or clean log files', [
                'log_file' => $logFile,
                'user_id' => auth()->user()->id ?? null,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Er is een fout opgetreden bij het ophalen of opschonen van de logs.']);
        }
    }
}