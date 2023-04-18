@extends('layout')
@section('title')
    <h2>Welcome {{$user->name}}</h2>
@endsection

@section('userDashboard')
@if ($shiftStatus == false)
<a href="{{route('start')}}" class="startButton">
    <p class="buttonText">Start</p>
</a> 
{{-- @else 
<a href="{{route('break')}}" class="breakButton">
    <p class="buttonText">Break</p>
</a> 
<a href="{{route('stop')}}" class="stopButton">
    <p class="buttonText">Stop</p>
</a>  --}}
@endif
{{$shiftStatus}}

    @if (isset($start))
    <p class="startTime">{{$start}}</p>
        
    @endif
@endsection