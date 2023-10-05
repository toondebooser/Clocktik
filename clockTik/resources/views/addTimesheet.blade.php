@extends('layout')
@section('content')
<h2>Nieuw rooster voor: {{$worker->name}}</h2>
<form action="{{route('newTimesheet')}}" class="addNewTimesheetForm" method="POST">
@csrf
<input class="newTimesheetDate" type="date" name="newTimesheetDate">
<input class="newTimesheetInput" type="hidden" name="workerId" value="{{$id}}">
<input class="newTimesheetInput" type="time" name="startTime">
<input class="newTimesheetInput" type="time" name="endTime" >
<input class="newTimesheetSubmit" type="submit" value="Voeg toe">
</form>
@endsection