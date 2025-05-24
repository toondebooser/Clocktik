@extends('layout')
@section('title')
    
    <h1 class="welcomeTitle fadeUpEffect" style="margin-top: 100px">Werkuren.be</h1>

    <a href="{{ route('pricing') }}" class="logoContainer">
        <img id="companyLogo" class="logo " style="opacity: 0"
            src="{{ auth()?->user()?->company?->company_logo ? asset(auth()->user()->company->company_logo) : asset('images/TaxusLogo.png') }}"
            alt="Company Logo">
    </a>

    @guest
    <div class="welcomCard fadeUpEffect">
        <p style="margin-bottom: 15px; font-size:small;">
            Registreer werktijden.<br>
            Snel & duidelijk.
        </p>
        <a href="{{ route('pricing') }}" class="authLinks button"
            style="grid-row:3/4; grid-column:1/13; justify-self:center; height:fit-content; align-self:end ">Start
            vandaag</a>
    </div>
    @endguest
@endsection
