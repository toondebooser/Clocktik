@extends('layout')
@section('content')
    <h2>Instellen voor {{$forWho}}</h2>

    <form class="workersForm" method="POST" action="{{route('setSpecial')}}">
    <input type="submit" name="ziek" value="ziek">
    <input type="submit" name="weerverlet" value="weerverlet">
    </form>
    @endsection