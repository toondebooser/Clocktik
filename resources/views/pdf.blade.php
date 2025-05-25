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

        table {
            width: 100%;
            border-collapse: separate;
            /* Prevents collapsing row heights */
            border-spacing: 0;
            /* Maintains border appearance */
        }

        .timesheetRow {
            min-height: 50px;
            /* Set row height to 50px */
            height: 50px;
            /* Enforce exact height */
        }

        table,
        th,
        td {
            border: 0.4px solid #333;
        }

        th {
            height: 70px;
            font-size: x-large;
            background-color: lightblue;
        }

        td {
            width: 130px;
            min-height: 50px;
            height: 50px;
            vertical-align: middle;
            box-sizing: border-box;

        }

        .date {
            text-align: center;
        }

        .displayRegular {
            text-align: center;
            color: #2626da;
            padding: 5px;
            /* Reduced padding for smaller height */
        }


        .displayBreak {
            text-align: center;
            color: #da0a0a;
            padding: 5px;
        }

        .displayOvertTime {
            text-align: center;
            color: black padding: 5px;
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
            color: black;
            background-color: lightgrey;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid black;
        }

        .text-danger {
            color: red;
        }
    </style>
</head>

<body>

    <div class="content">
        <header>
            <h1>{{ date('F', strtotime($dayTotal[0]->Month)) }} {{ $user->name }}</h1>
        </header>
        <div class="totalContainer">
            <h3>Maand Totaal</h3>
            @if (isset($monthlyTotal))

                @foreach ($monthlyTotal as $item)
                    <div class="displayTotalRegular total">
                        Gewerkt {{ $item->RegularHours }}
                    </div>
                    <div class="displayTotalBreak total">
                        Gepauzeerd {{ $item->BreakHours }}
                    </div>
                    <div class="displayTotalOverTime total">
                        Overuren {{ $item->OverTime }}
                    </div>
                @endforeach
            @else
                <div class="text-danger">Something went wrong pls call Toon.</div>
            @endif
        </div>
    </div>
    <h3>Uurrooster</h3>
    <main>
        <table>
            <thead>
                <tr class="pdfTableHeader">
                    <th>Datum</th>
                    <th>Dag type</th>
                    <th>Pauze</th>
                    <th>Overuren</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dayTotal as $item)
                    <tr class="timesheetRow {{ $item->NightShift ? 'nightShift' : '' }}"
                        >
                        {{-- date --}}
                        <td class="date" id="{{ $item->id }}">
                            <?php
                            $toTime = strtotime($item->Month);
                            $days = ['Mon' => 'Ma', 'Tue' => 'Di', 'Wed' => 'Wo', 'Thu' => 'Do', 'Fri' => 'Vr', 'Sat' => 'Za', 'Sun' => 'Zo'];
                            $englishDay = date('D', $toTime);
                            $dutchDay = $days[$englishDay];
                            $dayOfMonth = date('d', $toTime);
                            echo $dutchDay . ' ' . $dayOfMonth;
                            ?>
                        </td>
                        {{-- regular --}}
                        <td>
                            <div class="displayRegular">
                                @if ($item->RegularHours !== $user->company->day_hours && $item->Weekend == false && $item->type == 'workday')
                                    <s>{{ $item->RegularHours }}</s>
                                    => {{ $user->company->day_hours }}
                                @elseif($item->Weekend == true && $item->type == 'workday')
                                    Weekend
                                @elseif ($item->Weekend == false && $item->type !== 'workday')
                                    {{ $item->type }}
                                @else
                                    {{$item->RegularHours}}
                                @endif
                               
                            </div>
                        </td>
                        {{-- break --}}
                        <td>
                            <div class="displayBreak">
                        {{ $item->BreakHours }}

                        </div>
                        </td>
                        {{-- overTime --}}
                        <td>
                            <div class="displayOvertTime">
                                {{ $item->OverTime }}
                            </div>
                        </td>
                        {{-- notes --}}
                        {{-- <td>
                            <div class="notitie">
                                @if ($item->userNote !== null)
                                    {{ $item->userNote }}
                                @endif
                            </div>
                        </td> --}}

                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>

</html>
