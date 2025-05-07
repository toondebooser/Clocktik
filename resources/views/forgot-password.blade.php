@extends('layout')
<?php $currentUser = auth()->user();?>
@section('title')
    <h1>Tiktrack</h1>
 
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
@endsection
@section('content')

        <form class="loginForm" action="{{route('password.request')}}" method="post">
            @csrf
            <label class="userNameLabel" for="email">Email adres    </label>
            <input class="userName uniform-input" type="email" id='email' name="email" >
            <input class="loginButton" type="submit" value="send">
        </form>
    
@endsection