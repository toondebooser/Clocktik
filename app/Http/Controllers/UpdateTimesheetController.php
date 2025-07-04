<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Daytotal;
use App\Models\ExtraBreakSlot;
use Illuminate\Support\Carbon;
use App\Utilities\CalculateUtility;
use App\Utilities\DateUtility;
use App\Utilities\TimeloggingUtility;
use App\Utilities\UserUtility;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateTimesheetController extends Controller
{

    public function updateForm($id, $timesheet, $type = null, $usedDayTotalId = null, $usedDayTotalDate = null)
    {
        $worker = User::find($id);
        switch ($type) {
            case 'timesheet':
                $timesheet = Timesheet::find($timesheet);
                $nightShift = UserUtility::userDayTotalFetch($timesheet->Month, $id)->NightShift;
                $endDate = Carbon::parse($timesheet->ClockedOut)->format('Y-m-d');
                $startDate = Carbon::parse($timesheet->ClockedIn)->format('Y-m-d');
                $usedDayTotalDate = Carbon::parse($usedDayTotalDate)->format('Y-m-d');
                break;
            case 'extraBreakSlot':
                $timesheet = ExtraBreakSlot::find($timesheet);
                $nightShift = null;
                $endDate = null;
                $startDate = null;
                $usedDayTotalDate = Carbon::parse($usedDayTotalDate)->format('Y-m-d');
                break;
            case false:
                $timesheet = Daytotal::find($timesheet);
                $nightShift = null;
                $endDate = null;
                $startDate = null;
                break;
        }
        if ($timesheet === null) {
            $postData = [
                'worker' => $id,
            ];

            return redirect()->route('getData', $postData)->with('error', $worker->name . ' heeft juist ingeklokt. ');
        }
        $startShift = $timesheet->ClockedIn ? Carbon::parse($timesheet->ClockedIn)->format('H:i') : null;
        $endShift = $timesheet->ClockedOut ? Carbon::parse($timesheet->ClockedOut)->format('H:i') : null;
        $startBreak = $timesheet->BreakStart ? Carbon::parse($timesheet->BreakStart)->format('H:i') : null;
        $endBreak = $timesheet->BreakStop ? Carbon::parse($timesheet->BreakStop)->format('H:i') : null;
        $monthString = $timesheet->Month->format('d/m/Y');

        return view('updateTimesheet', ['usedDayTotalId' => $usedDayTotalId, 'usedDayTotalDate' => $usedDayTotalDate, 'startDate' => $startDate, 'endDate' => $endDate, 'nightShift' => $nightShift, 'worker' => $worker, 'timesheet' => $timesheet, 'startShift' => $startShift, 'endShift' => $endShift, 'startBreak' => $startBreak, 'endBreak' => $endBreak, 'monthString' => $monthString]);
    }

    public function updateTimesheet(Request $request)
    {

        $dayType = $request->dayType;
        $id = $request->id;
        $companyDayHours = User::find($id)->company->day_hours;
        $timesheet = null;
        if ($request->type === 'workday' && $request->startTime !== null) {
            $timesheet = Timesheet::find($request->timesheet);
        } elseif ($request->type === 'workday' && $request->startTime === null) {
            $timesheet = ExtraBreakSlot::find($request->timesheet);
        } else {
           $timesheet = Daytotal::find($request->timesheet);
        }

        $type = $request->updateSpecial;
        $weekend = DateUtility::checkWeekend($timesheet->Month, User::find($id)->company->company_code);
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

                return redirect()->route('getData', $postData)->with('success', 'Dag is aangepast');
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

                    return redirect()->route('getData', $postData)->with('success', 'Dag is aangepast');
                }
            } else {
                $postData = [
                    'worker' => $id,
                ];

                return redirect()->route('getData', $postData)->with('error', 'Er ging iets mis, kijk even na of de dag in het uurrooster is aangepast.');
            }
        } else {
            $validator = Validator::make(
                $request->all(),
                [
                    'usedDayTotalDate' => 'required|date',
                    'startTime' => [
                        'nullable',
                        'date_format:H:i',
                        Rule::requiredIf(function () use ($request) {
                            return !empty($request->endTime);
                        }),
                    ],
                    'endTime' => [
                        'nullable',
                        'date_format:H:i',
                        Rule::requiredIf(function () use ($request) {
                            return !empty($request->startTime);
                        }),
                        Rule::when(
                            function () use ($request) {
                                $dayTotal = UserUtility::userDayTotalFetch($request->usedDayTotalDate, $request->id);
                                return !$dayTotal || !$dayTotal->NightShift;
                            },
                            'after:startTime'
                        ),
                    ],
                    'startBreak' => [
                        'nullable',
                        'date_format:H:i',
                        Rule::requiredIf(function () use ($request) {
                            return !empty($request->endBreak);
                        }),
                    ],
                    'endBreak' => [
                        'nullable',
                        'date_format:H:i',
                        Rule::requiredIf(function () use ($request) {
                            return !empty($request->startBreak);
                        }),
                        Rule::when(
                            function () use ($request) {
                                $dayTotal = UserUtility::userDayTotalFetch($request->usedDayTotalDate, auth()->user()->id);
                                return !$dayTotal || !$dayTotal->NightShift;
                            },
                            'after_or_equal:startBreak'
                        ),
                    ],
                    'timeslot_pair' => Rule::requiredIf(function () use ($request) {
                        return (
                            (is_null($request->startTime) || is_null($request->endTime)) &&
                            (is_null($request->startBreak) || is_null($request->endBreak))
                        );
                    }),
                ],
                [
                    'usedDayTotalDate.required' => 'De datum is verplicht.',
                    'usedDayTotalDate.date' => 'De datum moet een geldige datum zijn.',
                    'startTime.required_with' => 'Starttijd is verplicht als eindtijd is ingevuld.',
                    'startTime.date_format' => 'Starttijd moet in het formaat UU:MM zijn.',
                    'endTime.required_with' => 'Eindtijd is verplicht als starttijd is ingevuld.',
                    'endTime.date_format' => 'Eindtijd moet in het formaat UU:MM zijn.',
                    'endTime.after' => 'Eindtijd moet na starttijd liggen.',
                    'startBreak.required_with' => 'Pauzestart is verplicht als pauzeeinde is ingevuld.',
                    'startBreak.date_format' => 'Pauzestart moet in het formaat UU:MM zijn.',
                    'endBreak.required_with' => 'Pauzeeinde is verplicht als pauzestart is ingevuld.',
                    'endBreak.date_format' => 'Pauzeeinde moet in het formaat UU:MM zijn.',
                    'endBreak.after_or_equal' => 'Pauzeeinde moet gelijk aan of na pauzestart liggen.',
                    'timeslot_pair.required' => 'Ten minste één tijdsblok (werkuren of pauze) moet worden ingevuld.',
                ]
            );
            if ($validator->fails()) {
                return  redirect()->back()->withErrors($validator);
            }
            if ($request->startTime !== null) {
                $userRow = (object) [
                    'UserId' => $id,
                    'StartWork' => $request->startTime ? Carbon::parse($request->startDate . ' ' . $request->startTime, 'Europe/Brussels') : null,
                    'StopWork' => $request->endTime ? Carbon::parse($request->endDate . ' ' . $request->endTime, 'Europe/Brussels') : null,
                    'StartBreak' => $request->startBreak ?  Carbon::parse($date->format('Y-m-d') . ' ' . $request->startBreak, 'Europe/Brussels') : null,
                    'EndBreak' => $request->endBreak ? Carbon::parse($date->format('Y-m-d') . ' ' . $request->endBreak, 'Europe/Brussels') : null,
                    'Weekend' => $weekend,
                    'userNote' => $request->userNote ?? null,
                ];
                TimeloggingUtility::logTimeEntry($userRow, $id, $timesheet->id);
            } else {
                $extraBreakSlot = ExtraBreakSlot::find($request->timesheet);
                $extraBreakSlot->update([
                    'BreakStart' => $request->startBreak,
                    'BreakStop' => $request->endBreak
                ]);
            }

            $usedDayTotal = UserUtility::userDayTotalFetch($request->usedDayTotalDate, $id);
            if ($request->usedDayTotalDate && !DateUtility::checkIfSameDay(Carbon::parse($request->usedDayTotalDate), Carbon::parse($request->startDate))) {
                if ($usedDayTotal->timesheets()->where('Month', Carbon::parse($request->usedDayTotalDate))->exists()) {
                    UserUtility::updateAllUsersDayTotals(User::find($id)->company_code);
                    return redirect('/')->with('success', 'Dag is aangepast');
                }
                $usedDayTotal->delete();
            }

            $updateAllTotals = UserUtility::updateAllUsersDayTotals(User::find($id)->company_code);

            if ($updateAllTotals) return redirect()->back()->with('success', 'Dag is aangepast');
        }
    }
}
