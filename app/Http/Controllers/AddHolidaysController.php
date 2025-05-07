<?php

namespace App\Http\Controllers;

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
            return DB::transaction(function () use ($request) {
                $holidaysEntryObject = self::convertRequestToEntry($request);
                $company = Company::where('company_code', $request->company_code)->first();
                $errors = [];
                
                foreach ($company->users as $worker) {
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
                            }
                        }
                    }
                }
                
                if (!empty($errors)) {
                    return redirect()->back()->withErrors($errors);
                }
                
                return redirect()->back()->with('success', 'Feestdagen succesvol toegevoegd voor al de werknemers');
            });
        } catch (QueryException $e) {
            // Log error to laravel.log
            Log::error('Failed to add holidays', [
                'company_code' => $request->company_code,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return redirect()->back()->with('error', 'Er is een fout opgetreden bij het toevoegen van de feestdagen.');
        }
    }

    public static function convertRequestToEntry($request)
    {
        try {
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
        } catch (\Exception $e) {
            // Log error to laravel.log
            Log::error('Failed to convert holiday request to entry', [
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            throw $e; // Re-throw to be caught by addHolidays' try-catch
        }
    }
}