{{--{{ dd(request()->is('news')) }}--}}
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/css/main.css">
    <title>Stalkerok</title>
</head>

<body>
    <main id="panel">
        <div class="container ajax__wrap">
            @include('blocks.main.header')

            @yield('content')

            @include('blocks.main.footer')
        </div>
    </main>

    <script src="/js/app.js"></script>
{{--    <script>$('[data-active={{ $activePage }}]').addClass('active')</script>--}}
    @yield('js')
</body>
</html>
