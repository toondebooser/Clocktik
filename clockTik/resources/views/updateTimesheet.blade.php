@extends('layout')
@section('content')
    <h2>Update rooster van: {{ $worker->name }}</h2>
    @php
        $startShift = \Carbon\Carbon::parse($timesheet->ClockedIn)->format('H:i');
        $endShift = \Carbon\Carbon::parse($timesheet->ClockedOut)->format('H:i');
        $startBreak = $timesheet->BreakStart ? \Carbon\Carbon::parse($timesheet->BreakStart)->format('H:i') : null;
        $endBreak = $timesheet->BreakStop ? \Carbon\Carbon::parse($timesheet->BreakStop)->format('H:i') : null;
    @endphp
    <div class="formContainer">
        <h3>{{ \Carbon\Carbon::parse($timesheet->Month)->format('d/m/Y') }}</h3>
        @if ($timesheet->type == 'workday')
            <form  action="{{ route('updateTimesheet') }}" class="updateTimesheet" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $worker->id }}">
                <input type="hidden" name="timesheet" value="{{ $timesheet->id }}">
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
                    <input class="updateStartBreak" type="time" name="startBreak" value="{{ $startBreak }}">
                    <label for="endBreak">End:</label>
                    <input type="time" name="endBreak" class="updateEndBreak" value="{{ $endBreak }}">
                </div>
                </fieldset>
                <input class="userNoteSubmit" type="submit" value="update">
            </form>
        @endif
    </div>
    @if ($timesheet->userNote !== null)
        <fieldset class="userNoteContainer">
            <div><b>Notitie:</b></div>
            <div class="userNote">{{ $timesheet->userNote }}</div>
        </fieldset>
    @endif
@endsection
