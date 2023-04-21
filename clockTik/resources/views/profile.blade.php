@extends('layout')
@section('title')
<?php 
$now = now('Europe/Brussels');
$monthNow = date('F', strtotime(now('Europe/Brussels')));
$nextMonth = date('F', strtotime($now->modify('+1 month')));
// var_dump($clockedMonths)?>
    <h1>{{ auth()->user()->name }}</h1>
@endsection
@section('userDashboard')

    <a class="updateProfile" href="">Update profile</a>
    <div class="profileContent">
        <form class="timesheetForm" action="">

            <select id="month" name="month" size="1">
                <option value="0"></option>
                @foreach ($clockedMonths as $allMonths)
              <option value="{{date('F', strtotime($allMonths->Month))}}">{{date('F', strtotime($allMonths->Month))}}</option>
              @endforeach
            </select>
                @php
                    $thisYear = date('Y');
                    $endYear = date('Y')+10;
                    $years = range($thisYear, $endYear);
                @endphp
                 <select id="month" name="month" size="1">
                    <option value="0"></option>
                    @foreach ($years as $year)
                  <option value="{{$year}}">{{$year}}</option>
                  @endforeach
                </select>
            {{-- <input type="submit"> --}}
        </form>
        <div class="timesheetHeader">

            @if (isset($month))
            {{date('F', strtotime($month))}}
            
            @endif
        </div>
        {{-- <p class="date">Date</p>
        <p class="displayRegular">Regular hours</p>
        <p class="displayBreak">Break hours</p>
        <p class="displayOverTime">overtime</p> --}}
        <table>
            <thead class="stikyHeader">
            <tr>
              <th>Date</th>
              <th>Regular hours</th>
              <th>Break hours</th>
              <th>Overtime</th>
            </tr>
            </thead>
            @if (isset($timesheet))
            @foreach ($timesheet as $item)
            <tr>
                <td class="date" id="{{ $item->id }}">
                    <a class='displayDay'href="{{route('myProfile')}}">
                        {{ date('D', strtotime($item->ClockedIn)) . ' ' . date('d', strtotime($item->ClockedIn)) }}
                    </a>
                </td>
                <td>
                    <div class="displayRegular">
                        @if ($item->RegularHours < 7.6)
                        <s>{{$item->RegularHours}}</s>
                        => 7.60
                        @else
                        {{$item->RegularHours}}
                        @endif
                            

                    </div>
                </td>
                <td>
                    <div class="displayBreak">
                        @if ($item->BreakHours > 0 )
                        <s>{{$item->BreakHours}}</s>
                        @else 
                        {{$item->BreakHours}}
                        @endif
                    </div>
                </td>
                <td>
                    <div class="displayOvertTime">
                        {{$item->OverTime}}
                    </div>
                </td>
            </tr>
            @endforeach
              
        @else
            <p class="text-danger">No data for this month</p>
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
    @if (isset($userTotal))
        @foreach ($userTotal as $item)
        <div class="displayTotalRegular">Regular {{$item->RegularHours}}</div>
        <div class="displayTotalBreak"> Break {{$item->BreakHours}}</div>
        <div class="displayTotalOverTime">Overtime {{$item->OverTime}}</div>
    @endforeach
    @else 
    <div class="text-danger">Something went wrong pls call tech guy.</div>
    @endif
@endsection
