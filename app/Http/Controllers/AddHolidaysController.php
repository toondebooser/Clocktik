<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Utilities\CalculateUtility;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AddHolidaysController extends Controller
{



    public static function addHolidays(Request $request)
    {
        $holidaysEntryObject = self::convertRequestToEntry($request);
        $company = Company::where('company_code', $request->company_code)->first();
        $errors = [];
        foreach ($company->users as  $worker) {
            if ($worker['admin'] === true && $company->admin_timeclock === false) {
                continue;
            }
            foreach ($holidaysEntryObject as $holiday) {
                $result = TimesheetController::setDay($holiday['name'], 'Betaald', $worker['id'], $holiday['date']);
                CalculateUtility::calculateUserTotal($worker['id']);
                if ($result !== true) {
                    // Skip if error message already exists in $errors
                    if (!in_array(['error', $result], $errors, true)) {
                        array_push($errors, ['error', $result]);
                    }}
            }
        }
        if (!empty($errors)) return redirect()->back()->withErrors($errors);
        return redirect()->back()->with('success', 'Feestdagen succesvol toegevoegd voor al de werknemers');
    }

    public static function convertRequestToEntry($request)
    {
        $processedRequest = collect($request->all())->map(function ($date, $name) {
            if (in_array($name, ['_token', 'company_code'])) {
                return null;
            }
            return [
                'name' => str_replace('_', ' ', $name),
                'date' => Carbon::parse($date),
            ];
        })->filter()->values()->toArray();
        return $processedRequest;
    }
}
