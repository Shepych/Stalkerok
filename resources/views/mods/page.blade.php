@extends('layouts.main')

@section('content')
    <h1>{{ $mod->title }} - {{ $mod->rating != 0 ? round($mod->rating, 1) : null }}</h1>
    <img src="{{ $mod->img }}" width="400px">
    <div>
        {!! $mod->content !!}
    </div>

    <h3>Галерея</h3>
    <div class="mod__gallery">
        @foreach($mod->images as $img)
            <img src="{{ $img->href }}" width="80px">
        @endforeach
    </div>

    <h3>Тэги</h3>
    <div>
        @foreach($tags as $tag)
            @if(array_search($tag->id, convertTagsFromString($mod->tags)) !== false)
                <span style="padding: 10px;background-color: darkslategrey;color:white;border-radius: 5px">{{ $tag->title }}</span>
            @endif
        @endforeach
    </div>

    <h3>Отзывы</h3>
    @if(session('success'))
        <div style="color:green">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="color:red">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->post->any())
        <div style="background-color: red;color:white">
            @foreach($errors->post->all() as $error)
                {{ $error }}<br>
            @endforeach
        </div>
    @endif

    <div class="reviews__list">
        @foreach($mod->reviews as $review)
            <div style="background-color: {{ defineReviewColor($review->rating) }}">
                <h5>{{ $review->title }}</h5>
                <p>{!! $review->content !!}</p>
                <span>{{ $review->rating }}</span>
            </div>
        @endforeach
    </div>
    <div>
        <form class="ajax__form" action="{{ route('mod.review', $mod->id) }}" method="post">
            @csrf
            <label>Заголовок <br>
                <input type="text" name="title">
            </label><br>

            <label>Контент <br>
                <textarea id="tinymce" name="content" id="" cols="30" rows="10"></textarea>
            </label>

            <br>
            <input name="rating" type="range" min="1" max="10" step="1" start="10">
            <input type="submit" value="Отправить отзыв">
        </form>
    </div>

    <div>
        <h3>Комментарии</h3>
        <div class="comments__list">
            Список комментариев
            @foreach($mod->comments as $comment)
                <div style="background-color: #0a53be;color:white;margin-bottom: 20px;">
                    {!! $comment->content !!}
                </div>
            @endforeach
        </div>

        <form style="width:400px" action="{{ route('mod.comment', $mod->id) }}" method="post">
            @csrf
            <div class="form-group">
                <textarea id="tinymce" name="comment" cols="30" rows="10" placeholder="Введите сообщение"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Отправить комментарий</button>
        </form>
    </div>
@endsection

@section('js')
    <script src="/js/app.js"></script>
    <script src="/js/main/main.js"></script>
@endsection
