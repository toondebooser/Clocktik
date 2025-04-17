<?php

namespace App\Utilities;

use Carbon\Carbon;

class DateUtility
{

   public static function carbonParse($date)
   {
      $parsedDate = Carbon::parse( $date, 'Europe/Brussels');
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
      if ($weekdayNr == $company->weekend_day_1 || $weekdayNr == $company->weekend_day_2){
         return true;
      }else{
         return false;
      }
   }
   public static function checkIfSameDay($in, $out)
   {
      return DateUtility::carbonParse($in)->isSameDay(DateUtility::carbonParse($out));
   }
   public static function checkNightShift($timestamp)
   {
       $time = Carbon::parse($timestamp)->format('H:i');
       return $time >= '20:00' || $time < '06:00';
   }
}
