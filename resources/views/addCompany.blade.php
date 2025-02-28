@extends('layout')
@php
    $isLoading = false
@endphp
@section('title')
    <h2>Company Registration</h2>
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

    <form class='form'name="newUserForm" action="{{ route('addCompany') }}" method="post" onsubmit="showLoader()">

        @csrf
        <label class="nameLabel" for="companyName">Company name</label> <br>
        <input id="companyName" class="name" type="text" name="companyName"> <br> 

        <label for="adminName">Admin name</label> <br>
        <input type="text" name='adminName' id="adminName"> <br>
      

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
