@extends('layout')
@section('title')

{{-- @php
    use Spatie\Holidays\Holidays;
    use Spatie\Holidays\Countries\Belgium;
    use Carbon\Carbon;


    $holidays = Holidays::for('be')->getInRange(Carbon::now()->startOfMonth(),Carbon::now()->endOfmonth() );
dd($holidays);
@endphp --}}
    <h1>Tiktrack</h1>
    <a href="{{ route('dashboard') }}" class="logoContainer">
            <img class="logo" src="{{ auth()->check() && auth()->user()->company && auth()->user()->company->company_logo ? asset(auth()->user()->company->company_logo) : asset('images/TaxusLogo.png') }}"
            alt="Company Logo">
    </a>
    <p style="grid-column: 1/13; justify-self:center; font-size:large">2.0.0</p>
@endsection
