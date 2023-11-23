@extends('layout')
@section('content')
    <h2>Update rooster van: {{ $worker->name }}</h2>
    @php
        $specialDays = ['Ziek', 'Weerverlet', 'Onbetaald verlof', 'Betaald verlof', 'Feestdag', 'Solicitatie verlof'];
        if ($timesheet === null) {
            header('Location: /my-workers');
            exit();
        }
    @endphp
    <div class="formContainer">
        <h3>{{ $monthString }}</h3>
        <form action="{{ route('updateTimesheet') }}" class="updateTimesheet" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{ $worker->id }}">
            <input type="hidden" name="timesheet" value="{{ $timesheet->id }}">
            <input type="hidden" name="type" value="{{ $timesheet->type }}">

            @if ($timesheet->type == 'workday')
                <fieldset>
                    <legend>Gewerkte periode</legend>
                    <div>
                        <label for="startTime">Start:</label>
                        <input id="startTime" class="updateStartTime" name="startTime" type="time" value="{{ $startShift }}"> <br>
                        <label for="endTime">End:</label>
                        <input id="endTime" class="updateEndTime" type="time" name="endTime" value="{{ $endShift }}"> <br>
                    </div>
                </fieldset>
                <hr>
                <legend>Gepauzeerde periode</legend>
                <div>
                    <label for="startBreak">Start:</label>
                    <input id="startBreak" class="updateStartBreak" type="time" name="startBreak" value="{{ $startBreak }}"> <br>
                    <label for="endBreak">End:</label>
                    <input id="endBreak" type="time" name="endBreak" class="updateEndBreak" value="{{ $endBreak }}"> <br>
                </div>
                </fieldset>
            @else
                @if(in_array($timesheet->type, $specialDays))
                <select name="updateSpecial" size="1">
                    @foreach ($specialDays as $specialDay)
                        <option value="{{ $specialDay }}" {{ $specialDay == $timesheet->type ? 'selected' : '' }}>
                            {{ $specialDay }}</option>
                    @endforeach
                </select>
                @else
                <input type="text" name="updateSpecial" value="{{$timesheet->type}}">
                @endif
            @endif
            <input class="updateTimesheetSubmit button" type="submit" value="update">
        </form>
    </div>
    <form action="{{ route('delete') }}" class="delete" method="POST">
        @csrf
        <input type="hidden" name="workerId" value="{{ $worker->id }}">
        <input type="hidden" name="deleteSheet" value="{{ $timesheet->id }}">
        <input type="hidden" name="date" value="{{ $timesheet->Month }}">
        <input onclick="return confirm('zedde zeker ?')" class="submit" type="image"
            src="{{ asset('/images/1843344.png') }}" name="deleteThisSheet" alt="Delete">
    </form>
    @if ($timesheet->userNote !== null)
        <fieldset class="userNoteContainer">
            <div><b>Notitie:</b></div>
            <div class="userNote">{{ $timesheet->userNote }}</div>
        </fieldset>
    @endif
@endsection
