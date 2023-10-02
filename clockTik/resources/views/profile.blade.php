@extends('layout')
@section('title')
    @php
        $requestedMonth = '';
    @endphp
    <h2>

        {{ $user->name }}

    </h2>
@endsection
@section('userDashboard')
    <div class="profileContent">
        <form class="timesheetForm" method="POST" action="{{ route('getData') }}">
            @csrf
            <select class="dropDownMonth"  name="month" size="1">
                @foreach ($clockedMonths as $allMonths)
                    <option value="{{ $allMonths->month }}">
                        @php
                        $carbonDate = \Carbon\Carbon::create(null,  $allMonths->month,1);
                        echo $carbonDate->format('F');
                        @endphp
                      
                    </option>
                @endforeach
            </select>
            @if(isset($user))
            <input type="hidden" name="worker" value='{{$user->id}}'>
            @endif
            <input class="getMonthButton" type="submit" value="Go">
        </form>
        <div class="timesheetHeader">
        
            @if (isset($monthString))
                {{ date('F', strtotime($monthString)) }}
            @endif
        </div>
        <table class="timesheetTable">
            <thead class="stikyHeader">
                <tr>
                    <th>Date</th>
                    <th>Regular hours</th>
                    <th>Break hours</th>
                    <th>Overtime</th>
                </tr>
            </thead>
            @if (isset($timesheet) && $timesheet->count() > 0)
                @foreach ($timesheet as $item)
                    <tr class="timesheetRow">
                        <td class="date" id="{{ $item->id }}">
                            <a class='displayDay'href="{{ route('myProfile', ['timesheet' => $item->id]) }}">
                                <?php
                                $toTime = strtotime($item->ClockedIn);
                                $days = ['Mon' => 'Ma', 'Tue' => 'Di', 'Wed' => 'Wo', 'Thu' => 'Do', 'Fri' => 'Vr', 'Sat' => 'Za', 'Sun' => 'Zo'];
                                $englishDay = date('D', $toTime);
                                $dutchDay = $days[$englishDay];
                                $dayOfMonth = date('d', $toTime);
                                echo $dutchDay . ' ' . $dayOfMonth;
                                ?>
                            </a>
                        </td>
                        <td>
                            <div class="displayRegular">
                                @if ($item->RegularHours < 7.6 && $item->Weekend == false)
                                    <s>{{ $item->RegularHours }}</s>
                                    => 7.60
                                @elseif($item->Weekend == true)
                                    {{ $item->RegularHours }} Weekend
                                @else
                                    {{ $item->RegularHours }}
                                @endif


                            </div>
                        </td>
                        <td>
                            <div class="displayBreak">
                                {{-- @if ($item->BreakHours > 0) --}}
                                {{ $item->BreakHours }}
                                {{-- @else --}}
                                {{-- {{ $item->BreakHours }}
                                @endif --}}
                            </div>
                        </td>
                        <td>
                            <div class="displayOvertTime">
                                {{ $item->OverTime }}
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <p class="text-danger">No data</p>
            @endif

        </table>

        {{-- @if (isset($timesheet))
            @foreach ($timesheet as $item)
                <p class="date" id="{{ $item->id }}">
                    {{ date('D', strtotime($item->ClockedIn)) . ' ' . date('d', strtotime($item->ClockedIn)) }}
                </p>
            @endforeach
        @else
            <p class="text-danger">No data for this month</p>
        @endif --}}
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
        <div class="text-danger">Something went wrong pls call Toon.</div>
    @endif
@endsection
