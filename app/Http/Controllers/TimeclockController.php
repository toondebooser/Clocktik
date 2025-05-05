<?php

namespace App\Http\Controllers;

use App\Models\Extra_break_slot;
use App\Utilities\CalculateUtility;
use App\Utilities\DateUtility;
use App\Utilities\TimeloggingUtility;
use App\Utilities\UserUtility;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class TimeclockController extends Controller
{



    public function startWorking(): RedirectResponse
    {
        try {
            return DB::transaction(function (): RedirectResponse {
                $currentUser = Auth::user();
                if (!$currentUser) {
                    throw new Exception('User not authenticated');
                }
                $now = now('Europe/Brussels');
                $dayTotal = UserUtility::findOrCreateUserDayTotal($now, $currentUser->id);
                UserUtility::CheckUserMonthTotal($now->copy(), $currentUser->id);
                $dayTotal->Weekend = DateUtility::checkWeekend($now, $currentUser->company->company_code);
                $userRow = $currentUser->timelogs;
                if (!$userRow) {
                    $userRow = $currentUser->timelogs->create([
                        'UserId' => $currentUser->id,
                        'daytotal_id' => $dayTotal->id,
                        'Month' => $now->format('Y-m-d'),
                    ]);
                }
                $userRow->update([
                    'daytotal_id' => $dayTotal->id,
                    'timesheet_id' => null,
                    'StartWork' => $now,
                    'StartBreak' => null,
                    'EndBreak' => null,
                    'BreaksTaken' => $dayTotal->BreaksTaken,
                    'StopWork' => null,
                    'userNote' => null,
                    'ShiftStatus' => true,
                ]);
                return redirect('/dashboard');
            });
        } catch (Exception $e) {
            Log::error('startWorking failed', ['error' => $e->getMessage(), 'user_id' => Auth::id()]);
            return redirect('/dashboard')->with('error', 'Failed to start shift: ' . $e->getMessage());
        }
    }



    public function break()
    {


        DB::transaction(function () {
            $now = now('Europe/Brussels');
            $userRow = auth()->user()->timelogs;
            $dayTotal = $userRow->dayTotal;
            if ($userRow->StartBreak !== null && $userRow->BreaksTaken == 1 && $userRow->timesheet_id == null) {

                $buildTimesheet = TimeloggingUtility::createTimesheetEntry([
                    $userRow->id
                ], auth()->user()->id);

                $addTimesheet = TimeloggingUtility::updateOrInsertTimesheet($buildTimesheet, null);
                $userRow->update(['timesheet_id' => $addTimesheet->id]);
            } 
            // elseif ($userRow->BreaksTaken > 1 && $userRow->timesheet_id !== null) {
            //   TimeloggingUtility::ExtraBreakSlot($userRow->id);
            // }

            $userRow->update([
                'StartBreak' => $now,
                'BreakStatus' => true,
                'BreaksTaken' => $userRow->BreaksTaken += 1
            ]);

            $dayTotal->update([
                'Regularhours' => $dayTotal->RegularHours += CalculateUtility::calculateDecimal(
                    $userRow->EndBreak ?: $userRow->StartWork,
                    $now
                ),
            ]);
        });

        return redirect()->back();
    }


    public function stopBreak()
    {
        $now = now('Europe/Brussels');
        $userRow = auth()->user()->timelogs;
        DB::transaction(function () use ($userRow, $now) {
            $dayTotal = $userRow->dayTotal;
            $userRow->update([
                'BreakStatus' => false,
                'EndBreak' => $now,
            ]);
            if ($userRow->BreaksTaken > 1 && $userRow->timesheet_id !== null) {
                TimeloggingUtility::ExtraBreakSlot($userRow->id);
              }
            $dayTotal->update([
                'Breakhours' => $dayTotal->BreakHours += CalculateUtility::calculateDecimal($userRow->StartBreak, $now),
            ]);
        });
        return redirect()->back();
    }

    public function stop()
    {
        $userRow = auth()->user()->timelogs;
        $now = now('Europe/Brussels');

        DB::transaction(function () use ($userRow, $now) {
            $dayTotal = $userRow->dayTotal;
            $userRow->update([
                'ShiftStatus' => false,
                'StopWork' => $now,
            ]);
            if ($userRow->BreakStatus == true) {
                $userRow->update([
                    'BreakStatus' => false,
                    'EndBreak' => $now,
                ]);
                $dayTotal->update([
                    'BreakHours' => $dayTotal->BreakHours += CalculateUtility::calculateDecimal($userRow->StartBreak, $now)
                ]);
            }
            $dayTotal->update([
                'RegularHours' => $dayTotal->RegularHours += CalculateUtility::calculateDecimal(
                    $userRow->EndBreak ?? $userRow->StartWork,
                    $now
                ),

            ]);
        });
        return Redirect::route('makeTimesheet', ['id' => auth()->user()->id]);
    }
}
