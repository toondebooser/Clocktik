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
        ]);
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }


        $data = $request->only('name', 'company', 'email');

        Mail::raw("Nieuwe aanvraag voor subscribtie:\n\nName: {$data['name']}\nCompany: {$data['company']}\nEmail: {$data['email']}", function ($message) use ($data) {
            $message->to('taxus.work@gmail.com')
                    ->subject('Nieuwe subscriptie aanvraag werkuren.be');
        });

        return back()->with('success', 'Bedankt voor je aanvraag, We mailen jou spoedig met je inlog gegevens');
    }
}
