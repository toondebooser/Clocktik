@extends('layout')
@section('content')
    <h2 class='instellenVoor'>Instellen voor {{$forWho}}</h2>

    {{-- <form class="workersForm" method="POST" action="{{route('setSpecial')}}">
    <input type="submit" name="ziek" value="ziek">
    <input type="submit" name="weerverlet" value="weerverlet">
    </form> --}}

    <div class="specialDays">
        @if (isset($specialDays))
        @foreach ($specialDays as $specialDay)
        <form action="{{route('setSpecial')}}" method="POST" class="specialDayForm">
        <input type="submit" name="{{$specialDay}}" value="{{$specialDay}}">
        </form>
        @endforeach
        @endif
    </div>

    @endsection