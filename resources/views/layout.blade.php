<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@100;200;300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="icon" href="{{ asset('images/Taxus logo perfect round.png') }}" sizes="192x192" type="image/png">
    @if (env('APP_ENV') == 'local')
        @vite('resources/css/app.css', 'resources/js/app.js')
    @elseif (env('APP_ENV') == 'production')
        <link rel="stylesheet" href="{{ asset('public/build/assets//app-721905ac.css ') }}">
    @endif

    <style>
        :root {
            --primary-color: {{ auth()->check() ? auth()->user()->company->color ?? 'black' : 'black' }};
        }
    </style>


    <title>Tiktrack</title>



</head>

<body>
    <?php
    $currentUser = auth()->user(); ?>
    <div class="bodyContent">

        @yield('error')
        @yield('title')
        @yield('content')

        <header>
            @yield('header')
            <a class="headerLinks" href="{{ route('home') }}">Home</a>
            @auth
                @if ($currentUser->admin == false)
                    <a class="headerLinks" href="{{ route('dashboard') }}">Timeclock</a>
                    <a class="authLinks button"href="{{ route('myProfile') }}">Mijn profiel</a>
                @else
                    <a class="authLinks button"
                        href = "{{ route('myList', ['type' => 'Personeel', 'company_code' => $currentUser->company_code]) }}">Personeel</a>
                @endif
                <a class="authLinks button" href="{{ route('logout') }}">Logout</a>
            @endauth
            @guest
                <a class="authLinks button" href="{{ route('login') }}">Login</a>
            @endguest
        </header>

        @yield('login')
        @yield('userDashboard')
        @yield('newUser')

        <footer> &copy Toon De Booser</footer>
    </div>
</body>

</html>
