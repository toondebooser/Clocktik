<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class pricingController extends Controller
{
    
    public function pricingPage () {
        return view('pricing');
    }
}
