@extends('layouts.main')

@section('content')
    <div class="container d-flex justify-content-center align-items-center flex-column">
        <div class="d-flex flex-column justify-content-center align-items-center">
            <h1 class="m-4 mb-0">{{ $topic->title }}</h1>
            <i>{{ $topic->created_at }}</i>
            <a href="{{ route('home', $topic->author()->id) }}"><i class="mb-4">{{ $topic->author()->name }}</i></a>
        </div>

        <div class="topic-content">
            {!! $topic->content()->content !!}
        </div>

        <div class="topic-comments">
            @foreach($topic->comments() as $comment)
                <div class="bg-info mb-4 p-3">
                    {!! $comment->content !!}
                </div>
            @endforeach
        </div>

        <div class="container d-flex justify-content-center align-items-center">
            {{ $topic->comments()->links('pagination.classic') }}
        </div>

        <form action="{{ route('topic.comment', $topic->id) }}" class="ajax__form" method="post">
            @csrf
            <textarea name="content" id="tinymce" placeholder="Введите ваше сообщение"></textarea>
            <input type="submit" class="btn btn-primary" value="Отправить">
        </form>
    </div>
@endsection

@section('js')
    {{--  TinyMCE  --}}
    <script src="https://cdn.tiny.cloud/1/i03451ey1y4ll2i3ausrnlji7w92qwov6ma8vcqy47oc8ktw/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="/js/main/main.js"></script>
@endsection
