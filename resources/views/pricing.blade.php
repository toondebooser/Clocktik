@extends('layout')

{{-- @section('title')
    <h1>Werkuren</h1>
@endsection --}}

@section('content')
    {{-- <h1>Werkuren.be</h1> --}}
    <img src="{{ asset('images/TaxusLogo.png') }}" style="opacity: 0" id="companyLogo" alt="Taxus Logo" class="logo">

    <div class="container">
        <div class="card">
            <h2>Abonnement</h2>
            <p>
                Voor slechts <strong style="color: #059669">€29,99/maand</strong> krijg je toegang tot één
                bedrijfsaccount met <strong style="color: #059669">5 werknemers.</strong>
            </p>
            <p>
                Extra werknemers? Geen probleem. Voeg werknemers toe voor slechts
                <strong style="color: #059669">€1,99/maand</strong> per extra werknemer.
            </p>
            <ul style="color: black; text-align: left;">
                <li>Live tijdregistratie</li>
                <li>Decimaal uur notatie</li>
                <li>Schone, intuïtieve interface</li>
                <li>PDF-export per maand</li>
                <li>Data-opslag voor 3 maanden</li>
            </ul>
            <div class="text-center" style="display:flex; justify-content:center">
                <a href="{{ route('subscribe.form') }}" class="btn button" style="color: white">
                    Vraag je abonnement aan
                </a>
            </div>
        </div>
    </div>

    <style>
        .container {
            display: grid;
            grid-column: 1/13;
            grid-row: 3/5;
            grid-column: 1fr;
            width: fit-content;
            overflow-y: auto;
            margin: 0 auto;
            padding: 0px 20px;
            animation: fadeIn 0.8s ease-in;
        }

        #companyLogo {
            grid-column: 1/13;
            justify-self: center;
            padding-bottom: 10px;
        }
       
        .card {
            grid-column: 1/13;
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

        .btn {
            background-color: #4FAAFC;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1em;
            border-radius: 10px;
            cursor: pointer;
        }

        .card ul {
            list-style-type: disc;
            padding-left: 20px;
            margin-bottom: 20px;
            color: black;
        }

        .btn:hover {
            background-color: #379fe7;
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
@endsection
