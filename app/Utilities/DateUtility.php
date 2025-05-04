<?php

namespace App\Utilities;

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

   public static function checkWeekend($date, $company)
   {
      $weekdayNr = Carbon::parse($date)->weekday();
      if ($weekdayNr == $company->weekend_day_1 || $weekdayNr == $company->weekend_day_2) {
         return true;
      } else {
         return false;
      }
   }
   public static function checkIfSameDay($in, $out)
   {
      return DateUtility::carbonParse($in)->isSameDay(DateUtility::carbonParse($out));
   }
   public static function checkHolidaysInMonth()
   {

      $holidaysCheck = Holidays::for('be')->getInRange(now('Europe/Brussels')->startOfMonth()->format('Y-m-d'), now('Europe/Brussels')->endOfMonth()->format('Y-m-d'));

      return $holidaysCheck;
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
