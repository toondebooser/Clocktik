@extends('layout')
@section('content')

<h2>Nieuw rooster voor: {{$worker->name}}</h2>

<form action="{{route('newTimesheet')}}" class="addNewTimesheetForm" method="POST">
@csrf
<input id="addDate" class="newTimesheetDate" type="date" name="newTimesheetDate"> <br>
<input class="newTimesheetInput" type="hidden" name="workerId" value="{{$id}}">
<input class="newTimesheetInput startInput" type="time" name="startTime">
<input class="newTimesheetInput endInput" type="time" name="endTime" >
<input class="button addTimesheet" type="submit" value="Voeg toe">
</form>
@endsection