<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    {{--  Стили для спойлера  --}}
{{--    <style>--}}
{{--        .spoiler {--}}
{{--            border: 1px solid black;--}}
{{--            border-radius: 2px;--}}
{{--            padding: 1px;--}}
{{--            margin: 2px;--}}
{{--            overflow: auto;--}}
{{--            box-shadow: 0 0 3px gray;--}}
{{--        }--}}

{{--        .spoilerhead {--}}
{{--            border: 1px dotted black;--}}
{{--            background: lavender;--}}
{{--            border-radius: 2px;--}}
{{--            padding: 2px;--}}
{{--            font-weight: bold;--}}
{{--            font-size: 10pt;--}}
{{--            cursor: pointer;--}}
{{--        }--}}
{{--        .spoilerbody {--}}
{{--            margin: 5px;--}}
{{--        }--}}
{{--    </style>--}}
</head>

<body>
<main id="panel">
    @include('blocks.admin.header')

    @yield('content')

    @include('blocks.admin.footer')
    <script src="/js/app.js"></script>
    {{--  TinyMCE  --}}
    <script src="https://cdn.tiny.cloud/1/i03451ey1y4ll2i3ausrnlji7w92qwov6ma8vcqy47oc8ktw/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="/js/admin/admin.js"></script>
</main>
</body>
</html>
