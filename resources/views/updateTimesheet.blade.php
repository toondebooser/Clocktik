@extends('layout')
@section('content')
    <style>
        body {
            background: {{ $nightShift ? 'var(--night-mode)' :  'linear-gradient(to bottom, #bfdbfe, #d1fae5, #fed7aa)' }};
            color: {{ $nightShift ? 'white' : 'black' }};
        }
    </style>
    <h2>{{ $worker->name }}</h2>
    @php
        if ($timesheet === null) {
            header('Location: /my-workers');
            exit();
        }
    @endphp

    <div class="formContainer">
        <h2>{{ $nightShift ? null : $monthString }}</h2>
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
                @if ($startShift)
                    <fieldset class="periode"
                        style="{{ $nightShift ? 'display: flex;' : 'display: none;' }} justify-content: space-around;">
                        <legend>Datums</legend>
                        <input style="width: 120px" class="updateDateTime uniform-input" name="startDate" type="date"
                            value="{{ $startDate }}">
                        <input style="width: 120px" class="updateDateTime uniform-input" name="endDate" type="date"
                            value="{{ $endDate }}">

                    </fieldset>
                    <fieldset class="gewerkt">
                        <legend>Gewerkt</legend>
                        <div>
                            <label for="startTime">Start:</label>
                            <input id="startTime" class="updateDateTime uniform-input" name="startTime" type="time"
                                value="{{ $startShift }}">
                            <br><label for="endTime">End:</label>
                            <input id="endTime" class="updateDateTime uniform-input" type="time" name="endTime"
                                value="{{ $endShift }}">
                        </div>
                    </fieldset>
                @else
                    <input type="hidden" name="startTime" value="{{ null }}">
                    <input type="hidden" name="endTime" value="{{ null }}">
                @endif

                <fieldset class="gepauzeerd">
                    <legend>Gepauzeerd</legend>

                    <div>
                        <label for="startBreak">Start:</label>
                        <input id="startBreak" class="updateDateTime uniform-input" type="time" name="startBreak"
                            value="{{ $startBreak }}"> <br>
                        <label for="endBreak">End:</label>
                        <input id="endBreak" type="time" name="endBreak" class="updateDateTime uniform-input"
                            value="{{ $endBreak }}">
                        <br>
                    </div>
                </fieldset>
            @elseif(isset($timesheet->type) && $timesheet->type !== 'workday')
                <div class="specialUpdateContainer">
                    <input class="updateSpecialInput uniform-input" type="text" name="updateSpecial"
                        value="{{ $timesheet->type }}" {{ $timesheet->official_holiday ? 'readonly' : '' }}>
                    <br>
                    <span class="radioInput">
                        <label for="betaaldInput" class="checkboxContainer">
                            <input type="radio" class="radioBox" id="betaaldInput" name="dayType" value="betaald"
                                {{ $timesheet->accountableHours == $worker->company->day_hours ? 'checked' : '' }}
                                {{ $timesheet->official_holiday ? 'disabled' : '' }}>
                            <span class="labelAndere">Betaald</span>
                            <br>
                            <span class="checkMark"></span>
                        </label>
                        <label style="margin-bottom: 0px" for="onbetaaldInput" class="checkboxContainer" id="onbetaald">
                            <input type="radio" class="radioBox" id="onbetaaldInput" name="dayType" value="onbetaald"
                                {{ $timesheet->accountableHours == 0 ? 'checked' : '' }}
                                {{ $timesheet->official_holiday ? 'disabled' : '' }}>
                            <span class="labelAndere">Onbetaald</span>
                            <br>
                            <span class="checkMark"></span>
                        </label>
                    </span>
                    @if ($timesheet->official_holiday)
                        <div style="margin-top: 0px; " class="alert">
                            <img style="height: 30px" src="{{ asset('images/alert.svg') }}" alt="">
                            Officiële feestdagen <br> kunnen niet worden aangepast.
                        </div>
                    @endif
                </div>
            @endif
            <input class="updateTimesheetSubmit button" type="submit" value="update"
                {{ $timesheet->official_holiday ? 'disabled' : '' }}>


        </form>
    </div>
    <br>
    <form action="{{ route('delete') }}" class="delete" method="POST">
    @csrf
    <input type="hidden" name="workerId" value="{{ $worker->id }}">
    <input type="hidden" name="deleteSheet" value="{{ $timesheet->id }}">
    <input type="hidden" name="sheetType" value="{{ $timesheet->getTable() }}">
    <input type="hidden" name="date" value="{{ $timesheet->Month }}">
    <input class="submit" type="image" src="{{ asset('/images/1843344.png') }}" name="deleteThisSheet" alt="Delete"
        onclick="event.preventDefault(); openConfirmationModal('Ben je zeker dat je deze dag wilt verwijderen?', this.form.action, this.form);">
</form>
    @if ($timesheet->userNote)
        <fieldset class="userNoteContainer">
            <div><b>Notitie:</b></div>
            <div class="userNote">{{ $timesheet->userNote }}</div>
        </fieldset>
    @endif
@endsection
