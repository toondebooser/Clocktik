@extends('layout')
@section('content')
    <h2>Update rooster van: {{ $worker->name }}</h2>
    <form action="" class="updateTimesheet">
        @php
            $startShift = \Carbon\Carbon::parse($timesheet->ClockedIn)->format('H:i');
            $endShift = \Carbon\Carbon::parse($timesheet->ClockedOut)->format('H:i');
            $startBreak = $timesheet->StartBreak ? \Carbon\Carbon::parse($timesheet->StartBreak)->format('H:i') : null;
            $endBreak = $timesheet->EndBreak ? \Carbon\Carbon::parse($timesheet->EndBreak)->format('H:i') : null;
            
        @endphp
        <fieldset>
            <legend>Time</legend>
            <div>
                <label for="startTime">Start:</label>
                <input class="updateStartTime" name="startTime" type="time" value="{{ $startShift }}">
                <label for="endTime">End:</label>
                <input class="updateEndTime" type="time" name="endTime" value="{{ $endShift }}">
            </div>
        </fieldset>

        <fieldset>
            <legend>Break</legend>
            <div>
                <label for="startBreak">Start:</label>
                <input class="updateStartBreak" type="time" name="startBreak" value="">
                <label for="endBreak">End:</label>
                <input type="time" name="endBreak" class="updateEndBreak" value="">
            </div>
        </fieldset>
        <input type="submit" value="update">

    </form>
@endsection
