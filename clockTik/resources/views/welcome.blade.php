@extends('layout')
<?php $currentUser = auth()->user();?>
@section('title')
    <h1>ClockTik</h1>
    @if (isset($currentUser))
    <p>{{$currentUser->name}}</p>   
    @endif
@endsection

@section('error')
{{-- <div class="errorBox">

    @error('email')
    <p id='errEmail' class="text-danger">{{ $message }}</p>
    @enderror
    
    @error('password')
    <div class="text-danger">{{ $message }}</div>
    @enderror
</div> --}}
   
@if ($errors->any())
<div class="loginError">
    <ul>
@foreach ($errors->all() as $error)
    <li>{{$error}}</li>
@endforeach  
    </ul>  
</div>    
@endif
@endsection
@section('login')
    <div class="logincontainer">
        <form class="loginForm" action="{{route('authentication')}}" method="post">
            @csrf
            <label class="userNameLabel" for="email">Email adress</label>
            <input class="userName" type="email" name="email" >
            <label class="passLabel" for="password">Password</label>
            <input class="pass" type="password" name="password">
            <input class="loginButton" type="submit" value="Login">
            <a class='registerLink'href="{{ route('newUser') }}">Register</a>
        </form>
    </div>
@endsection