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
@section('content')

    <form class="newUserForm" name="newUserForm" action="{{ route('addCompany') }}" method="post" onsubmit="showLoader()">

        @csrf
        <label class="nameLabel" for="name">Company name</label>
        <input id="name" class="name" type="text" name="name">

      

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
