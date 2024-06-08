<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConfirmAction extends Controller
{
    public function confirmAction(Request $request)
    {
        $request->session()->put('confirmed_action', true);
        return response()->json(['success' => true]);
    }
}
