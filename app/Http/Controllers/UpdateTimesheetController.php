<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\TimesheetController;
use App\Models\Daytotal;
use Illuminate\Support\Carbon;
use App\Models\Usertotal;
use App\Utilities\CalculateUtility;
use App\Utilities\DateUtility;
use App\Utilities\TimeloggingUtility;
use App\Utilities\UserUtility;
use Illuminate\Support\Facades\Validator;

class UpdateTimesheetController extends Controller
{

    public function updateForm($id, $timesheet, $type = null, $usedDayTotalId = null, $usedDayTotalDate = null)
    {

        $worker = User::find($id);
        if ($type) {
            $timesheet = Timesheet::find($timesheet);
            $nightShift = UserUtility::userDayTotalFetch($timesheet->Month, $id)->NightShift;
            $endDate = Carbon::parse($timesheet->ClockedOut)->format('Y-m-d');
            $startDate = Carbon::parse($timesheet->ClockedIn)->format('Y-m-d');
            $usedDayTotalDate = Carbon::parse($usedDayTotalDate)->format('Y-m-d');
        } else {
            $timesheet = Daytotal::find($timesheet);
            $nightShift = null;
            $startDate = null;
            $endDate = null;
        }
        if ($timesheet === null) {
            $postData = [
                'worker' => $id,
            ];

            return redirect()->route('getData', $postData)->with('error', $worker->name . ' heeft juist ingeklokt. ');
        }
        $startShift = Carbon::parse($timesheet->ClockedIn)->format('H:i');
        $endShift = Carbon::parse($timesheet->ClockedOut)->format('H:i');
        $startBreak = $timesheet->BreakStart ? Carbon::parse($timesheet->BreakStart)->format('H:i') : null;
        $endBreak = $timesheet->BreakStop ? Carbon::parse($timesheet->BreakStop)->format('H:i') : null;
        $monthString = $timesheet->Month->format('d/m/Y');

        return view('updateTimesheet', ['usedDayTotalId' => $usedDayTotalId, 'usedDayTotalDate' => $usedDayTotalDate, 'startDate' => $startDate, 'endDate' => $endDate, 'nightShift' => $nightShift, 'worker' => $worker, 'timesheet' => $timesheet, 'startShift' => $startShift, 'endShift' => $endShift, 'startBreak' => $startBreak, 'endBreak' => $endBreak, 'monthString' => $monthString]);
    }

    public function updateTimesheet(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'startDate'   => 'required|date',
                'endDate'     => 'required|date|after_or_equal:startDate',

                'startTime'   => 'required|date_format:H:i',
                'endTime'     => 'required|date_format:H:i|after:startTime',

                'startBreak'  => 'date_format:H:i',
                'endBreak'    => 'date_format:H:i|after_or_equal:startBreak',
            ]
        );
        if ($validator->fails()) {
            return  redirect()->back()->withErrors($validator);
        }


        $dayType = $request->input('dayType');
        $id = $request->id;
        $companyDayHours = User::find($id)->company->day_hours;
        $timesheet = $request->type == 'workday' ? Timesheet::find($request->timesheet) : Daytotal::find($request->timesheet);
        $type = $request->updateSpecial;
        $weekend = DateUtility::checkWeekend($timesheet->Month, User::find($id)->company);
        $type == null ? $type = $timesheet->type : null;
        $date = $timesheet->Month;
        if ($dayType == "onbetaald" && $type !== 'workday') {
            $timesheet->update([
                'accountableHours' => 0,
                'type' => $type,
            ]);
            $fetchUserMonthTotal = UserUtility::CheckUserMonthTotal($date, $id);
            $calculateUserMonthTotal = CalculateUtility::calculateUserTotal($id);
            if ($fetchUserMonthTotal && $calculateUserMonthTotal) {
                $postData = [
                    'worker' => $id,
                ];

                return redirect()->route('getData', $postData);
            } else {
                $postData = [
                    'worker' => $id,
                ];

                return redirect()->route('getData', $postData)->with('error', 'Er ging iets mis, kijk even na of de dag in het uurrooster is aangepast.');
            }
        } elseif ($type !== 'workday') {
            $timesheet->update([
                'accountableHours' => $companyDayHours,
                'type' => $type,
            ]);
            $fetchUserMonthTotal = UserUtility::CheckUserMonthTotal($date, $id);
            $calculateUserMonthTotal = CalculateUtility::calculateUserTotal($id);
            if ($fetchUserMonthTotal && $calculateUserMonthTotal) { {
                    $postData = [
                        'worker' => $id,
                    ];

                    return redirect()->route('getData', $postData);
                }
            } else {
                $postData = [
                    'worker' => $id,
                ];

                return redirect()->route('getData', $postData)->with('error', 'Er ging iets mis, kijk even na of de dag in het uurrooster is aangepast.');
            }
        } else {
            $userRow = (object) [
                'UserId' => $id,
                'StartWork' => Carbon::parse($request->startDate . ' ' . $request->startTime, 'Europe/Brussels'),
                'StopWork' => Carbon::parse($request->endDate . ' ' . $request->endTime, 'Europe/Brussels'),
                'StartBreak' => Carbon::parse($date->format('Y-m-d') . ' ' . $request->startBreak, 'Europe/Brussels'),
                'EndBreak' => Carbon::parse($date->format('Y-m-d') . ' ' . $request->endBreak, 'Europe/Brussels'),
                'Weekend' => $weekend,
                'userNote' => $userNote ?? null,
            ];
            $timeloggingUtility = new TimeloggingUtility;
            $addTimesheet = $timeloggingUtility->logTimeEntry($userRow, $id, $timesheet);
            $usedDayTotal = UserUtility::userDayTotalFetch($request->usedDayTotalDate, $id);
            if ($request->usedDayTotalDate && !DateUtility::checkIfSameDay(Carbon::parse($request->usedDayTotalDate), Carbon::parse($request->startDate))) {
                if ($usedDayTotal->timesheets()->where('Month', Carbon::parse($request->usedDayTotalDate))->exists()) {
                    UserUtility::updateAllUsersDayTotals(User::find($id)->company_code);
                    return redirect('/')->with('success', 'Dag is aangepast');
                }
                $usedDayTotal->delete();
            }

            $updateAllTotals = UserUtility::updateAllUsersDayTotals(User::find($id)->company_code);

            if ($addTimesheet && $updateAllTotals) return redirect('/')->with('success', 'Dag is aangepast');
        }
    }
}
