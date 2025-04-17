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
    <link rel="icon"
        href="{{ auth()->check() && auth()->user()->company && auth()->user()->company->company_logo ? asset(auth()->user()->company->company_logo) : asset('images/TaxusLogo.png') }}"
        sizes="192x192" type="image/png">
    @if (env('APP_ENV') == 'local')
        @vite('resources/css/app.css', 'resources/js/app.js')
    @elseif (env('APP_ENV') == 'production')
        <link rel="stylesheet" href="{{ asset('public/build/assets//app-721905ac.css ') }}">
    @endif

    <style>
        :root {
            --primary-color: {{ auth()->check() ? auth()->user()->company->company_color ?? '#4FAAFC' : '#4FAAFC' }};
            
        }
        /* body{
            background-color: 
        } */
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

        @if (session('success'))
            <div class="success">
                {{ session('success') }} <br>
                <a class="removeError" href="">OK</a> <!-- "#" voorkomt page reload -->
            </div>
        @endif
        @if ($errors->any())
            <div class="error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <a class="removeError" href="">ok</a>
            </div>
        @endif
        @yield('header')
        <header>
            @auth
                <div id="side-menu" class="side-menu">
                    <a href="{{ route('adminSettings', ['company_code' => $currentUser->company_code]) }}">
                        <img style="height: 40px" src="{{ asset('images/settings.png') }}" alt="settings">
                    </a>
                    <a class="headerLinks" href="{{ route('dashboard') }}">Timeclock</a>
                    @if ($currentUser->god)
                        <a class="authLinks button"
                            href="{{ route('myList', ['type' => 'Bedrijven', 'company_code' => $currentUser->company_code]) }}">Bedrijven</a>
                    @else    
                            <a class="authLinks button"
                            href="{{ route('myList', ['type' => 'Personeel', 'company_code' => $currentUser->company_code]) }}">Personeel</a>
                    @endif
                </div>
                <a class="headerLinks" href="{{ route('home') }}">Home</a>
                @if (
                    ($currentUser->god && $currentUser->company->admin_timeclock) ||
                        ($currentUser->admin && $currentUser->company->admin_timeclock))
                    <div class="browserHeader">
                        <a href="{{ route('adminSettings', ['company_code' => $currentUser->company_code]) }}">
                            <img style="height: 40px" src="{{ asset('images/settings.png') }}" alt="settings">
                        </a>
                        <a class="headerLinks" href="{{ route('dashboard') }}">Timeclock</a>
                        <a class="authLinks button"
                            href="{{ route('myList', ['type' => 'Personeel', 'company_code' => $currentUser->company_code]) }}">Personeel</a>

                    </div>
                    <div class="headerLinks " id="nav-icon4" onclick="toggle()">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                @elseif ($currentUser->god)
                    <a class="authLinks button"
                        href="{{ route('myList', ['type' => 'Bedrijven', 'company_code' => $currentUser->company_code]) }}">Bedrijven</a>
                @elseif (!$currentUser->admin)
                    <a class="headerLinks" href="{{ route('dashboard') }}">Timeclock</a>
                    @if ($currentUser->admin && !$currentUser->god)
                        <a href="{{ route('adminSettings', ['company_code' => $currentUser->company_code]) }}">
                            <img style="height: 40px" src="{{ asset('images/settings.png') }}" alt="settings">
                        </a>
                        <a class="authLinks button"
                            href="{{ route('myList', ['type' => 'Personeel', 'company_code' => $currentUser->company_code]) }}">Personeel</a>
                    @endif
                    <a class="authLinks button" href="{{ route('myProfile') }}">Mijn profiel</a>
                @else
                    <a href="{{ route('adminSettings', ['company_code' => $currentUser->company_code]) }}">
                        <img style="height: 40px" src="{{ asset('images/settings.png') }}" alt="settings">
                    </a>
                    <a class="authLinks button"
                        href="{{ route('myList', ['type' => 'Personeel', 'company_code' => $currentUser->company_code]) }}">Personeel</a>
                @endif
                <a class="authLinks button" href="{{ route('logout') }}">Logout</a>
                <div class="backdrop"></div>
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
    <script>
        const toggle = () => {
            const element = document.getElementById('side-menu');
            const icon = document.getElementById('nav-icon4');
            element.classList.toggle('slide-in');
            icon.classList.toggle('open');
        };
        window.toggle = toggle;

        document.addEventListener('click', (event) => {
            const menu = document.getElementById('side-menu');
            const icon = document.getElementById('nav-icon4');
            const isMenuOpen = menu.classList.contains('slide-in');
            const clickedInsideMenu = menu.contains(event.target);
            const clickedToggle = event.target.closest('#nav-icon4');

            if (isMenuOpen && !clickedInsideMenu && !clickedToggle) {
                menu.classList.remove('slide-in');
                icon.classList.remove('open');
            }
        });
    </script>
</body>

</html>
