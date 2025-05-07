<?php

namespace App\Http\Controllers;

use App\Helpers\UserActivityLogger;
use App\Models\Daytotal;
use App\Models\Extra_break_slot;
use App\Models\Timesheet;
use App\Models\User;
use App\Utilities\CalculateUtility;
use App\Utilities\TimeloggingUtility;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteTimesheetController extends Controller
{
    public function deleteTimesheet(Request $request, $workerId = null, $deleteSheet = null, $date = null, $sheetType = null)
    {
        try {
            $workerId = $workerId ?? $request->workerId;
            $deleteSheet = $deleteSheet ?? $request->deleteSheet;
            $date = $date ?? $request->date;
            $sheetType = $sheetType ?? $request->sheetType;

            if (empty($request->all())) {
                $request->merge([
                    'workerId' => $workerId,
                    'deleteSheet' => $deleteSheet,
                    'date' => $date,
                    'sheetType' => $sheetType,
                ]);
            }

            $request->validate([
                'workerId' => 'required|numeric',
                'deleteSheet' => 'required|numeric',
                'date' => 'required|date',
                'sheetType' => 'required|string',
            ]);

            $user = User::find($workerId);
            if (!$user) {
                return redirect()->route('getData', ['worker' => $workerId])->withErrors('error', 'Gebruiker niet gevonden.');
            }

            $dayTotal = $user->daytotals()->where('Month', $date)->first();
            $modelMap = [
                'timesheets' => Timesheet::class,
                'extra_break_slots' => Extra_break_slot::class,
                'daytotals' => Daytotal::class,
            ];

            $sheet = $modelMap[$sheetType]::find($deleteSheet);
            if ($sheet === null) {
                return redirect()->route('getData', ['worker' => $user->id])->withErrors('error', 'Dag niet gevonden.');
            }

            DB::transaction(function () use ($sheet, $dayTotal, $date, $workerId, $sheetType, $deleteSheet) {
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

                // Log success
                UserActivityLogger::log('Timesheet deleted successfully', [
                    'worker_id' => $workerId,
                    'sheet_type' => $sheetType,
                    'sheet_id' => $deleteSheet,
                    'date' => $date,
                    'user_id' => auth()->user()->id ?? null,
                ]);
            });

            return redirect()->route('getData', ['worker' => $user->id])->with('success', 'Timesheet succesvol verwijderd.');
        } catch (QueryException $e) {
            Log::error('Failed to delete timesheet', [
                'worker_id' => $workerId,
                'sheet_type' => $sheetType,
                'sheet_id' => $deleteSheet,
                'date' => $date,
                'user_id' => auth()->user()->id ?? null,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()->withErrors('error', 'Er is een fout opgetreden bij het verwijderen van de timesheet.');
        } catch (Exception $e) {
            Log::error('Failed to delete timesheet', [
                'worker_id' => $workerId,
                'sheet_type' => $sheetType,
                'sheet_id' => $deleteSheet,
                'date' => $date,
                'user_id' => auth()->user()->id ?? null,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()->withErrors('error', 'Er is een fout opgetreden bij het verwijderen van de timesheet.');
        }
    }
}