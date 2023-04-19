@extends('layout')
@section('title')
    <h1>{{ auth()->user()->name }}</h1>
@endsection
@section('userDashboard')
    <a href="">Update personal info</a>
    <div class="profileContent">
        <div class="timesheetHeader"></div>
            <p>Date</p>
            <p class="clockedIn">Clocked in</p>
            <p class="clockedOut">Clocked out</p>
            <p class="breakStarted">Break started</p>
            <p class="breakEnded">Break ended</p>

        @if (isset($timesheet))
            @foreach ($timesheet as $item)
                <p class="date" id="{{ $item->id }}">
                    {{ date('l', strtotime($item->ClockedIn)) ." ". date('d', strtotime($item->ClockedIn))  }}
                </p>
            @endforeach
        @else
            <p class="text-danger">No data for this month</p>
        @endif
    </div>

@endsection
