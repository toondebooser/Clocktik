<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    public function showForm()
    {
        return view('subscriptieAanvraag');
    }

    public function send(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'email' => 'required|email',
            'btw'=> 'required|text',
            'adres' => 'required|text'
        ]);
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }


        $data = $request->only('name', 'company', 'email', 'btw', 'adres' );

       Mail::send('emailSubscription', ['data' => $data], function ($message) use ($data) {
    $message->to('taxus.work@gmail.com')
            ->subject('Nieuwe subscriptie aanvraag ->'. $data['company']);
});

        return back()->with('success', 'Bedankt voor je aanvraag, We mailen jou spoedig met je inlog gegevens');
    }
}
