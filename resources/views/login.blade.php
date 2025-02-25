@extends('layout')
<?php $currentUser = auth()->user();?>
@section('title')
    <h1>ClockTik</h1>
 
@endsection

@section('error')

   
@if ($errors->any())
<div class="loginError">
    <ul>
@foreach ($errors->all() as $error)
    <li>{{$error}}</li>
@endforeach  
    </ul>  
</div>    
@endif
@if (session('status'))
<div class="success">{{session('status')}}</div>
@endif
@if (session()->has('verified'))
<div class="success">Thank you for verifying your email!</div>
@endif
@endsection
@section('login')
   
        <form class="loginForm" action="{{route('authentication')}}" method="post">
            @csrf
            <label class="userNameLabel" for="email">Email adress</label>
            <input class="userName" type="email" id='email' name="email" >
            <label class="passLabel" for="password">Password</label>
            <input class="pass" type="password" id="password" name="password">
            <input class="loginButton" type="submit" value="Login">
            <a class='registerLink'href="{{ route('registration-form') }}">Register</a>
            <label class="rememberLabel" for="remember">Remember me</label>
            <input id="remember" type="checkbox" name="remember" class="remeberCheckbox">
            <a class="forgotPassword" href="{{route('password.request')}}">Wachtwoord vergeten?</a>
        </form>
    
@endsection