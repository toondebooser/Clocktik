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
        .buttonText{
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
        <h2>Verify Your Email Address</h2>
        <p>Hello,</p>
        <p>Please click the button below to verify your email address:</p>
        <a href="{{ $url }}" class="email-button"><p class="buttonText">Verify Email Address</p></a>
        <p>If you did not create this account, no further action is required.</p>
        <p>Thank you for using our application!</p>
    </div>
</body>
</html>
