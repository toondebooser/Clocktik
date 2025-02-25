@extends('layout')
@section('title')
@if (session()->has('success'))
<div class="success">
 {{session('success')}}
</div> 
@endif
    <h1>Tiktrack</h1>
    <a href="{{ route('dashboard') }}" class="logoContainer">
            <img class="logo" src="{{ asset('images/Taxus logo perfect round.png') }}"
                alt="hmm there should be a alogo here">
    </a>
    <p style="grid-column: 1/13; justify-self:center; font-size:large">1.4.10</p>
@endsection
