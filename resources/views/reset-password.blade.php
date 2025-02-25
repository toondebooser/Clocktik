@extends('layout')
@php
    $isLoading = false
@endphp
@section('title')
    <h2>Reset password</h2>
    @if (isset($exists))
        <p class="emailExists">{{$exists}}</p>
    @endif
@endsection
@if (session('status'))
<div class="success">{{session('status')}}</div>
@endif
@section('error')
<div class="loginError">
    @error('name')
    <p id='errName' class="text-danger">{{ $message }}</p>
    @enderror
    
    @error('email')
    <p id='errEmail' class="text-danger">{{ $message }}</p>
    @enderror
    
    @error('password')
    <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
@endsection
@section('newUser')

    <form class="newUserForm" name="newUserForm" action="{{route('password.update')}}" method="post" onsubmit="showLoader()">

        @csrf
        <input type="hidden" name="token" value="{{$token}}">

        <label class="emailLabel" for="email">Email</label>
        <input id="email_adress" class="email" type="email" name="email">

        <label class="passwordLabel" for="password">Password</label>
        <input id="password"  class="password" type="password" name="password">
       
        <label class="passwordConfirmationLabel" for="password_confirmation">Repeat password</label>
        <input  class="passwordConfirmation" type="password" name="password_confirmation">

      

        <input class="registrationButton" type="submit" value="Reset" >
        
        <div class="loader" style="display: {{$isLoading ? 'flex' : 'none'}}"></div>
    </form>
    <script>
        function showLoader() {
            const loader = document.querySelector('.loader');
            loader.style.display = 'flex';
        }
    </script>
    @endsection
