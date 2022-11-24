<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Анализатор страниц</title>

    @vite(['resources/sass/app.scss'])

    <script>
        /*to prevent Firefox FOUC, this must be here*/
        let FF_FOUC_FIX;
    </script>
</head>
<body class="min-vh-100 d-flex flex-column">
    <header class="flex-shrink-0">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark px-3">
            <a class="navbar-brand" href="{{ route('home') }}">Анализатор страниц</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Главная</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('urls.index') ? 'active' : '' }}" href="{{ route('urls.index') }}">Сайты</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="flex-grow-1">
        @yield('content')
    </main>

    <footer class="border-top py-3 mt-5 flex-shrink-0">
        <div class="container-lg">
            <div class="text-center">
                <a href="https://hexlet.io/pages/about" target="_blank">Hexlet</a>
            </div>
        </div>
    </footer>
</body>
</html>
