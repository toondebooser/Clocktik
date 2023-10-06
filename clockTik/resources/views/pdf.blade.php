<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <title>My PDF Document</title>
    <style>
        body {
            font-family: "Noto Sans", sans-serif;

        }

        h1 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #333;
        }

        th {
            background-color: rgb(200, 215, 248);
        }

        .displayRegular {
            text-align: center;
            color: #2626da;
        }

        .displayBreak {
            text-align: center;
            color: #da0a0a;
        }

        .displayOvertTime {
            text-align: center;
            color: #daa30a;
        }
    </style>
</head>

<body>
    <header>
        <h1>Uurrooster: {{ date('F', strtotime($timesheet[0]->Month)) }} {{ $user->name }}</h1>
    </header>
    <main>
        <table>
            <thead>
                <tr>
                    <th>Datum</th>
                    <th>Werkuren</th>
                    <th>Pauze</th>
                    <th>Overuren</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($timesheet as $item)
                    <tr class="timesheetRow">
                        <td class="date" id="{{ $item->id }}">
                            <?php
                            $toTime = strtotime($item->ClockedIn);
                            $days = ['Mon' => 'Ma', 'Tue' => 'Di', 'Wed' => 'Wo', 'Thu' => 'Do', 'Fri' => 'Vr', 'Sat' => 'Za', 'Sun' => 'Zo'];
                            $englishDay = date('D', $toTime);
                            $dutchDay = $days[$englishDay];
                            $dayOfMonth = date('d', $toTime);
                            echo $dutchDay . ' ' . $dayOfMonth;
                            ?>
                            </a>
                            @if ($item->userNote !== null)
                                <img class="noteIcon"src="{{ asset('/images/148883.png') }}" alt="Icon">
                            @endif
                        </td>
                        <td>
                            <div class="displayRegular">
                                @if ($item->RegularHours < 7.6 && $item->Weekend == false && $item->type == 'workday')
                                    <s>{{ $item->RegularHours }}</s>
                                    => 7.60
                                @elseif($item->Weekend == true && $item->type == 'workday')
                                    Weekend
                                @elseif ($item->Weekend == false && $item->type !== 'workday')
                                    {{ $item->type }}
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
            </tbody>
        </table>
    </main>
</body>

</html>
