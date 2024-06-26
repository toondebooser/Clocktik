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
            <form action="{{ route('setSpecial') }}" method="POST" class="specialDayForm">
                @csrf
                <span class="radioInput">
                    <label for="betaaldInput"  class="checkboxContainer" >
                        <input type="radio" class="radioBox" id="betaaldInput" name="dayType" value="betaald" >
                        <span class="labelAndere">Betaald</span>
                        <br>
                        <input type="text" class="betaaldInput" name="betaald" >
                        <span class="checkMark"></span>
                    </label>         
                    <label for="onbetaaldInput"  class="checkboxContainer" id="onbetaald">
                        <input type="radio" class="radioBox" id="onbetaaldInput" name="dayType" value="onbetaald" >
                        <span class="labelAndere">Onbetaald</span>
                        <br>
                        <input type="text" class="onbetaaldInput" name="onbetaald" >
                        <span class="checkMark"></span>                      
                    </label> 
               

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
    </div>
 
@endsection
