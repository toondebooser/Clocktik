@extends('layout')
@section('content')

<h2>{{ $worker->name }}</h2>

<form action="{{ route('newTimesheet') }}" class="addNewTimesheetForm" method="POST">
    @csrf
    <input id="addDate" class="newTimesheetDate uniform-input" type="date" name="newTimesheetDate"> <br>
    <input class="newTimesheetInput uniform-input" type="hidden" name="workerId" value="{{ $id }}">
    <input class="newTimesheetInput startInput uniform-input" type="time" name="startTime">
    <input class="newTimesheetInput endInput uniform-input" type="time" name="endTime">

    <!-- Pause Button -->
    <button type="button" class="button pauseButton" onclick="toggleBreakFields()">Pauze toevoegen</button>

    <!-- Break Input Fields (Initially Hidden) -->
    <div id="breakFields" class="break-fields">
        <div class="break-fields-content">
            <h3>Add Break</h3>
            <label for="breakStart">Break Start:</label>
            <input type="time" name="StartBreak" id="startBreak" class="uniform-input">
            <label for="breakEnd">Break End:</label>
            <input type="time" name="EndBreak" id="endBreak" class="uniform-input">
            <div class="break-fields-buttons">
                <button type="button" class="button" onclick="toggleBreakFields()">Close</button>
            </div>
        </div>
    </div>

    <input class="button addTimesheet" type="submit" value="Voeg toe">
</form>

<!-- JavaScript to Toggle Break Fields -->
<script>
    function toggleBreakFields() {
        const breakFields = document.getElementById('breakFields');
        breakFields.style.display = breakFields.style.display === 'flex' ? 'none' : 'flex';
    }
</script>

<!-- CSS for Fixed Positioning -->
<style>
    .break-fields {
        display: none; /* Initially hidden */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .break-fields-content {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        width: 300px;
        text-align: center;
    }

    .break-fields-content label {
        display: block;
        margin: 10px 0 5px;
    }

    .break-fields-content input {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
    }

    .break-fields-buttons {
        margin-top: 15px;
    }

    .break-fields-buttons button {
        padding: 8px 16px;
        cursor: pointer;
    }

    .pauseButton {
        padding: 8px 16px;
        margin: 10px 0;
        cursor: pointer;
        background-color: #f0ad4e; /* Example color */
        border: none;
        border-radius: 4px;
        color: white;
    }

    .pauseButton:hover {
        background-color: #ec971f;
    }
</style>

@endsection