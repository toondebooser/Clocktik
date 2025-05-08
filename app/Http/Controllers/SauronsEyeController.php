<?php

namespace App\Http\Controllers;

use App\Helpers\UserActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SauronsEyeController extends Controller
{
    public function index()
    {
        $this->middleware('admin');

        try {
            $logFile = storage_path('logs/laravel.log');
            $logs = [];

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

            // Log success
            UserActivityLogger::log('Log file retrieved successfully', [
                'log_file' => $logFile,
                'log_count' => count($logs),
                'user_id' => auth()->user()->id ?? null,
            ]);

            return view('sauronsEye', ['logs' => array_reverse($logs)]);
        } catch (\Exception $e) {
            // Log error
            Log::error('Failed to read log file', [
                'log_file' => storage_path('logs/laravel.log'),
                'user_id' => auth()->user()->id ?? null,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()->withErrors('error', 'Er is een fout opgetreden bij het ophalen van de logs.');
        }
    }
}