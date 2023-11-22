<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AddCustomTimesheetController extends Controller
{
    public function customTimesheetForm(Request $request)
    {
        $id = $request->input('worker');
        $worker = User::where('id', $id)->first();
        return view('addTimesheet', ['id' => $id, 'worker' => $worker]);
    }
}
