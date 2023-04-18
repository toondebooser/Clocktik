@extends('layout')
@section('title')
    <h2>Welcome {{$user->name}}</h2>
@endsection

@section('userDashboard')
@if ($shiftStatus == 0)
<a href="{{route('start')}}" class="startButton">
    <p class="buttonText">Start working</p>
</a> 
@endif
{{$shiftStatus}}

    @if (isset($start))
    <p class="startTime">{{$start}}</p>
        
    @endif
@endsection