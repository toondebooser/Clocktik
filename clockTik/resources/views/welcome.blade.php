@extends('layout')
@section('title')
    <h1>ClockTik</h1>
@endsection

@section('login')
    <div class="logincontainer">
        <form class="loginForm" action="" method="post">
            @csrf
            <label class="userNameLabel" for="userName">Email adress</label>
            <input class="userName" type="email" name="userName" >
            <label class="passLabel" for="password">Password</label>
            <input class="pass" type="password" name="password">
            <input class="loginButton" type="submit" value="Login">
        </form>
    </div>
@endsection