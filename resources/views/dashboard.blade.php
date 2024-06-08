@extends('layout')
@section('title')
<h2>Welcome {{ $user->name }}</h2>
@endsection
@section('userDashboard')

@if (session('error'))
<div class="error">
    {{ session('error') }}
    <a class="removeError" href="">ok</a>
    </div>
    @endif
    
    @if ($shiftStatus == false)
        <a href="#" class="startButton"
            onclick="openConfirmationModal('Klaar om te werken ?', '{{ route('start') }}')">
            <p class="buttonText">Start</p>
        </a>
    @else
            <form method="POST" name="userNoteForm" action="{{ route('dashboard') }}" class="userNoteForm">
                @csrf
                <textarea class="userNoteInput" name="userNote" rows="2" cols="30">{{ $userNote }}</textarea> <br>
                <input class="button" type="submit" value='Voeg notitie toe'>
            </form>
        @if ($shiftStatus == true && $breakStatus == true)
            <a href="#"
                onclick="openConfirmationModal('Are you sure you want to start working again?', '{{ route('stopBreak') }}')"
                class="breakButton">
                <p class="buttonText">Back to work</p>
            </a>
          @else
            <a href="#"
            onclick="openConfirmationModal('Zedde zeker da ge wilt pauzeren ?', '{{ route('break') }}')"
            class="breakButton">
            <p class="buttonText">Break</p>
            </a>
        @endif
            <a href="#"
                onclick="openConfirmationModal('Gade nu al naar huis ?', '{{ route('stop') }}')"
                class="stopButton">
                <p class="buttonText">Stop</p>
            </a>
    @endif

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
            margin: 15% auto;
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
            function openConfirmationModal(message, actionUrl) {
                document.getElementById('modalText').innerText = message;
                document.getElementById('confirmButton').dataset.url = actionUrl;
                document.getElementById('confirmationModal').style.display = "block";
            }
    
            function closeModal() {
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
