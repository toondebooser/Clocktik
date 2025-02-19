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

            @if (isset($timesheet) && count($timesheet) != 0)
                {{ date('F', strtotime($timesheet[0]->Month)) }}
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
                        href="{{ route('exportPdf', ['userId' => $userId, 'month' => $days[0]->Month    , 'type' => 'download']) }}">
                        <img class="downloadIcon" src="{{ asset('/images/download.png') }}" alt="Download">
                    </a>
                @endif
              
                @foreach ($days as $item)
               
                    <tr class="timesheetRow">
                        <td class="date" id="{{ $item->id }}">
                            @if($item->type !== 'workday')
                            <a class='displayDay' href="{{ route('update', ['id' => $user->id, 'timesheet' => $item]) }}">
                            @endif
                            @php
                                $toTime = strtotime($item->Month);
                                $days = [
                                    'Mon' => 'Ma',
                                    'Tue' => 'Di',
                                    'Wed' => 'Wo',
                                    'Thu' => 'Do',
                                    'Fri' => 'Vr',
                                    'Sat' => 'Za',
                                    'Sun' => 'Zo',
                                ];
                                $englishDay = date('D', $toTime);
                                $dutchDay = $days[$englishDay];
                                $dayOfMonth = date('d', $toTime);
                                echo $dutchDay . ' ' . $dayOfMonth;
                            @endphp
                            </a>
                            @if ($item->userNote !== null)
                                <img class="noteIcon"src="{{ asset('/images/148883.png') }}" alt="Icon">
                            @endif
                        </td>
                        <td class="displayRegular">
                            @if ($item->RegularHours !== 7.6 && $item->Weekend == false && $item->type == 'workday')
                                <s>{{ $item->RegularHours }}</s>
                                => 7.60
                            @elseif($item->Weekend == true && $item->type == 'workday')
                                Weekend
                            @elseif ($item->Weekend == false && $item->type !== 'workday')
                                {{ $item->type }}
                            @else
                                {{ $item->RegularHours }}
                            @endif


                        </td>
                        <td>
                            <div class="displayBreak">
                                {{ $item->BreakHours }}
                            </div>
                        </td>
                        <td>
                            <div class="displayOvertTime">
                                {{ $item->OverTime }}
                            </div>
                        </td>
                    </tr>
                    <tr class = " timesheetRow">
                        @foreach ($item->timesheets as $timesheet)
                    <tr>
                        <td>
                            <a href="{{ route('update', ['id' => $user->id, 'timesheet' => $timesheet, 'type' => 'timesheet']) }}">Update</a>
                        </td>
                        {{-- <td class="date" id="{{ $timesheet->id }}">
                            <a
                                class='displayDay'href="{{ route('update', ['id' => $user->id, 'timesheet' => $timesheet]) }}">
                                @php
                                    $toTime = strtotime($timesheet->Month);
                                    $days = [
                                        'Mon' => 'Ma',
                                        'Tue' => 'Di',
                                        'Wed' => 'Wo',
                                        'Thu' => 'Do',
                                        'Fri' => 'Vr',
                                        'Sat' => 'Za',
                                        'Sun' => 'Zo',
                                    ];
                                    $englishDay = date('D', $toTime);
                                    $dutchDay = $days[$englishDay];
                                    $dayOfMonth = date('d', $toTime);
                                    echo $dutchDay . ' ' . $dayOfMonth;
                                @endphp
                            </a>
                            @if ($timesheet->userNote !== null)
                                <img class="noteIcon"src="{{ asset('/images/148883.png') }}" alt="Icon">
                            @endif
                        </td> --}}

                        <td>
                            In: {{ \Carbon\Carbon::parse($timesheet->ClockedIn)->format('H:i:s') }} <br>
                            Uit: {{ \Carbon\Carbon::parse($timesheet->ClockedOut)->format('H:i:s') }}
                        </td>
                        <td> In: {{ \Carbon\Carbon::parse($timesheet->BreakStart)->format('H:i:s') }} <br>
                            Uit: {{ \Carbon\Carbon::parse($timesheet->BreakStop)->format('H:i:s') }}</td>
                    </tr>
                @endforeach
                </tr>
                {{-- @else
                    <tr class="content-row">
                        <td>Update</td><td class="displayRegular">  @if ($item->RegularHours !== 7.6 && $item->Weekend == false && $item->type == 'workday')
                            <s>{{ $item->RegularHours }}</s>
                            => 7.60
                        @elseif($item->Weekend == true && $item->type == 'workday')
                            Weekend
                        @elseif ($item->Weekend == false && $item->type !== 'workday')
                            {{ $item->type }}
                        @else
                            {{ $item->RegularHours }}
                        @endif</td><td></td><td></td>
                    </tr>
                    <tr class="toggle-row">
                        <td class="arrow">
                            <img class="dropdownArrow" src="{{ asset('images/download.png') }}" alt="">
                        </td>
                    </tr>
                    @endif --}}
            @endforeach
        @else
            <p class="text-danger">No data</p>
            @endif

        </table>
    </div>
    @if (isset($monthlyTotal))
        @foreach ($monthlyTotal as $item)
            <div class="displayTotalRegular">
                Regular {{ $item->RegularHours }}
            </div>
            <div class="displayTotalBreak">
                Break {{ $item->BreakHours }}
            </div>
            <div class="displayTotalOverTime">
                Overtime {{ $item->OverTime }}
            </div>
        @endforeach
    @else
        <div class="text-danger">Something went wrong.</div>
    @endif
@endsection
{{-- <script>
   document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.arrow').addEventListener('click', function(e) {
        e.preventDefault();
        
        const contentRow = this.closest('tr').previousElementSibling;

        // Toggle the 'active' class to change display
        contentRow.classList.toggle('active');
        
        // Optionally, you might want to toggle the arrow direction here
        // For simplicity, I'll omit this part
    });
});
</script> --}}
