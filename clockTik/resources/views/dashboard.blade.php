@extends('layout')
@section('title')
    <h2>Welcome {{ $user->name }}</h2>
@endsection

@section('userDashboard')
    @if ($shiftStatus == false)
        <a href="{{ route('start') }}" class="startButton">
            <p class="buttonText">Start</p>
        </a>
    @else
        @if ($shiftStatus == true && $breakStatus == false)

            <a href="{{ route('break') }}" class="breakButton">
                <p class="buttonText">Break</p>
            </a>
            <a href="{{ route('stop') }}" class="stopButton">
                <p class="buttonText">Stop</p>
            </a>
        @else
            @if ($shiftStatus == true && $breakStatus == true)

                <a href="{{ route('stopBreak') }}" class="breakButton">
                    <p class="buttonText">Back to work</p>
                </a>

                <a href="{{ route('stop') }}" class="stopButton">
                    <p class="buttonText">Stop</p>
                </a>
                @else
                <div class="text-danger">Something went wrong pls contact tech guy.</div>
                
                @endif
        @endif

            @if (isset($start))
                <p class="startTime">{{ $start }}</p>

            @endif
        @endif    
        @endsection
