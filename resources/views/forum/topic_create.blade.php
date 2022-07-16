@extends('layouts.main')

@section('content')
    <div class="container d-flex justify-content-center align-items-center flex-column">
        <h1 class="m-4">Создание топика</h1>
        <form class="ajax__form" action="{{ route('topic.create') }}" method="post">
            @csrf
            <input class="form-control mb-4" type="text" name="title" placeholder="Заголовок">
            <textarea name="content" id="tinymce" placeholder="Контент"></textarea>
            <input class="btn btn-primary" type="submit" value="Отправить">
        </form>
    </div>
@endsection

@section('js')
    {{--  TinyMCE  --}}
    <script src="https://cdn.tiny.cloud/1/i03451ey1y4ll2i3ausrnlji7w92qwov6ma8vcqy47oc8ktw/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="/js/main/main.js"></script>
@endsection
