@extends('layout')
@section('title')
    <h1>ClockTik</h1>
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
        <form class="loginForm" action="{{route('login')}}" method="post">
            @csrf
            <label class="userNameLabel" for="userName">Email adress</label>
            <input class="userName" type="email" name="userName" >
            <label class="passLabel" for="password">Password</label>
            <input class="pass" type="password" name="password">
            <input class="loginButton" type="submit" value="Login">
        </form>
    </div>
@endsection