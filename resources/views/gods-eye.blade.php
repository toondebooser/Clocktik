@extends('layout') <!-- Adjust to your app's layout -->
@section('title')
    <h2>Logboek</h2>
@endsection
@section('content')
<div class="log-container">
        <div class="search-bar-group">
            <label for="log-timestamp">Zoek op datum of tijd:</label>
            <input type="text" id="log-timestamp" placeholder="Bijv. 2025-05-07 of 15:30" oninput="filterLogs()" />
    
    
            <label for="log-user">Zoek op gebruiker:</label>
            <input type="text" id="log-user" placeholder="Gebruikersnaam" oninput="filterLogs()" />
        </div>

        @if (session('error'))
            <div class="message error">
                <span>{{ session('error') }}</span>
                <a href="#" class="remove-message" onclick="closeMessage(this)">Sluiten</a>
            </div>
        @endif

        @if (empty($logs))
            <p>Geen logboeken beschikbaar.</p>
        @else


            <div class="log-list">
                @foreach ($logs as $log)
                    <div class="log-entry">
                        <div class="log-header">
                            <span class="log-timestamp">{{ $log['timestamp'] }}</span>
                            <span class="log-message">{{ $log['message'] }}</span>
                        </div>
                        <div class="log-context">
                            <ul>
                                @if (!empty($log['context']))
                                    @foreach ($log['context'] as $key => $value)
                                        <li class="log-data"><strong>{{ ucfirst($key) }}:</strong>
                                            {{ is_array($value) ? json_encode($value) : $value }}</li>
                                    @endforeach
                                @else
                                    <li>Geen context beschikbaar.</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    <style>
        .log-container {
            grid-row: 3/5;
            grid-column: 1/13;
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
            overflow-y: auto;
        }

        .log-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
            width: 100%;
        }

        .log-entry {
            display: flex;
            flex-direction: column;
            background-color: white;
            border: 1px solid grey;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
            /* Full width of parent */
            box-sizing: border-box;
        }

        .log-header {
            display: flex;
            flex-direction: column;
            margin-bottom: 10px;
        }

        .log-timestamp {
            font-weight: bold;
            color: #333;
            font-size: 14px;
        }

        .log-message {
            color: #721c24;
            font-size: 16px;
        }

        .log-context ul {
            margin: 0;
            padding-left: 20px;
            list-style-type: disc;
        }

        .log-context li {
            text-align: start;
            margin-bottom: 5px;
            font-size: 14px;
            color: #333;
        }

        /* Reuse error message styles from earlier */
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            animation: fadeIn 0.3s ease-out;
            width: 100%;
            box-sizing: border-box;
        }

        .remove-message {
            color: #721c24;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }

        .remove-message:hover {
            color: #501015;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-out {
            animation: fadeOut 0.3s ease-in forwards;
        }
        .search-bar-group {
    display: flex;
    flex-wrap: wrap;
    position: sticky;
    top: 0;
    z-index: 3;
    background-color: white; /* Ensures it doesn't get transparent when scrolling */
    padding: 15px 0;
    gap: 15px;
    align-items: center;
    margin-bottom: 20px;
    border-bottom: 1px solid #ddd;
}


        .search-bar-group label {
            font-weight: 600;
            font-size: 14px;
        }

        .search-bar-group input {
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .log-container {
                padding: 10px;
            }

            .log-entry {
                padding: 10px;
            }

            .log-timestamp,
            .log-message {
                font-size: 12px;
            }

            .log-context li {
                font-size: 12px;
            }
        }

    </style>

    <script>
        function closeMessage(element) {
            const message = element.parentElement;
            message.classList.add('fade-out');
            setTimeout(() => {
                message.style.display = 'none';
            }, 300);
        }

        function filterLogs() {
            const datetimeInput = document.getElementById('log-timestamp').value.trim();
            const usernameInput = document.getElementById('log-user').value.trim().toLowerCase();

            const logEntries = document.querySelectorAll('.log-entry');

            logEntries.forEach(entry => {
                const timestampText = entry.querySelector('.log-timestamp').textContent.trim();
                const messageText = entry.querySelector('.log-message').textContent;

                // Extract username
                const userMatch = messageText.match(/\[USER-ACTIVITY:\s*(.+?)\]/);
                const extractedUsername = userMatch ? userMatch[1].toLowerCase() : '';

                const timestampMatch = datetimeInput === '' || timestampText.includes(datetimeInput);
                const usernameMatch = usernameInput === '' || extractedUsername.includes(usernameInput);

                const show = timestampMatch && usernameMatch;
                entry.style.display = show ? 'flex' : 'none';
            });
        }
    </script>



@endsection
