@extends('layout')

@section('content')


    <h2>{{$type}}</h2>

    <div class="workersForm">
        @foreach ($dataSet as $data)
            <!-- Personeel of Voor wie? -->
            @if ($type !== 'Bedrijven' && !$data->admin)
                <form class="workerForm" action="{{ $type === 'Personeel' ? route('getData') : route('specials') }}"
                    method="post">
                    @csrf
                    <button class="workerButton" type="submit" name="worker" value="{{ $data->id }}">
                        {{ $data->name }}
                        <div
                            class="{{ $data->timelogs->ShiftStatus ? ($data->timelogs->BreakStatus ? 'onBreak' : 'working') : 'notWorking' }}">
                        </div>
                    </button>
                </form>

                <!-- Bedrijven -->
            @elseif ($type === 'Bedrijven')
                <div class="bedrijvenLijst">
                    <form class="workerForm"
                        action="{{ route('myList', ['type' => 'Personeel', 'company_code' => $data->company_code]) }}"
                        method="get">
                        @csrf
                        <button class="workerButton" type="submit" name="worker"
                            style="display: flex; align-items: center; justify-content: center;">
                            {{ $data->company_name }}
                            <img style="height: 30px; margin-left: 10px;" src="{{ asset($data->image) }}" alt="Company logo">
                        </button>
                    </form>
                    <a href="{{route('godSettings',['company_code' => $data->company_code, 'godMode' => true])}}">
                        <img  style="height: 50px" src="{{asset("images/2849830-gear-interface-multimedia-options-setting-settings_107986.png")}}" alt="settings">
                    </a>
                </div> @if ($loop->last)
                    <a class="specialsButton" href="{{ route('addCompany') }}">Bedrijf toevoegen</a>
                @endif
            @endif
            @if ($type === 'Personeel' && $loop->last)
                <a href="{{ route('myList', ['type' => 'Voor wie', 'company_code' => $data->company_code]) }}"
                    class="specialsButton">Dagen instellen</a>
            @endif
        @endforeach

        <!-- Voor iedereen knop -->
        @if ($type === 'Voor wie?')
            <form class="workerForm" method="post" action="{{ route('specials') }}">
                @csrf
                <button class="workerButton" type="submit" name="worker" value="{{ $dataSet }}">Voor
                    iedereen</button>
            </form>
        @endif
    </div>

    <!-- Dagen instellen link -->
@endsection
