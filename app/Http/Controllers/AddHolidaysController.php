<?php

namespace App\Http\Controllers;

use App\Helpers\UserActivityLogger;
use App\Models\Company;
use App\Models\User;
use App\Utilities\CalculateUtility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddHolidaysController extends Controller
{
    public static function addHolidays(Request $request)
    {
        try {
            $holidaysEntryObject = self::convertRequestToEntry($request);
            $company = Company::where('company_code', $request->company_code)->first();

            if (!$company) {
                return redirect()->back()->withErrors('error', 'Bedrijf niet gevonden.');
            }

            $errors = [];
            DB::transaction(function () use ($company, $holidaysEntryObject, &$errors) {
                foreach ($company->users as $worker) {
                    if ($worker['admin'] === true && $company->admin_timeclock === false) {
                        continue;
                    }
                    foreach ($holidaysEntryObject as $holiday) {
                        $result = TimesheetController::setDay($holiday['name'], 'Betaald', $worker['id'], $holiday['date']);
                        CalculateUtility::calculateUserTotal($worker['id']);
                        if ($result !== true) {
                            if (!in_array(['error', $result], $errors, true)) {
                                array_push($errors, ['error', $result]);
                            }
                        }
                    }
                }
            });

            if (!empty($errors)) {
                return redirect()->back()->withErrors('error', 'Er zijn fouten opgetreden bij het toevoegen van feestdagen.');
            }

            // Log success
            UserActivityLogger::log('Holidays added successfully', [
                'company_code' => $request->company_code,
                'user_id' => auth()->user()->id ?? null,
                'holidays_count' => count($holidaysEntryObject),
            ]);

            return redirect()->back()->with('success', 'Feestdagen succesvol toegevoegd voor al de werknemers');
        } catch (QueryException $e) {
            Log::error('Failed to add holidays', [
                'company_code' => $request->company_code,
                'user_id' => auth()->user()->id ?? null,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()->withErrors('error', 'Er is een fout opgetreden bij het toevoegen van feestdagen.');
        }
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