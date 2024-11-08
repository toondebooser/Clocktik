@extends('layout')
@php
    $isLoading = null
@endphp
@section('title')
    <h2>User Registration</h2>
    @if (isset($exists))
        <p class="emailExists">{{$exists}}</p>
    @endif
@endsection

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

    <form class="newUserForm" name="newUserForm" action="{{ route('registrate') }}" method="post" onsubmit="showLoader()">

        @csrf
        <label class="nameLabel" for="name">Name</label>
        <input id="name" class="name" type="text" name="name">
        

    

        <label class="emailLabel" for="email">Email</label>
        <input id="email_adress" class="email" type="email" name="email">

       

        <label class="passwordLabel" for="password">Password</label>
        <input id="password"  class="password" type="password" name="password">
       
        <label class="passwordConfirmationLabel" for="password_confirmation">Repeat password</label>
        <input  class="passwordConfirmation" type="password" name="password_confirmation">

      

        <input class="registrationButton" type="submit" value="Registrate" >
        
        <div class="loader" style="display: {{$isLoading ? 'flex' : 'none'}}"></div>
    </form>
    <script>
        function showLoader() {
            const loader = document.querySelector('.loader');
            loader.style.display = 'flex';
        }
    </script>
    @endsection
