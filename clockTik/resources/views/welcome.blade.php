@extends('layout')
@section('title')
<h1>Clocktik</h1>
    @section('logo')
    <a href="{{route('dashboard')}}" class="logoContainer">
        <div class="logo">
            <img src="build/images/android-chrome-512x512.png" alt="hmm there should be a alogo here">
        </div>
    </a>
    @endsection
@endsection