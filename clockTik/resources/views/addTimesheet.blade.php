@extends('layout')
@section('content')
@if(isset($workerReturn))
    {{ dd($workerReturn) }}
@endif
<h2>Nieuw rooster voor: {{$worker->name}}</h2>
@if(session('error'))
<div class="error">{{ session('error') }} <br>
    <a class="removeError" href=""> ok </a>
</div>
@endif
<form action="{{route('newTimesheet')}}" class="addNewTimesheetForm" method="POST">
@csrf
<input class="newTimesheetDate" type="date" name="newTimesheetDate">
<input class="newTimesheetInput" type="hidden" name="workerId" value="{{$id}}">
<input class="newTimesheetInput" type="hidden" name="worker" value="{{json_encode($worker)}}">
<input class="newTimesheetInput" type="time" name="startTime">
<input class="newTimesheetInput" type="time" name="endTime" >
<input class="userNoteSubmit" type="submit" value="Voeg toe">
</form>
@endsection