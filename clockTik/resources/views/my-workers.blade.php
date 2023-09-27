@extends('layout')
@section('content')
<h2>Welcome {{auth()->user()->name}}</h2>

<div class="workersForm">
    @foreach ($workers as $worker)
        @if (!$worker->admin)
        <form class='workerForm' action="{{ route('getData') }}" method="post">
            @csrf
            <button class='workerButton' type="submit" name='worker' value="{{ $worker->id }}">
                {{ $worker->name }}
                @switch(true)

                @case($worker->timelogs[0]->ShiftStatus == true && $worker->timelogs[0]->BreakStatus == false)
                <div class="working"></div>
                @break

                @case( $worker->timelogs[0]->ShiftStatus == true && $worker->timelogs[0]->BreakStatus == true)
                <div class="onBreak"></div>
                @break

                @default
                <div class="notWorking"></div>
                @endswitch 
                    </button>
                </form>
                @endif
                @endforeach
            </div>
    <a href="{{ route('specials') }}" class='specialsButton'>Specials</a>
@endsection
