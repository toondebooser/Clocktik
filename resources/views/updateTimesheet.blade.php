@extends('layout')
@section('content')
    <style>
        body {
            background-color: {{ $nightShift ? 'var(--night-mode)' : 'white' }};
            color: {{ $nightShift ? 'white' : 'black' }};
        }
    </style>
    <h2>Update rooster van: {{ $worker->name }}</h2>
    @php
        if ($timesheet === null) {
            header('Location: /my-workers');
            exit();
        }
    @endphp

    <div class="formContainer">
        <h3>{{ $nightShift ? 'Nacht shift' : $monthString }}</h3>
        <form action="{{ route('updateTimesheet') }}" class="updateTimesheet" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{ $worker->id }}">
            <input type="hidden" name="timesheet" value="{{ $timesheet->id }}">
            <input type="hidden" name="usedDayTotalId" value="{{ $usedDayTotalId }}">
            <input type="hidden" name="usedDayTotalDate" value="{{ $usedDayTotalDate }}">
            <input type="hidden" name="userNote" value="{{ $timesheet->userNote }}">
            <input type="hidden" name="type" value="{{ $timesheet->type }}">
            <input type="hidden" name="BreaksTaken" value="{{ $timesheet->BreaksTaken }}">
            @if ($timesheet->type == 'workday')
                @if ($startShift )
                    <fieldset class="periode" style="{{ $nightShift ? 'display: block;' : 'display: none;' }}">
                        <legend>Datums</legend>
                        <input style="width: 120px" class="updateDateTime" name="startDate" type="date"
                            value="{{ $startDate }}">
                        <input style="width: 120px" class="updateDateTime" name="endDate" type="date"
                            value="{{ $endDate }}">

                    </fieldset>
                    <fieldset class="gewerkt">
                        <legend>Gewerkt</legend>
                        <div>
                            <label for="startTime">Start:</label>
                            <input id="startTime" class="updateDateTime" name="startTime" type="time"
                                value="{{ $startShift }}">
                            <br><label for="endTime">End:</label>
                            <input id="endTime" class="updateDateTime" type="time" name="endTime"
                                value="{{ $endShift }}">
                        </div>
                    </fieldset>
                @else
                    <input type="hidden" name="startTime" value="{{ null }}">
                    <input type="hidden" name="endTime" value="{{ null }}">
                @endif
                @if ($startBreak )
                    <fieldset class="gepauzeerd">
                        <legend>Gepauzeerd</legend>

                        <div>
                            <label for="startBreak">Start:</label>
                            <input id="startBreak" class="updateDateTime" type="time" name="startBreak"
                                value="{{ $startBreak }}"> <br>
                            <label for="endBreak">End:</label>
                            <input id="endBreak" type="time" name="endBreak" class="updateDateTime"
                                value="{{ $endBreak }}">
                            <br>
                        </div>
                    </fieldset>
                @endif
            @elseif(isset($timesheet->type) && $timesheet->type !== 'workday')
                <div class="specialUpdateContainer">
                    <input class="updateSpecialInput" type="text" name="updateSpecial" value="{{ $timesheet->type }}" @readonly($timesheet->official_holiday)>
                    <br>
                    <span class="radioInput">
                        <label for="betaaldInput" class="checkboxContainer">
                            <input @if ($timesheet->accountableHours == $worker->company->day_hours) {{ 'checked' }} @endif type="radio"
                                class="radioBox" id="betaaldInput" name="dayType" value="betaald">
                            <span class="labelAndere">Betaald</span>
                            <br>
                            <span class="checkMark"></span>
                        </label>
                        <label for="onbetaaldInput" class="checkboxContainer" id="onbetaald">
                            <input @if ($timesheet->accountableHours == 0) {{ 'checked' }} @endif type="radio"
                                class="radioBox" id="onbetaaldInput" name="dayType" value="onbetaald">
                            <span class="labelAndere">Onbetaald</span>
                            <br>
                            <span class="checkMark"></span>
                        </label>
                    </span>
                </div>
                @endif
                <input class="updateTimesheetSubmit button" type="submit" value="update">
         

        </form>
    </div>
    <br>
    <form action="{{ route('delete') }}" class="delete" method="POST">
        @csrf
        <input type="hidden" name="workerId" value="{{ $worker->id }}">
        {{-- <input type="hidden" name="type" value="{{$timesheet->type}}"> --}}
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
