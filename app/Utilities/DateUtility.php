<?php

namespace App\Utilities;

use Carbon\Carbon;

class DateUtility
{
    
   public static function carbonParse($date)
   {
    $parsedDate = Carbon::parse($date, 'Europe/Brussels');
    return $parsedDate;
   }

   public static function checkDayDiff($in, $out){
    return DateUtility::carbonParse($in)->isSameDay(DateUtility::carbonParse($out));
    
   }

}