@extends('layout')

@section('content')

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.tailwindcss.com"></script>
        <title>TikTrack Prijzen</title>
        <style>
            body {
                font-family: 'Inter', sans-serif;
                margin: 0;
                color: #1f2937;
                height: 100vh;
            }

            .backdrop {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                backdrop-filter: blur(5px);
                opacity: 100;
                background: linear-gradient(to bottom, #bfdbfe, #d1fae5, #fed7aa);
                pointer-events: none;
                transition: opacity 0.3s ease;
                z-index: -1;
            }

            header {
                margin-top: 10px;
                margin-right: 5px;
            }

            .bodyContent {
                display: block
            }

            .container {
                display: grid;
                grid-column: 1fr;
                width: fit-content;
                margin: 0 auto;
                padding: 40px 20px;
                animation: fadeIn 0.8s ease-in;
            }

            .fade-in {
                animation: fadeIn 0.8s ease-in;
            }

            
            .logo {
                display: block;
                margin: 30px auto 0;
                height: 80px;
                opacity: 1;
            }

            .logo-small {
                display: block;
                margin: 60px auto;
                height: 60px;
                animation: bounce 2s infinite;
                opacity: 1;
            }
            
            
            h1 {
                text-align: center;
                font-size: 2em;
                margin-bottom: 30px;
                font-weight: bold;
                grid-column: 1/2;
                grid-row: 1/2;
            }
            
            p.subtitle {
                text-align: center;
                color: #4b5563;
                font-size: 1.1em;
            }

            .card {
                max-width: 400px;
                background: white;
                border-radius: 16px;
                padding: 30px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            }

            .card h2 {
                font-size: 1.5em;
                margin-bottom: 20px;
            }

            .card p {
                margin-bottom: 20px;
                color: #374151;
            }
            
            .card ul {
                list-style: disc inside;
                padding-left: 20px;
                margin-bottom: 30px;
            }
            
            .btn {
                background-color: #4FAAFC;
                color: white;
                border: none;
                padding: 15px 30px;
                font-size: 1em;
                border-radius: 10px;
                cursor: pointer;
                /* transition: background 0.3s ease; */
            }

            .btn:hover {
                background-color: #379fe7;
            }

            .text-center {
                text-align: center;
            }
            @media (min-width: 768px) {
                .backdrop {
                    display: block
                }
            }
            @keyframes bounce {
                
                0%,
                100% {
                    transform: translateY(0);
                }
        
                50% {
                    transform: translateY(-5px);
                }
            }
        
            @keyframes fadeIn {
                0% {
                    opacity: 0;
                    transform: translateY(10px);
                }
        
                100% {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    </head>

        <div class="content">
            <img src="{{ asset('images/TaxusLogo.png') }}" style="opacity: 0" id="companyLogo" alt="Taxus Logo" class="logo">
            <div class="container">
                <h1>Werkuren.be</h1>
                <div class="card">
                    <h2>Standaard Abonnement</h2>
                    <p>Voor slechts <strong style="color: #059669">€29,99/maand</strong> krijg je toegang tot één
                        bedrijfsaccount met <strong style="color: #059669">5 werknemers.</strong> </p>
                    <p>Extra werknemers? Geen probleem. Voeg werknemers toe voor slechts <strong
                            style="color: #059669">€1,99/maand</strong> per extra werknemer.</p>
                    <ul style="color: black; text-align: left;">
                        <li>Live tijdregistratie</li>
                        <li>Decimaal uur notatie</li>
                        <li>Schone, intuïtieve interface</li>
                        <li>PDF-export per maand</li>
                        <li>Data-opslag voor 3 maanden</li>
                    </ul>
                    <div class="text-center" style="display:flex; justify-content:center">
                        <a href="{{route('subscribe.form')}}" class="btn button" style="color: white" >Probeer TikTrack nu</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- <img src="{{ asset('images/TaxusLogo.png') }}" alt="Taxus Logo" class="logo-small"> --}}

@endsection
