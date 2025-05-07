@extends('layout')
@section('title')
    <h2>Welcome {{ $user->name }}</h2>
    <h3 style="height:fit-content;grid-row: 3/4; grid-column: 1/13; justify-self:center" id="clock"></h3>
@endsection
@section('content')

    @if (session('error'))
        <div class="error">
            {{ session('error') }}
            <a class="removeError" href="">ok</a>
        </div>
    @endif

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
    {{-- <div class="dayStatus" style="grid-column: 1/13; grid-row: 3/4; justify-self: center; align-self: end; height:100px">
        <div style="text-align: center"><span style="color: red">{{date('d-m-y', strToTime($lastWorkedDate))}}</span></div>
        <div class="workedHours">Gelogde werkuren: {{ $workedHours }}</div>
        <div class="pausedHours">Gelogde pauze: {{ $breakHours }}</div>
    </div> --}}
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <p id="modalText"></p>
            <button id="confirmButton" class="modal-button">Confirm</button>
            <button class="modal-button cancel" onclick="closeModal()">Cancel</button>
        </div>
    </div>




    <style>
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgb(0, 0, 0);
            /* Fallback color */
            background-color: rgba(0, 0, 0, 0.4);
            /* Black w/ opacity */
        }

        .modal-content {
            background-color: #fefefe;
            /* margin: 15% auto; */
            justify-self: center;
            align-self: center;
            height: fit-content;
            /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            /* Could be more or less, depending on screen size */
            max-width: 400px;
            /* Maximum width */
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
    </style>

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
                //  console.log(elapsed);
                 
                 
                 
                } else{
                     elapsed = now - startBreak + breakMilliseconds;
                }

                const hours = Math.floor(elapsed / (1000 * 60 * 60));
                const minutes = Math.floor((elapsed % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((elapsed % (1000 * 60)) / 1000);

                // Display the result
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


            const openConfirmationModal = (message, actionUrl) => {
                document.getElementById('modalText').innerText = message;
                document.getElementById('confirmButton').dataset.url = actionUrl;
                document.getElementById('confirmationModal').style.display = "grid";
            }

            const closeModal = () => {
                document.getElementById('confirmationModal').style.display = "none";
            }

            document.getElementById('confirmButton').onclick = function() {
                const actionUrl = this.dataset.url;

                fetch('/confirm-action', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            action: actionUrl
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log(data)
                            window.location.href = actionUrl;
                        } else {
                            alert('Failed to confirm action.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            window.onclick = function(event) {
                const modal = document.getElementById('confirmationModal');
                if (event.target == modal) {
                    closeModal();
                }
            };

            window.openConfirmationModal = openConfirmationModal;
            window.closeModal = closeModal;
        });
    </script>



@endsection
