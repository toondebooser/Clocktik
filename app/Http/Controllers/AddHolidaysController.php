<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AddHolidaysController extends Controller
{
    public static function addHolidays (Request $request)
    {
        dd($request->all());
    }
}
