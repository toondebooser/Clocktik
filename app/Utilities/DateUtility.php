<?php

namespace App\Utilities;

use App\Models\Company;
use Carbon\Carbon;
use Spatie\Holidays\Holidays;
use Spatie\Holidays\Countries\Belgium;

class DateUtility
{

   public static function carbonParse($date)
   {
      $parsedDate = Carbon::parse($date, 'Europe/Brussels');
      return $parsedDate;
   }
   public static function getWeekdayNumber($dutchWeekDay)
   {

      $dutchToCarbonNumber = [
         'maandag' => 1,
         'dinsdag' => 2,
         'woensdag' => 3,
         'donderdag' => 4,
         'vrijdag' => 5,
         'zaterdag' => 6,
         'zondag' => 0,
      ];

      $dutchWeekdayLowCase = trim(strtolower($dutchWeekDay));

      return $dutchToCarbonNumber[$dutchWeekdayLowCase];
   }

   public static function checkWeekend($date, $company_code)
   {
      $company = Company::where('company_code', $company_code)->first();
      $weekdayNr = Carbon::parse($date)->weekday();
      return $weekdayNr == $company->weekend_day_1 || $weekdayNr == $company->weekend_day_2;
   }

   public static function checkIfSameDay($in, $out)
   {
      return DateUtility::carbonParse($in)->isSameDay(DateUtility::carbonParse($out));
   }
   public static function checkHolidaysInMonth($date)
   {
      $start = $date->startOfMonth()->format('Y-m-d');
      $end = $date->endOfMonth()->format('Y-m-d');

      $holidays = Holidays::for('be')->getInRange($start, $end);

      $processed = [];
      foreach ($holidays as $date => $name) {
         $processed[] = [
            'date' => $date,
            'name' => $name,
            'weekend' => self::getWeekdayNumber(Carbon::parse($date)->locale('nl')->isoFormat('dddd')),
         ];
      }

      return $processed;
   }
   public static function isValidHolidayName($holidayName)
{
    $holidays = Holidays::for('be')->get();
    $holidayNames = array_column($holidays, 'name'); 
    $normalizedInputName = str_replace('_', ' ', $holidayName); 
    return in_array($normalizedInputName, $holidayNames);
}
   public static function checkNightShift($timestamp)
   {
      $time = Carbon::parse($timestamp)->format('H:i');
      return $time >= '20:00' || $time < '06:00';
   }
   public static function updateDayTotalFlags($timesheets, $summary)
   {
      $hasOverlap = false;
      $hasNightShift = false;

      foreach ($timesheets as $timesheet) {
         if (!DateUtility::checkIfSameDay($timesheet->ClockedIn, $timesheet->ClockedOut)) {
            $hasOverlap = true;
            $hasNightShift = true;
         }

         if (
            DateUtility::checkNightShift($timesheet->ClockedIn) ||
            DateUtility::checkNightShift($timesheet->ClockedOut)
         ) {
            $hasNightShift = true;
         }

         if ($hasOverlap && $hasNightShift) {
            break;
         }
      }

      $summary['DayOverlap'] = $hasOverlap;
      $summary['NightShift'] = $hasNightShift;

      return $summary;
   }
}
