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
@section('userDashboard')
    @if (session('error'))
        <div class="error">
            {{ session('error') }} <br>
            <a class="removeError" href=""> ok </a>
        </div>
    @endif

    <div class="profileContent">
        <form class="timesheetForm" method="POST" action="{{ route('getData') }}">
            @csrf
            <select class="dropDownMonth" name="month" size="1">
                @foreach ($clockedMonths as $allMonths)
                    @php
                        $currentMonth = \Carbon\Carbon::now()->month;

                    @endphp
                    <option value="{{ $allMonths->month }}" {{ $allMonths->month == $currentMonth ? 'selected' : '' }}>
                        @php
                            $carbonDate = \Carbon\Carbon::create(null, $allMonths->month, 1);
                            echo $carbonDate->format('F');
                        @endphp

                    </option>
                @endforeach
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
                {{ date('F', strtotime($days[0]->Month)) }}
            @endif
        </div>
        <table class="timesheetTable">
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
                        href="{{ route('exportPdf', ['userId' => $userId, 'month' => $days[0]->Month, 'type' => 'preview']) }}"
                        target="_blank">
                        <img class="previewIcon" src="{{ asset('/images/preview.png') }}" alt="Preview">
                    </a>
                    <a class="downloadLink"
                        href="{{ route('exportPdf', ['userId' => $userId, 'month' => $days[0]->Month, 'type' => 'download']) }}">
                        <img class="downloadIcon" src="{{ asset('/images/download.png') }}" alt="Download">
                    </a>
                @endif

                @foreach ($days as $day)
                    <tr onclick="{{ $day->type == 'workday' ? 'toggleTimesheets(this)' : 'window.location.href=\'' . route('update', ['id' => $user->id, 'timesheet' => $day]) . '\'' }}"
                        class="{{ collect([$day->type === 'workday' ? 'timesheetRow' : null, $day->NightShift ? 'nightShift' : null])->filter()->implode(' ') }}"
                        data-dayType="{{ $day->type }}" data-day="{{ $day->id }}">
                        <td style="width: 69.29px" class="date update" id="{{ $day->id }}">
                            {{-- @if ($day->type !== 'workday')
                            <a class='displayDay'
                                    href="{{ route('update', ['id' => $user->id, 'timesheet' => $day]) }}"> 
                            @endif --}}
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
                            {{-- </a> --}}
                            @if ($day->userNote !== null)
                                <img class="noteIcon"src="{{ asset('/images/148883.png') }}" alt="Icon">
                            @endif
                        </td>
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
                        <td class="displayBreak">
                            {{ $day->BreakHours }}
                        </td>
                        <td class="displayOvertTime">
                            {{ $day->OverTime }}
                        </td>
                    </tr>
                    @foreach ($day->timesheets as $timesheet)
                        <tr class='hidden timesheetStyle ' data-timesheet="{{ $day->id }}">
                            <td class="date">
                                <a
                                    href="{{ route('update', ['id' => $user->id, 'timesheet' => $timesheet, 'type' => 'timesheet', 'usedDayTotalId' => $day->id, 'usedDayTotalDate' => $day->Month]) }}">Update</a>
                                    @php
                                    @endphp
                                    @if ($timesheet->userNote !== null)
                                    <img class="noteIcon"src="{{ asset('/images/148883.png') }}" alt="Icon">
                                @endif
                            </td>


                            <td class="timesheetStyle">
                                @if ($timesheet->ClockedIn)
                                    In: {{ $timesheet->ClockedIn->format('H:i') }} <br>
                                    Uit: {{ $timesheet->ClockedOut->format('H:i') }}
                                @else
                                    /
                                @endif
                            </td>
                            <td class="timesheetStyle">
                                @if($timesheet->BreakStart)
                                In: {{ $timesheet->BreakStart->format('H:i') }} <br>
                                Uit: {{ $timesheet->BreakStop->format('H:i') }}</td>
                                @else/
                                @endif
                            <td>
                                <a href="{{ route('delete', ['workerId' => $userId, 'deleteSheet' => $timesheet->id, 'date' => $timesheet->Month]) }}"
                                    onclick="return confirm('Zedde zeker?')">

                                    <img class="trashIcon" src="{{ asset('/images/1843344.png') }}" alt="Delete">
                                </a>
                            </td>
                        </tr>
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
