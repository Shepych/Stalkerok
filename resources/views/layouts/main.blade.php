<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/css/app.css">
    <title>Stalkerok</title>
</head>

<body>
    <main id="panel">
        @include('blocks.main.header')

        @yield('content')

        @include('blocks.main.footer')
    </main>

    {{--  TinyMCE  --}}
    <script src="https://cdn.tiny.cloud/1/i03451ey1y4ll2i3ausrnlji7w92qwov6ma8vcqy47oc8ktw/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    @yield('js')
</body>
</html>
