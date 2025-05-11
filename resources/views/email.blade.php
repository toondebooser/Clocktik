<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #dddddd;
            border-radius: 5px;
        }

        .buttonText {
            color: white
        }

        .email-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            text-decoration: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <h2>Verifieer uw e-mailadres</h2>
        <p>Hallo, {{ $name }}</p>
        <p>Klik op de onderstaande knop om je e-mailadres te verifiëren:</p>
        <a href="{{ $url }}" class="email-button">
            <p class="buttonText">E-mailadres verifiëren</p>
        </a>
        @if (isset($companyCode))
            <h2>Jouw bedrijfsaccount</h2>
            <p>Je hebt een nieuw bedrijf gestart en jouw account bezit administrator rechten over het aangemaakte
                bedrijf:{{$company->company_name}}</p><br>
                <p>De volgende code hebben jou werknemers nodig om zich te kunnen registreren:</p>
                <h2><strong>{{$companyCode}}</strong></h2>
            
                <h3>jou inloggegevens:</h3>
                <ul>
                    <li>naam: <strong>{{$adminEmail}}</strong></li>
                    <li>passwoord: <strong>tiktrackadmin</strong></li>
                </ul>
                <p style="color: red">! Dit is een standaard passwoord gelieve deze na verificatie aan te passen via wachtwoord vergeten</p>

        @endif
        <p>Als u dit account niet heeft aangemaakt, hoeft u geen verdere actie te ondernemen.</p>
        <p>Bedankt voor het gebruik van onze applicatie!</p>
    </div>
</body>

</html>
