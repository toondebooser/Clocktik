@extends('layout')
@section('title')
<h1>{{auth()->user()->name}}</h1>
    
@endsection
@section('userDashboard')
<div class="profileContent">
    {{$timesheet}}
</div>
    
@endsection