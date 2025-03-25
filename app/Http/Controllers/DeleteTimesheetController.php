<?php

namespace App\Http\Controllers;

use App\Models\Daytotal;
use App\Models\Timesheet;
use App\Utilities\CalculateUtility;
use App\Utilities\TimeloggingUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

class DeleteTimesheetController extends Controller
{
    public function deleteTimesheet(Request $request, $workerId = null, $deleteSheet = null, $date = null)
    {
        $workerId = $workerId ?? $request->workerId;
        $deleteSheet = $deleteSheet ?? $request->deleteSheet;
        $date = $date ?? $request->date;
        $dayTotal = Daytotal::where('Month', $date)->first();

        if (empty($request->all())) {
            $request->merge([
                'workerId' => $workerId,
                'deleteSheet' => $deleteSheet,
                'date' => $date,
            ]);
        }
        $request->validate([
            'workerId' => 'required|numeric',
            'deleteSheet' => 'required|numeric|exists:timesheets,id',
            'date' => 'required|date',
        ]);
        
        $timesheet = Timesheet::findOrFail($deleteSheet);
        dd($request->all());


        !$timesheet ? $this->redirectError('Timesheet niet gevonden.', $workerId) : null;


        // $deleted = $timesheet->delete();
        // if ($deleted) {
        //     $timeloggingUtility = new TimeloggingUtility;

        //     if ($dayTotal && $dayTotal->timesheets->isEmpty()) {
        //         $dayTotal->delete();
        //         CalculateUtility::calculateUserTotal($date, $workerId);
        //         return redirect('/my-workers')->with('success', 'Timesheet succesvol verwijderd.');
        //     }

        //     $timeloggingUtility->updateDailySummery($workerId, $date);
        //     CalculateUtility::calculateUserTotal($date, $workerId);

        //     return redirect('/my-workers')->with('success', 'Timesheet succesvol verwijderd.');
        // }


        DB::transaction(function () use ($timesheet, $dayTotal, $date, $workerId) {
            $timesheet->delete();

            if ($dayTotal) {
                if ($dayTotal->type !== 'workday' || $dayTotal->timesheets->isEmpty()) {
                    $dayTotal->delete();
                } else {
                    (new TimeloggingUtility)->updateDailySummery($workerId, $date);
                }
            } elseif (!$dayTotal) {
                $this->redirectError('Geen dagtotal gevonden, berekeningen bijgewerkt.', $workerId);
            }

            CalculateUtility::calculateUserTotal($date, $workerId);
        });

        return $this->redirectSuccess('Timesheet succesvol verwijderd.');
    }

    private function redirectSuccess(string $message)
    {
        return redirect()->route('my-workers')->with('success', $message);
    }

    private function redirectError(string $message, $workerId)
    {
        return redirect()->route('getData', ['worker' => $workerId])->with('error', $message);
    }
}
