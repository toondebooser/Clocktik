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
@section('content')

    <form class="newUserForm" name="newUserForm" action="{{ route('registrate') }}" method="post" onsubmit="showLoader()">

        @csrf

        <input id="name" class="name uniform-input" type="text" name="name" placeholder="Name">
        

    

        <input id="email_adress" class="email uniform-input" type="email" name="email" placeholder="Email">

        <input id="company_code" class="company_code_input uniform-input" type="number" name="company_code" placeholder="Bedrijfscode">
       

        <input id="password"  class="password uniform-input" type="password" name="password" placeholder="Passwoord">
       
        <input  class="passwordConfirmation uniform-input" type="password" name="password_confirmation" placeholder="Herhaal passwoord">

      

        <input class="registrationButton" type="submit" value="Registreer" >
        
        <div class="loader" style="display: {{$isLoading ? 'flex' : 'none'}}"></div>
    </form>
    <script>
        function showLoader() {
            const loader = document.querySelector('.loader');
            loader.style.display = 'flex';
        }
    </script>
    @endsection
