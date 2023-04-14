<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    {{-- <link rel="stylesheet" href="{{ URL::asset('build/assets/app-42a44428.css') }}"> --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>clockTik</title>
</head>
<body>
    <?php $currentUser = auth()->user();?>
    <div class="bodyContent">

        @yield('error')

        @yield('title')
        <header>
            @if (isset($currentUser))
                <a href="{{route('dashboard')}}">Dashboard</a>
            @endif
            <a href="{{ route('home') }}">login</a>
            <a href="{{ route('newUser') }}">register</a>
        </header>

        @yield('login')

        @yield('newUser')

        <footer> &copy Toon De Booser</footer>
    </div>
</body>

</html>
