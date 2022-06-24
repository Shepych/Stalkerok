@extends('layouts.main')

@section('content')
    <h1>{{ $article->title }}</h1>
    <p>{!! $article->content !!}</p>

    <div>
        <h3>Комментарии</h3>
        @foreach($article->comments as $comment)
            <div style="background-color: goldenrod;margin-bottom: 10px">
                <div style="width: 100px;background-color: green">
                    {{ $comment->user->name }}
                </div>
                {!! $comment->content !!}
            </div>
        @endforeach
    </div>

    <form style="width: 600px" action="{{ route('new.comment', $article->id) }}" method="post">
        @csrf
        <textarea name="content" id="tinymce" cols="30" rows="10"></textarea>
        <input type="submit" value="Отправить комментарий">
    </form>
@endsection

@section('js')
    <script src="/js/app.js"></script>
    <script src="/js/main/main.js"></script>
@endsection
