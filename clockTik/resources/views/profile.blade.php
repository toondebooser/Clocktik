@extends('layout')
@section('title')
    <h1>{{ auth()->user()->name }}</h1>
@endsection
@section('userDashboard')
    <a href="">Update personal info</a>
    <div class="profileContent">
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
            <tr>
              <th>Date</th>
              <th>Regular hours</th>
              <th>Break hours</th>
              <th>Overtime</th>
            </tr>
            @if (isset($timesheet))
            @foreach ($timesheet as $item)
            <tr>
                <td class="date" id="{{ $item->id }}">
                    <a href="{{route('myProfile')}}">
                        {{ date('D', strtotime($item->ClockedIn)) . ' ' . date('d', strtotime($item->ClockedIn)) }}
                    </a>
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

@endsection
