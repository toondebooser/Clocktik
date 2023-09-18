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

     <!-- <link rel="stylesheet" href="{{ URL::asset('resources/css/app.css') }}">  -->
     <!-- <link rel="stylesheet" href="{{ mix('resources/css/app.css') }}"> -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>clockTik</title>
</head>

<body>
    <?php $currentUser = auth()->user(); ?>
    <div class="bodyContent">

        @yield('error')
        @yield('logo')
        
        @yield('title')
        <header>
            @yield('header')
            <a class="headerLinks" href="{{ route('home') }}">Home</a>
            @auth
                <a class="headerLinks" href="{{ route('dashboard') }}">Time Clock</a>
                <a class="authLinks"href="{{route('myProfile')}}">My profile</a>
                <a class="authLinks" href="{{ route('logout') }}">Logout</a>
            @endauth
            @guest
                <a class="authLinks" href="{{ route('login') }}">Login</a>
            @endguest
        </header>

        @yield('login')
        @yield('userDashboard')
        @yield('newUser')

        <footer> &copy Toon De Booser</footer>
    </div>
</body>

</html>
