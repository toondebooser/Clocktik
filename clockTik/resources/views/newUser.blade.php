@extends('layout')
@section('newUser')
    <form class="newUserForm" name="newUserForm" action="" method="post">

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
        <input class="password" type="password" name="password">

        <label class="passwordConfirmationLabel" for="passwordConfirmation">Repeat password</label>
        <input class="passwordConfirmation" type="password" name="passwordConfirmation">


    </form>
@endsection
