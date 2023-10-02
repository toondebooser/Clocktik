
@extends('layout')

@section('title')
    <h2>Welcome {{ $user->name }}</h2>
@endsection

@section('userDashboard')

@if(session('error'))
<div class="error">
    {{ session('error') }}
    <a class="removeError" href="">ok</a>
</div>
@endif
    @if ($shiftStatus == false)
        <a href="{{ route('start') }}" class="startButton">
            <p class="buttonText">Start</p>
        </a>
    @else
        @if ($shiftStatus == true && $breakStatus == false)
            <a onclick="return confirm('Are you sure you want to take a break?')" href="{{ route('break') }}"
                class="breakButton">
                <p class="buttonText">Break</p>
            </a>
            <a onclick="return confirm('Are you sure you want to quit your shift?')" href="{{ route('stop') }}"
                class="stopButton">
                <p class="buttonText">Stop</p>
            </a>
        @else
            @if ($shiftStatus == true && $breakStatus == true)
                <a onclick="return confirm('Are you sure you want to start working again?')" href="{{ route('stopBreak') }}"
                    class="breakButton">
                    <p class="buttonText">Back to work</p>
                </a>

                <a onclick="return confirm('Are you sure you want to quit your shift?')" href="{{ route('stop') }}"
                    class="stopButton">
                    <p class="buttonText">Stop</p>
                </a>
            @else
                <div class="text-danger">Something went wrong pls contact tech guy.</div>
            @endif
        @endif

        @endif
        {{-- @if (isset($start))
            <p class="startTime">{{ $start }}</p>
        @endif --}}
@endsection
