@extends('layout')
@section('content')
@section('content')
    @if (session('success'))
        <div class="success">
            {{ session('success') }} <br>
            <a class="removeError" href=""> ok </a>
        </div>
    @endif
    <h2>{{ $type }}</h2>

    <div class="workersForm">
        @foreach ($dataSet as $data)
            @if ($type !== 'Bedrijven' && !$data->admin)
                <form class='workerForm'
                    action="{{ $type == 'Personeel' ? route('getData') : ($type == 'Voor wie?' ? route('specials') : '') }}"
                    method="post">
                    @csrf
                    <button class='workerButton' type="submit" name='worker' value="{{ $data->id }}">
                        {{ $data->name }}
                        @switch(true)
                            @case($data->timelogs->ShiftStatus == true && $data->timelogs->BreakStatus == false)
                                <div class="working"></div>
                            @break

                            @case($data->timelogs->ShiftStatus == true && $data->timelogs->BreakStatus == true)
                                <div class="onBreak"></div>
                            @break

                            @default
                                <div class="notWorking"></div>
                        @endswitch
                    </button>
                </form>
            @elseif($type == "Bedrijven")
                <form class='workerForm' action="{{ route('myList', ['type' => 'Personeel', 'company_code' => $data->company_code]) }}" method="get">
                    @csrf
                    <button style="display: flex; align-items: center; justify-content: center" class='workerButton' type="submit" name='worker'>
                        {{ $data->company_name }}
                        <img style="height: 30px; margin-left: 10px" src="{{asset($data->image)}}" alt="Company logo">
                    </button>
                </form>
                @if ($loop->last)
                    <a class='specialsButton' href="{{ route('addCompany') }}">Bedrijf toevoegen</a>
                @endif
            @endif
        @endforeach
        @if ($type == 'Voor wie?')
            <form class="workerForm" method="post" action="{{ route('specials') }}">
                @csrf
                <button class="workerButton" type="submit" name="worker" value="{{ $dataSet }}">
                    Voor iedereen</button>
            </form>
        @endif
    </div>
    @if ($type == 'Personeel')
        <a href="{{ route('myList', ['type' => 'Voorwie', 'company_code' => auth()->user()->company_code]) }}"
            class='specialsButton'>Dagen instellen</a>
    @endif
@endsection
