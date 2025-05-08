@extends('layout')
@section('title')


        <h1 style="margin-top: 100px">Tiktrack</h1>
        <a href="{{ route('pricing') }}" class="logoContainer">
                <img class="logo" src="{{ auth()->check() && auth()->user()->company && auth()->user()->company->company_logo ? asset(auth()->user()->company->company_logo) : asset('images/TaxusLogo.png') }}"
                alt="Company Logo">
        </a>
        <p style="grid-column: 1/13; grid-row: 5/4; align-self:end; justify-self:center; font-size:large">2.0.0</p>
        <a href="{{route('pricing')}}" class="button" style="grid-row:3/4; grid-column:1/13; justify-self:center; height:fit-content; align-self:end ">Start vandaag</a>
        
@endsection
