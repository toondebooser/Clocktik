@extends('layout')
@section('title')
    <h2>Welcome {{ $user->name }}</h2>
    <h3 style="height:fit-content;grid-row: 3/4; grid-column: 1/13; justify-self:center" id="clock"></h3>
@endsection
@section('content')

    {{-- @if (session('error'))
        <div class="error">
            {{ session('error') }}
            <a class="removeError" href="">ok</a>
        </div>
    @endif --}}

    @if ($shiftStatus == false)
        <a href="#" class="startButton" onclick="openConfirmationModal('Klaar om te werken ?', '{{ route('start') }}')">
            <p class="buttonText">Start</p>
        </a>
    @else
        <form method="POST" name="userNoteForm" action="{{ route('dashboard') }}" class="userNoteForm">
            @csrf
            <textarea class="userNoteInput" name="userNote" rows="2" cols="30">{{ $userNote }}</textarea> <br>
            <input class="button" type="submit" value='Voeg notitie toe'>
        </form>
        @if ($shiftStatus == true && $breakStatus == true)
            <a href="#" onclick="openConfirmationModal('wil je terug aan het werk?', '{{ route('stopBreak') }}')"
                class="breakButton">
                <p class="buttonText">Uit pauze</p>
            </a>
        @else
            <a href="#" onclick="openConfirmationModal('Ben je zeker dat je wil pauzeren?', '{{ route('break') }}')"
                class="breakButton">
                <p class="buttonText">pauze</p>
            </a>
        @endif
        <a href="#" onclick="openConfirmationModal('Ga je naar huis?', '{{ route('stop') }}')" class="stopButton">
            <p class="buttonText">Stop</p>
        </a>
    @endif





    {{-- <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            justify-self: center;
            align-self: center;
            height: fit-content;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            text-align: center;
            border-radius: 8px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-button {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .modal-button:hover {
            background-color: #0056b3;
        }

        .modal-button.cancel {
            background-color: #6c757d;
        }

        .modal-button.cancel:hover {
            background-color: #5a6268;
        }
    </style> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const shiftStatus = {{ json_encode($shiftStatus) }};
            const breakStatus = {{ json_encode($breakStatus) }};

            const startBreak = new Date("{{ $startBreak }}").getTime();
            const startShift = new Date("{{ $start }}").getTime();
            const clockElement = document.getElementById('clock');
            const breakHours = parseFloat("{{ $breakHours }}"); 
            const workedHours = parseFloat("{{$workedHours}}");
            
            const workedMilliseconds = workedHours * 60 * 60 * 1000;
            const breakMilliseconds = breakHours * 60 * 60 * 1000;
            console.log(workedMilliseconds)


            function updateClock(type) {
                
                const now = new Date().getTime(); 
                
                let elapsed = null
                if(type == "work"){
                 elapsed = now - startShift   + workedMilliseconds    ;
                 
                 
                 
                } else{
                     elapsed = now - startBreak + breakMilliseconds;
                }

                const hours = Math.floor(elapsed / (1000 * 60 * 60));
                const minutes = Math.floor((elapsed % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((elapsed % (1000 * 60)) / 1000);

                clockElement.innerText =
                    `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }

            setInterval(() => {
                if (shiftStatus && !breakStatus) {
                    updateClock("work");
                } else if (shiftStatus && breakStatus) {
                    updateClock("break")
                } else {
                    null
                }

            }, 1000);
        });
    </script>



@endsection
