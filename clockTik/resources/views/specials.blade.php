@extends('layout')
@section('content')
    <h2 class='instellenVoor'>Instellen voor {{ $forWho }}</h2>



    <div class="specialDays">
        @if (isset($specialDays))
            <form action="{{ route('setSpecial') }}" method="POST" class="specialDayForm">
                @csrf
                <span class="radioInput">
                    @foreach ($specialDays as $specialDay)
                        <input type="radio" id='{{ $specialDay }}' name="specialDay" value="{{ $specialDay }}"
                            @if ($loop->first) required @endif>
                        <label for="{{ $specialDay }}">{{ $specialDay }}</label>
                        <br>
                    @endforeach
                </span>
                <span class="dateInput">
                    <input type="datetime-local" name="singleDay" id="singleDayInput"> <br>
                    <input class="dagSubmit" type="submit" name="submitType" value="Dag Toevoegen"><br>
                    <hr>

                    <input class="startDateInput" type="datetime-local" name="period" id="startDateInput">
                    <input class="endDateInput" type="datetime-local" name="period" id="endDateInput"><br>
                    <input class="periodeSubmit" type="submit" name="submitType" value="Periode Toevoegen">
            </form>
                </span>
        @endif
    </div>

@endsection
