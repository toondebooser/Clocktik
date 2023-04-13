@extends('layout')

@section('title')
    <h2>User Registration</h2>
@endsection
@section('newUser')
    <form class="newUserForm" name="newUserForm" action="{{ route('registrate') }}" method="post">

        @csrf
        <label class="nameLabel" for="name">Name</label>
        <input class="name" type="text" name="name">

        @error('name')
            <p id='errName' class="text-danger">{{ $message }}</p>
        @enderror

        <label class="emailLabel" for="email">Email</label>
        <input class="email" type="email" name="email">

        @error('email')
            <p id='errEmail' class="text-danger">{{ $message }}</p>
        @enderror

        <label class="passwordLabel" for="password">Password</label>
        <input id="password" class="password" type="password" name="password">

        @error('password')
            <div class="text-danger">{{ $message }}</div>
        @enderror

        <label class="passwordConfirmationLabel" for="password_confirmation">Repeat password</label>
        <input id="password_confirmation" class="passwordConfirmation" type="password" name="password_confirmation">

        @error('passwordConfirmation')
            <div class="text-danger">{{ $message }}</div>
        @enderror

        <input class="registrationButton" type="submit" value="Registrate">

    </form>
@endsection
