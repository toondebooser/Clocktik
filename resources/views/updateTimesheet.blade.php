@extends('layout')
@section('content')
    <h2>Update rooster van: {{ $worker->name }}</h2>
    @php
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
                <input class="updateSpecialInput" type="text" name="updateSpecial" value="{{$timesheet->type}}">
                <br>
                <span class="radioInput">
                    <label for="betaaldInput"  class="checkboxContainer" >
                        <input @if ($timesheet->accountableHours == 7.6) {{"checked"}} @endif type="radio" class="radioBox" id="betaaldInput" name="dayType" value="betaald" >
                        <span class="labelAndere">Betaald</span>
                    <br>
                    <span class="checkMark"></span>
                </label>         
                <label for="onbetaaldInput"  class="checkboxContainer" id="onbetaald">
                    <input @if ($timesheet->accountableHours == 0) {{"checked"}} @endif type="radio" class="radioBox" id="onbetaaldInput" name="dayType" value="onbetaald" >
                    <span class="labelAndere">Onbetaald</span>
                    <br>
                    <span class="checkMark"></span>                      
                </label> 
            </span>
            @endif
            <input class="updateTimesheetSubmit button" type="submit" value="update">
          
        </form>
    </div>
    <br>
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
