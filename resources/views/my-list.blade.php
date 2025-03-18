@extends('layout')
@section('content')
@section('content')
@if (session('success'))
<div class="success">
    {{ session('success') }} <br>
    <a class="removeError" href=""> ok </a>
</div>
@endif
@if (isset($setForTimesheet) && $setForTimesheet == true)
    <h2>Uurroosters</h2>
@else
    <h2>Voor wie ?</h2>
@endif

    <div class="workersForm">
        @foreach ($workers as $worker)
        @if(!$worker->admin) 
        <form class='workerForm'
        action="
        @if (isset($setForTimesheet) && $setForTimesheet == true) {{ route('getData') }}
        @elseif (isset($setForTimesheet) && $setForTimesheet == false)
        {{ route('specials') }} @endif
        "
                method="post">
                @csrf
                <button class='workerButton' type="submit" name='worker' value="{{ $worker->id }}">
                    {{ $worker->name }}
                    @switch(true)
                    @case($worker->timelogs->ShiftStatus == true && $worker->timelogs->BreakStatus == false)
                            <div class="working"></div>
                        @break

                        @case($worker->timelogs->ShiftStatus == true && $worker->timelogs->BreakStatus == true)
                            <div class="onBreak"></div>
                        @break
                        
                        @default
                        <div class="notWorking"></div>
                    @endswitch
                </button>
            </form>
            @endif
        @endforeach
        @if (isset($setForTimesheet) && $setForTimesheet == false)
        <form class="workerForm" method="post" action="{{ route('specials') }}">
                @csrf
                <button class="workerButton" type="submit" name="worker" value="{{ $workers }}">
                    Voor iedereen</button>
            </form>
        @endif
    </div>
    @if (isset($setForTimesheet) && $setForTimesheet == true)
        <a href="{{ route('myList'), ['type' => 'Voor wie?', 'company_code' => auth()->user()->company_code] }}" class='specialsButton'>Dagen instellen</a>
    @endif
@endsection
