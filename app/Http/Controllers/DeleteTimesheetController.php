<?php

namespace App\Http\Controllers;

use App\Models\Daytotal;
use App\Models\ExtraBreakSlot;
use App\Models\Timesheet;
use App\Models\User;
use App\Utilities\CalculateUtility;
use App\Utilities\TimeloggingUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;


class DeleteTimesheetController extends Controller
{
    public function deleteTimesheet(Request $request, $workerId = null, $deleteSheet = null, $date = null, $sheetType = null)
{
    try {
        $workerId = $workerId ?? $request->workerId;
        $deleteSheet = $deleteSheet ?? $request->deleteSheet;
        $date = $date ?? $request->date;
        $user = User::find($workerId);
        $dayTotal = $user->dayTotals()->where('Month', $date)->first();
        $modelMap = [
            'timesheets' => Timesheet::class,
            'extra_break_slots' => ExtraBreakSlot::class,
            'daytotals' => Daytotal::class
        ];

        if (empty($request->all())) {
            $request->merge([
                'workerId' => $workerId,
                'deleteSheet' => $deleteSheet,
                'date' => $date,
                'sheetType' => $sheetType
            ]);
        }
        
        $request->validate([
            'workerId' => 'required|numeric',
            'deleteSheet' => 'required|numeric',
            'date' => 'required|date',
            'sheetType' => 'required|string'
        ]);
        $sheet = $modelMap[$request->sheetType]::find($deleteSheet);
        
        if ($sheet === null) {
            return $this->redirectError('Dag niet gevonden.', $user);
        }

        DB::transaction(function () use ($sheet, $dayTotal, $date, $workerId) {
            $sheet->delete();

            if ($dayTotal) {
                if ($dayTotal->type !== 'workday' || $dayTotal->timesheets->isEmpty()) {
                    $dayTotal->delete();
                } else {
                    (new TimeloggingUtility)->updateDailySummery($workerId, $date);
                }
            } elseif (!$dayTotal) {
                throw new Exception('Geen dagtotal gevonden, berekeningen bijgewerkt.');
            }

            CalculateUtility::calculateUserTotal($workerId);
        });

        return $this->redirectSuccess('Timesheet succesvol verwijderd.', $user);
    } catch (Exception $e) {
        Log::error("Error in deleteTimesheet for worker ID $workerId: " . $e->getMessage());
        return redirect()->back()->withErrors(['error' => 'Er ging iets mis bij het aanpassen van de instellingen: ' . $e->getMessage()]);
    }
}
   
    private function redirectSuccess(string $message, $user)
    {
        return redirect()->route('getData', ['worker' => $user->id  ])->with('success', $message);
    }

    private function redirectError(string $message, $user)
    {
        return redirect()->route('getData', ['worker' => $user->id  ])->with('success', $message);
    }
}
