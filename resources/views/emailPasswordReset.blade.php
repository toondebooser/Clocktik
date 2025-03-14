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
        <h1>Reset je wachtwoord</h1>
        <p>Hallo {{ $user->name ?? 'User' }},</p>
        <p>We hebben een aanvraag ontvangen om jou wachtwoord te resetten klik onderaan op de knopo om naar de herstel
            pagina te gaan:</p>
        <a href="{{ $url }}" class="email-button">
            <p class="buttonText">Reset Password</p>
        </a>
        <p>Als de knop niet werkt kopieer onderstaande url in je browser:</p>
        <p>{{ $url }}</p>
        <p>Indien jij dit niet was gelieven ons zo snel mogelijk op de hoogte te brengen.</p>
        <p>Bedankt<br>ticktrack</p>
    </div>
</body>

</html>
