@extends('layouts.main')

@section('content')
    <div class="container d-flex justify-content-center align-items-center flex-column p-4">
        <h1>{{ $article->title }}</h1>
        <p>{!! $article->topic()->content()->content !!}</p>

        <div>
            <h3>Комментарии</h3>
            @foreach($article->comments() as $comment)
                <div style="background-color: goldenrod;margin-bottom: 10px; min-width: 700px">
                    <div style="width: 100px;background-color: green">
                        {{ $comment->user->name }}
                    </div>
                    {!! $comment->content !!}
                </div>
            @endforeach
        </div>

        {{ $article->comments()->links('pagination.classic') }}

        <form class="ajax__form" style="width: 600px" action="{{ route('new.comment', $article->id) }}" method="post">
            @csrf
            <textarea name="content" id="tinymce" cols="30" rows="10"></textarea>
            <input type="submit" value="Отправить комментарий">
        </form>
    </div>
@endsection

@section('js')
    {{--  TinyMCE  --}}
    <script src="https://cdn.tiny.cloud/1/i03451ey1y4ll2i3ausrnlji7w92qwov6ma8vcqy47oc8ktw/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="/js/app.js"></script>
    <script src="/js/main/main.js"></script>
@endsection
