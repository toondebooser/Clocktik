@extends('layout')
@section('content')
    <h2>Update rooster van: {{ $worker->name }}</h2>
    @php
            $startShift = \Carbon\Carbon::parse($timesheet->ClockedIn)->format('H:i');
            $endShift = \Carbon\Carbon::parse($timesheet->ClockedOut)->format('H:i');
            $startBreak = $timesheet->StartBreak ? \Carbon\Carbon::parse($timesheet->StartBreak)->format('H:i') : null;
            $endBreak = $timesheet->EndBreak ? \Carbon\Carbon::parse($timesheet->EndBreak)->format('H:i') : null;
            
            @endphp
            <form action="{{route('updateTimesheet')}}" class="updateTimesheet" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{$worker->id}}">
                <input type="hidden" name="timesheet" value="{{$timesheet->id}}">
        <fieldset>
            <legend>Gewerkte periode</legend>
            <div>
                <label for="startTime">Start:</label>
                <input class="updateStartTime" name="startTime" type="time" value="{{ $startShift }}">
                <label for="endTime">End:</label>
                <input class="updateEndTime" type="time" name="endTime" value="{{ $endShift }}">
            </div>
        </fieldset>
            <legend>Gepauzeerde periode</legend>
            <div>
                <label for="startBreak">Start:</label>
                <input class="updateStartBreak" type="time" name="startBreak" value="">
                <label for="endBreak">End:</label>
                <input type="time" name="endBreak" class="updateEndBreak" value="">
            </div>
        </fieldset>
        <input class="userNoteSubmit" type="submit" value="update">

    </form>
@endsection
