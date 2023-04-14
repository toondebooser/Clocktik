@extends('layout')

@section('title')
    <h4>User Registration</h4>
    @if (isset($exists))
        <p class="emailExists">{{$exists}}</p>
    @endif
@endsection

@section('error')
<div class="errorBox">
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
<div class="formContainer">
    <form class="newUserForm" name="newUserForm" action="{{ route('registrate') }}" method="post">

        @csrf
        <label class="nameLabel" for="name">Name</label>
        <input id="name" class="name" type="text" name="name">

    

        <label class="emailLabel" for="email">Email</label>
        <input id="email_adress" class="email" type="email" name="email">

       

        <label class="passwordLabel" for="password">Password</label>
        <input id="password"  class="password" type="password" name="password">
       
        <label class="passwordConfirmationLabel" for="password_confirmation">Repeat password</label>
        <input  class="passwordConfirmation" type="password" name="password_confirmation">

      

        <input class="registrationButton" type="submit" value="Registrate">

    </form>
</div>
@endsection
