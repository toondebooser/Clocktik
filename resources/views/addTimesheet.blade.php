@extends('layout')
@section('content')

<h2>Nieuw rooster voor: {{$worker->name}}</h2>

<form action="{{route('newTimesheet')}}" class="addNewTimesheetForm" method="POST">
@csrf
<input id="addDate" class="newTimesheetDate uniform-input" type="date" name="newTimesheetDate"> <br>
<input class="newTimesheetInput uniform-input" type="hidden" name="workerId" value="{{$id}}">
<input class="newTimesheetInput startInput uniform-input" type="time" name="startTime">
<input class="newTimesheetInput endInput uniform-input" type="time" name="endTime" >
<input class="button addTimesheet" type="submit" value="Voeg toe">
</form>
@endsection