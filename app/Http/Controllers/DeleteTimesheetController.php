<?php

namespace App\Http\Controllers;

use App\Models\Daytotal;
use App\Models\Timesheet;
use App\Models\User;
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
        $user = User::find($workerId);
        $dayTotal = $user->daytotals()->where('Month', $date)->first();
        dd($dayTotal->id);
        if (empty($request->all())) {
            $request->merge([
                'workerId' => $workerId,
                'deleteSheet' => $deleteSheet,
                'date' => $date,
            ]);
        }
        $day = Timesheet::find($deleteSheet) ?? Daytotal::find($deleteSheet);
        $request->validate([
            'workerId' => 'required|numeric',
            'deleteSheet' => 'required|numeric',
            'date' => 'required|date',
        ]);
        
        dd($request->all());


        !$day ? $this->redirectError('Dag niet gevonden.', $workerId) : null;



        DB::transaction(function () use ($day, $dayTotal, $date, $workerId) {
            $day->delete();

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
    public function deleteSpecialDay(Request $request, $workerId = null, $deleteSheet = null){

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
