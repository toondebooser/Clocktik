<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class AddCustomTimesheetController extends Controller
{
    public function customTimesheetForm(Request $request)
    {
        $id = $request->input('worker');
        
        try {
            $worker = User::where('id', $id)->first();
            return view('addTimesheet', ['id' => $id, 'worker' => $worker]);
        } catch (QueryException $e) {
            Log::error('Failed to retrieve worker in customTimesheetForm', [
                'worker_id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return redirect()->back()->with('error', 'Er is een fout opgetreden bij het ophalen van de gegevens.');
        }
    }
}