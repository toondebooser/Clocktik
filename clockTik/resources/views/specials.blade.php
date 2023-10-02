@extends('layout')
@section('content')
    <h2 class='instellenVoor'>Instellen voor {{ $forWho }}</h2>

    @if(session('error'))
    <div class="error">
        {{ session('error') }}
    </div>
    @endif

    <div class="specialDays">
        @if (isset($specialDays))
            <form action="{{ route('setSpecial') }}" method="POST" class="specialDayForm">
                @csrf
                <span class="radioInput">
                    @foreach ($specialDays as $specialDay)
                    <label class="checkboxContainer" for="{{ $specialDay }}">
                        <input class="radioBox" type="radio" id='{{ $specialDay }}' name="specialDay" value="{{ $specialDay }}"
                        @if ($loop->first) required @endif>
                        {{ $specialDay }}
                        <span class="checkMark"></span>
                        </label>
                        <br>
                    @endforeach
                </span>
                <input type="hidden" name="worker" value="{{$worker}}">
                <span class="dateInput">
                    <input type="date" name="singleDay" id="singleDayInput"> <br>
                    <input class="dagSubmit" type="submit" name="submitType" value="Dag Toevoegen"><br>
                    <hr>

                    <input class="startDateInput" type="date" name="startDate" id="startDateInput">
                    <input class="endDateInput" type="date" name="endDate" id="endDateInput"><br>
                    <input class="periodeSubmit" type="submit" name="submitType" value="Periode Toevoegen">
            </form>
                </span>
        @endif
    </div>

@endsection
