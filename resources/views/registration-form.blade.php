@extends('layout')
@php
    $isLoading = false
@endphp
@section('title')
    <h2>User Registration</h2>
    @if (isset($exists))
        <p class="emailExists">{{$exists}}</p>
    @endif
@endsection

@section('error')
<div class="loginError">
    @if ($errors->any())
    <div class="text-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
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
