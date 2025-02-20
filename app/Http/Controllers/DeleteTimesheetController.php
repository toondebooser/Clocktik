<?php

namespace App\Http\Controllers;

use App\Models\Daytotal;
use App\Models\Timesheet;
use App\Utilities\CalculateUtility;
use App\Utilities\TimeloggingUtility;
use Illuminate\Http\Request;

class DeleteTimesheetController extends Controller
{
    public function deleteTimesheet(Request $request, $workerId = null, $deleteSheet = null, $date = null)
    {
        // Resolve parameters: Prefer route params, fall back to request data
        $workerId = $workerId ?? $request->input('workerId');
        $deleteSheet = $deleteSheet ?? $request->input('deleteSheet');
        $date = $date ?? $request->input('date');

        // Validate required parameters
        if (!$deleteSheet) {
            return redirect()->route('getData', ['worker' => $workerId])
                ->with('error', 'Geen timesheet ID opgegeven.');
        }

        // Find and delete the timesheet
        $timesheet = Timesheet::find($deleteSheet);
        if (!$timesheet) {
            return redirect()->route('getData', ['worker' => $workerId])
                ->with('error', 'Timesheet niet gevonden.');
        }

        $deleted = $timesheet->delete();
        if ($deleted) {
            $timeloggingUtility = new TimeloggingUtility;
            $dayTotal = Daytotal::where('Month', $date)->first();

            // Check if dayTotal exists and has no timesheets left
            if ($dayTotal && $dayTotal->timesheets->isEmpty()) {
                $dayTotal->delete();
                CalculateUtility::calculateUserTotal($date, $workerId);
                return redirect('/my-workers')->with('success', 'Timesheet succesvol verwijderd.');
            }

                $timeloggingUtility->updateDailySummery($workerId, $date);
                CalculateUtility::calculateUserTotal($date, $workerId);

            return redirect('/my-workers')->with('success', 'Timesheet succesvol verwijderd.');
        }

        // Fallback error case
        return redirect()->route('getData', ['worker' => $workerId])
            ->with('error', 'Er ging iets mis bij het verwijderen van de timesheet.');
    }
}