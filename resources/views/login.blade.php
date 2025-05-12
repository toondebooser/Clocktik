@extends('layout')
<?php $currentUser = auth()->user();?>
@section('title')
    <h1>Werkuren</h1>
 
@endsection




@section('content')
        <form class="loginForm" action="{{route('authentication')}}" method="post">
            @csrf
            <label class="userNameLabel" for="email">Email adress</label>
            <input class="userName uniform-input"  type="email" id='email' name="email" >
            <label class="passLabel" for="password">Password</label>
            <input class="pass uniform-input" type="password" id="password" name="password">
            <input class="loginButton" type="submit" value="Login">
            <a class='registerLink'href="{{ route('registration-form') }}">Register</a>
            <label class="rememberLabel" for="remember">Remember me</label>
            <input id="remember" type="checkbox" name="remember" class="remeberCheckbox">
            <a class="forgotPassword" href="{{route('password.request')}}">Wachtwoord vergeten?</a>
        </form>
    
@endsection