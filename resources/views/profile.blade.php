@extends('layout')
@section('title')
    @php
        $userId = $user->id;
        if (isset($timesheet[0])) {
            $month = $timesheet[0]->Month;
        }
    @endphp

    <h2>
        {{ $user->name }}
    </h2>
@endsection
@section('content')
    @if (session('error'))
        <div class="error">
            {{ session('error') }} <br>
            <a class="removeError" href=""> ok </a>
        </div>
    @endif

    <div class="profileContent">
        <form class="timesheetForm" method="POST" action="{{ route('getData') }}">
            @csrf
            <select class="dropDownMonth uniform-input" name="month" size="1">
                @foreach ($clockedMonths as $allMonths)
                    @php

                        $currentMonth = Carbon\Carbon::now()->month;
                        $carbonDate = Carbon\Carbon::create(null, $allMonths->month, 1)->locale('nl');
                    @endphp
                    <option value="{{ $allMonths->month }}" {{ $allMonths->month == $currentMonth ? 'selected' : '' }}>
                        {{ $carbonDate->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>

            </select>
            @if (isset($user))
                <input type="hidden" name="worker" value='{{ $user->id }}'>
            @endif
            <input class="button" type="submit" value="Ga naar maand">
        </form>
        @if (auth()->user()->admin == true)
            <form class="dagenInstellen" action="{{ route('specials') }}" method="post">
                @csrf
                <input type="hidden" name="worker" value="{{ $user->id }}">
                <input class="submit" type="image" src="{{ asset('/images/sunPic.png') }}" name="submitUserId"
                    alt="Submit">
            </form>
            <form class="timesheetToevoegen" action="{{ route('timesheetForm') }}" method="post">
                @csrf
                <input type="hidden" name="worker" value="{{ $user->id }}">
                <input class="submit" type="image" src="{{ asset('/images/stopwatch.png') }}" name="submitUserId"
                    alt="Submit">
            </form>
        @endif
        <div class="timesheetHeader">

            @if (isset($days) && count($days) != 0)
                @php
                    $monthName = Carbon\Carbon::parse($days[0]->Month)->locale('nl')->translatedFormat('F');
                @endphp
                {{ $monthName }}
            @endif
        </div>

        <table class="timesheetTable styled-table">
            <thead class="stikyHeader">
                <tr>
                    <th>Datum</th>
                    <th>Gewerkt</th>
                    <th>Gepauzeerd</th>
                    <th>overuren</th>
                </tr>
            </thead>
            @if ($days->count() > 0)
                @if (auth()->user()->admin == true)
                <a class="previewLink"
                        href="{{ route('exportPdf', ['userId' => $userId, 'month' => $days[0]->Month, 'type' => 'preview']) }}">
                        <img class="previewIcon" src="{{ asset('/images/preview.png') }}" alt="Download">
                    </a>
                    <a class="downloadLink"
                        href="{{ route('exportPdf', ['userId' => $userId, 'month' => $days[0]->Month, 'type' => 'download']) }}">
                        <img class="downloadIcon" src="{{ asset('/images/download.png') }}" alt="Download">
                    </a>
                @endif

                @foreach ($days as $day)
                    {{-- Daytotal Row --}}
                    <tr onclick="{{ $day->type == 'workday' ? 'toggleTimesheets(this)' : 'window.location.href=\'' . route('update', ['id' => $user->id, 'timesheet' => $day]) . '\'' }}"
                        class="{{ collect([$day->type === 'workday' ? 'timesheetRow' : null, $day->NightShift ? 'nightShift' : null])->filter()->implode(' ') }}"
                        data-dayType="{{ $day->type }}" data-day="{{ $day->id }}">

                        {{-- Date cell --}}

                        <td style="width: 69.29px" class="date update" id="{{ $day->id }}">

                            @php
                                $toTime = strtotime($day->Month);
                                $days = [
                                    'Mon' => 'Ma',
                                    'Tue' => 'Di',
                                    'Wed' => 'Wo',
                                    'Thu' => 'Do',
                                    'Fri' => 'Vr',
                                    'Sat' => 'Za',
                                    'Sun' => 'Zo',
                                ];
                                if ($day->DayOverlap) {
                                    $currentDate = date('d-m', $toTime);
                                    $nextDate = date('d-m', strtotime('+1 day', $toTime));
                                    echo $currentDate . '<br>' . '>>' . '<br>' . $nextDate;
                                } else {
                                    $englishDay = date('D', $toTime);
                                    $dutchDay = $days[$englishDay];
                                    $dayOfMonth = date('d', $toTime);
                                    echo $dutchDay . ' ' . $dayOfMonth;
                                }
                            @endphp
                            @if ($day->userNote)
                                <img class="noteIcon"src="{{ asset('/images/148883.png') }}" alt="Icon">
                            @endif
                        </td>

                        {{-- Regular hour cell --}}

                        <td class="displayRegular">
                            @if ($day->RegularHours !== $companyDayHours && $day->Weekend == false && $day->type == 'workday')
                                <s>{{ $day->RegularHours }}</s> => {{ $companyDayHours }}
                            @elseif($day->Weekend == true && $day->type == 'workday')
                                Weekend
                            @elseif ($day->Weekend == false && $day->type !== 'workday')
                                {{ $day->type }}
                            @else
                                {{ $day->RegularHours }}
                            @endif
                        </td>

                        {{-- Break hour cell --}}

                        <td class="displayBreak">
                            {{ $day->BreakHours }}
                        </td>


                        {{-- OverTime cell --}}
                        <td class="displayOvertTime">
                            {{ $day->OverTime }}
                        </td>
                    </tr>

                    {{--  Hidden Timesheets linked to daytotals --}}
                    @foreach ($day->timesheets as $timesheet)
                        <tr class='hidden timesheetStyle ' data-timesheet="{{ $day->id }}">
                            <td class="date">
                                <a
                                    href="{{ route('update', ['id' => $user->id, 'timesheet' => $timesheet, 'type' => 'timesheet', 'usedDayTotalId' => $day->id, 'usedDayTotalDate' => $day->Month]) }}">Update</a>
                                @if ($timesheet->userNote !== null)
                                    <img class="noteIcon"src="{{ asset('/images/148883.png') }}" alt="Icon">
                                @endif
                            </td>


                            <td class="timesheetStyle">
                                @if ($timesheet->ClockedIn)
                                    In: {{ $timesheet->ClockedIn->format('H:i') }} <br>
                                    Uit: {{ $timesheet->ClockedOut->format('H:i') }}
                                @endif
                            </td>
                            <td class="timesheetStyle">
                                @if ($timesheet->BreakStart )
                                    In: {{ $timesheet->BreakStart->format('H:i') }} <br>
                                    Uit: {{ $timesheet->BreakStop->format('H:i') }}
                                @endif
                            </td>
                            <td>
                                @if (auth()->user()->admin)
                                    <form  class="deleteForm" method="POST"
                                        action="{{ route('delete') }}">
                                        @csrf
                                        <input type="hidden" name="workerId" value="{{ $userId }}">
                                        <input type="hidden" name="deleteSheet" value="{{ $timesheet->id }}">
                                        <input type="hidden" name="sheetType" value="{{ $timesheet->getTable() }}">
                                        <input type="hidden" name="date" value="{{ $timesheet->Month }}">
                                        <input
                                            onclick="event.preventDefault(); openConfirmationModal('Ben je zeker dat je deze dag wilt verwijderen?', this.form.action, this.form);"
                                            class="submit trashIcon" type="image" src="{{ asset('/images/1843344.png') }}"
                                            name="deleteThisSheet" alt="Delete">
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @if ($timesheet->extraBreakSlots->isNotEmpty())
                            @foreach ($timesheet->extraBreakSlots->sortBy('BreakStart') as $breakSlot)
                                {{-- Set extra break slots as row --}}
                                {{-- @php
                                    dd($breakSlot->timesheet_id);
                                @endphp --}}
                                <tr class='hidden timesheetStyle' data-timesheet="{{ $day->id }}">
                                    <td class="date"> <a
                                            href="{{ route('update', ['id' => $user->id, 'timesheet' => $breakSlot, 'type' => 'extraBreakSlot', 'usedDayTotalId' => $day->id, 'usedDayTotalDate' => $day->Month]) }}">Update</a>
                                    </td>
                                    <td class="timesheetStyle"></td>
                                    <td class="timesheetStyle">
                                        @if ($breakSlot->BreakStart && $breakSlot->BreakStop)
                                            In: {{ $breakSlot->BreakStart->format('H:i') }} <br>
                                            Uit: {{ $breakSlot->BreakStop->format('H:i') }}
                                        @endif
                                    </td>
                                    <td>
                                        <form  class="deleteForm" method="POST"
                                            action="{{ route('delete') }}">
                                            @csrf
                                            <input type="hidden" name="workerId" value="{{ $userId }}">
                                            <input type="hidden" name="deleteSheet" value="{{ $breakSlot->id }}">
                                            <input type="hidden" name="sheetType" value="{{ $breakSlot->getTable() }}">
                                            <input type="hidden" name="date" value="{{ $breakSlot->Month }}">
                                            <input
                                                onclick="event.preventDefault(); openConfirmationModal('Ben je zeker dat je deze Pauze wilt verwijderen?', this.form.action, this.form);"
                                                class="submit trashIcon" type="image" src="{{ asset('/images/1843344.png') }}"
                                                name="deleteThisSheet" alt="Delete">
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                @endforeach
            @else
                <p class="text-danger">No data</p>
            @endif

        </table>
    </div>
    @if (isset($monthlyTotal))
        @foreach ($monthlyTotal as $day)
            <div class="displayTotalRegular">
                Regular {{ $day->RegularHours }}
            </div>
            <div class="displayTotalBreak">
                Break {{ $day->BreakHours }}
            </div>
            <div class="displayTotalOverTime">
                Overtime {{ $day->OverTime }}
            </div>
        @endforeach
    @else
        <div class="text-danger">Something went wrong.</div>
    @endif
@endsection
<script>
    const toggleTimesheets = (element) => {
        if (element.getAttribute('data-dayType') !== 'workday') return;
        const dayId = element.getAttribute('data-day');
        element.classList.toggle('belongTogether')
        const timesheets = document.querySelectorAll(`tr[data-timesheet="${dayId}"]`);
        timesheets.forEach(row => {
            row.classList.toggle('hidden');
            row.classList.toggle('belongTogether');
        })
    }
</script>
