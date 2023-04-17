@extends('layout')
@section('title')
    <h2>Welcome {{$user->name}}</h2>
@endsection

@section('userDashboard')
   <a href="" class="startButton">
    <p class="buttonText">Start working</p>
    </a> 
@endsection