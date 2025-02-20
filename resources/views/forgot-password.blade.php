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
@section('login')
   
        <form class="loginForm" action="" method="post">
            @csrf
            <label class="userNameLabel" for="email">Email adress</label>
            <input class="userName" type="email" id='email' name="email" >
            <input class="loginButton" type="submit" value="send">
        </form>
    
@endsection