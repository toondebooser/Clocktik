@extends('layout')
@section('title')
@if (session()->has('success'))
<div class="success">
 {{session('success')}}
</div> 
@endif
    <h1>Clocktik</h1>
    <a href="{{ route('dashboard') }}" class="logoContainer">
            <img class="logo" src="{{ asset('images/95090418_1090903184610004_7235939885578715136_n.png') }}"
                alt="hmm there should be a alogo here">
    </a>
    <p style="grid-column: 1/13; justify-self:center; font-size:large">1.3.9</p>
@endsection
