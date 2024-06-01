<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <title>Uurrooster personeel</title>
    <style>
        body {
            font-family: "Noto Sans", sans-serif;

        }

        h1 {
            color: #333;
            text-align: center;
        }

        .notitie {
            word-break: break-all;
        }


        table {
            width: 100%;
            border-collapse: collapse;
        }

        .timesheetRow {
            height: 100px;
        }

        table,
        th,
        td {
            border: 1px solid #333;
        }

        th {
            height: 40px;
            background-color: rgb(200, 215, 248);
        }

        td {
            width: 130px;
        }

        .notitie {
            min-height: 60px;
            width: 95%;
            margin: 5px;
            word-break: break-all;
        }

        .date {
            text-align: center;
        }

        .displayRegular {
            text-align: center;
            color: #2626da;
        }

        .inOut {
            text-align:left;
            color: black;
        }

        .displayBreak {
            text-align: center;
            color: #da0a0a;
        }

        .displayOvertTime {
            text-align: center;
            color: #daa30a;
        }

        .displayTotalRegular {
            text-align: center;

            justify-self: center;
            align-self: center;
            color: #2626da;
            background-color: rgb(200, 215, 248);
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #2626da;
        }

        .displayTotalBreak {
            text-align: center;

            justify-self: center;
            align-self: center;
            color: #da0a0a;
            background-color: rgb(248, 200, 200);
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #da0a0a;
        }

        .totalContainer {
            text-align: center;
        }

        .total {
            display: inline-block;
            width: 150px;
            margin: 10px;
        }

        h3 {
            text-align: start;
        }

        .displayTotalOverTime {
            text-align: center;
            justify-self: center;
            align-self: center;
            color: #daa30a;
            background-color: rgb(248, 239, 200);
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #daa30a;
        }
    </style>
</head>

<body>

    <div class="content">
        <header>
            <h1>{{ date('F', strtotime($timesheet[0]->Month)) }} {{ $user->name }}</h1>
        </header>
        <div class="totalContainer">
            <h3>Maand Totaal</h3>
            @if (isset($monthlyTotal))
                @foreach ($monthlyTotal as $item)
                    <div class="displayTotalRegular total">
                        Regular {{ $item->RegularHours }}
                    </div>
                    <div class="displayTotalBreak total">
                        Break {{ $item->BreakHours }}
                    </div>
                    <div class="displayTotalOverTime total">
                        Overtime {{ $item->OverTime }}
                    </div>
                @endforeach
            @else
        </div>
        <div class="text-danger">Something went wrong pls call Toon.</div>
    </div>
    @endif
    <h3>Uurrooster</h3>
    <main>
        <table>
            <thead>
                <tr>
                    <th>Datum</th>
                    <th>Werkuren</th>
                    <th>Pauze</th>
                    <th>Overuren</th>
                    <th>Notitie</th>
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
                                @if ($item->type == 'workday')
                                        <span class="inOut">
                                            <br> Clocked in: {{ date('H:i', $toTime) }}
                                            <br> Clocked out: {{ date('H:i', strtotime($item->ClockedOut)) }}
                                        </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="displayBreak">
                                {{ $item->BreakHours }}
                                @if ($item->type == 'workday')
                                <span class="inOut">
                                    <br> Clocked in: {{ date('H:i', strtotime($item->BreakStart)) }}
                                    <br> Clocked out: {{ date('H:i', strtotime($item->BreakStop)) }}
                                </span>
                        @endif
                            </div>
                        </td>
                        <td>
                            <div class="displayOvertTime">
                                {{ $item->OverTime }}
                            </div>
                        </td>
                        <td>
                            <div class="notitie">
                                @if ($item->userNote !== null)
                                    {{ $item->userNote }}
                                @endif
                            </div>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>

</html>
