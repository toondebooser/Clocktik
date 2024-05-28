@extends('layout')
@section('content')
    <h2 class='instellenVoor'>Instellen voor {{ $forWho }}</h2>

    @if (session('error'))
        <div class="error">
            @foreach (session('error') as $userError)
                @php
                    $findUser = \App\Models\User::find($userError['id']);
                    $findUser? $user = $findUser->name: $user = 'iedereen'
                @endphp
                <p class='specifiedError'>Voor uurrooster van: {{ $user}}</p>
                {{ $userError['errorList'] }}
            @endforeach
            <a class="removeError" href="">ok</a>
        </div>
    @elseif (session('errors'))
        <div class="error">
            @foreach (session('errors') as $userError)
                @php
                    $findUser = \App\Models\User::find($userError['id']);
                    $findUser? $user = $findUser->name: $user = 'iedereen'
                @endphp
                <p class="specifiedError">Voor uurrooster van: {{ $user }}</p>
                @foreach ($userError['errorList'] as $error)
                    {{$error}}<br>
                @endforeach <br>
            @endforeach
    <a class="removeError" href="">ok</a>
    </div>
    @endif
    <div class="specialDays">
        @if (isset($specialDays))
            <form action="{{ route('setSpecial') }}" method="POST" class="specialDayForm">
                @csrf
                <span class="radioInput">
                    @foreach ($specialDays as $specialDay)
                        <label class="checkboxContainer" for="{{ $specialDay }}">
                            <input class="radioBox" type="radio" id='{{ $specialDay }}' name="specialDay"
                                value="{{ $specialDay }}" @if ($loop->first) required @endif >
                            {{ $specialDay }} @if ($specialDay == "Weerverlet")
                            <span class="smallText">(onbetaald)</span>
                        @endif
                            <span class="checkMark"></span>
                            @if ($specialDay == "Feestdag")
                            <br>
                            <input type="text" class="hollidayNote" name="Feestdag" >
                            @endif
                            @if ($specialDay == "Betaald_verlof")
                            <br>
                            <input type="text" class="bvNote" name="Betaald_verlof" >
                            @endif
                        </label>
                        <br>
                    @endforeach
                    <label for="customInput" id="andereLabel" class="checkboxContainer" for="customInput">
                        <input type="radio" class="radioBox" id="customInput" name="specialDay" value="" >
                        <span class="labelAndere">Andere <span class="smallText">(onbetaald)</span></span>
                        <input type="text" class="customInput" name="customInput" >
                        <span class="checkMark"></span>
                    </label>         
                    <br>
                </span>
                <input type="hidden" name="worker" value="{{ $worker }}">
                <span class="dateInput">
                    <input type="date" name="singleDay" id="singleDayInput"> <br>
                    <input class="dagSubmit button" type="submit" name="submitType" value="Dag Toevoegen"><br>
                    <hr>

                    <input class="startDateInput" type="date" name="startDate" id="startDateInput">
                    <input class="endDateInput" type="date" name="endDate" id="endDateInput"><br>
                    <input class="periodeSubmit button" type="submit" name="submitType" value="Periode Toevoegen">
                </span>
            </form>
        @endif
    </div>
 
@endsection
