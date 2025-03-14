@extends('layout')
@section('title')
@if (session()->has('success'))
<div class="success">
 {{session('success')}}
 <a href="">ok</a>
</div> 
@endif
    <h1>Tiktrack</h1>
    <a href="{{ route('dashboard') }}" class="logoContainer">
            <img class="logo" src="{{ auth()->check() ? asset(auth()->user()->company->image) : asset('images/Taxus logo perfect round.png') }}"
            alt="Company Logo">
    </a>
    <p style="grid-column: 1/13; justify-self:center; font-size:large">2.0.0</p>
@endsection
