<?php

namespace App\Http\Controllers;

use App\Helpers\UserActivityLogger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class AddCustomTimesheetController extends Controller
{
    public function customTimesheetForm(Request $request)
    {
        try {
            $id = $request->input('worker');
            $worker = User::where('id', $id)->first();

            if (!$worker) {
                return redirect()->back()->withErrors('error', 'Werknemer niet gevonden.');
            }

            

            return view('addTimesheet', ['id' => $id, 'worker' => $worker]);
        } catch (QueryException $e) {
            Log::error('Failed to load custom timesheet form', [
                'worker_id' => $request->input('worker'),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()->withErrors('error', 'Er is een fout opgetreden bij het laden van het timesheet-formulier.');
        }
    }
}