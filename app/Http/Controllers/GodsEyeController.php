<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class GodsEyeController extends Controller
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

        return view('gods-eye', ['logs' => array_reverse($logs)]);

    } catch (\Exception $e) {
        Log::error('Failed to read log file', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        return redirect()->back()->with('error', 'Er is een fout opgetreden bij het ophalen van de logs.');
    }
}

    
}
