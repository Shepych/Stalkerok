@extends('layouts.main')

@section('content')
    <div class="container d-flex justify-content-center align-items-center flex-column">
        <div class="h-100 p-5 text-white bg-gradient bg-dark rounded-3 mt-4">
            <div class="d-flex flex-column text-center mb-2">
                <h1>{{ $mod->title }}</h1>
                <i><strong>Рейтинг: <span class="{{ defineReviewColor($mod->rating) }}">{{ $mod->rating != 0 ? round($mod->rating, 1) : 0 }}</span></strong></i>
            </div>
            <img src="{{ $mod->img }}" width="400px">
        </div>

        <div class="h-100 p-5 bg-warning bg-gradient border rounded-3 mt-4">
            {!! $mod->topic()->content()->content !!}
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
                <div class="alert alert-{{ defineReviewColor($review->rating) }}" role="alert">
                    <div style="width: 100px;background-color: black;color:white">
                        {{ $review->user->name }}
                    </div>
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

        <div style="width: 600px">
            <h3>Комментарии</h3>
            <div class="comments__list">
                Список комментариев
                @foreach($mod->comments() as $comment)
                    <div style="background-color: #0a53be;color:white;margin-bottom: 20px;">
                       <div style="width: 100px;background-color: green">
                           {{ $comment->user->name }}
                       </div>

                        <div>
                            {!! $comment->content !!}
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pagination">
                {{ $mod->comments()->links('pagination.classic') }}
            </div>

            <form class="ajax__form" style="width:400px" action="{{ route('mod.comment', $mod->id) }}" method="post">
                @csrf
                <div class="form-group">
                    <textarea id="tinymce" name="comment" cols="30" rows="10" placeholder="Введите сообщение" style="width: 600px"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Отправить комментарий</button>
            </form>
        </div>
    </div>
@endsection

@section('js')
    {{--  TinyMCE  --}}
    <script src="https://cdn.tiny.cloud/1/i03451ey1y4ll2i3ausrnlji7w92qwov6ma8vcqy47oc8ktw/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="/js/app.js"></script>
    <script src="/js/main/main.js"></script>
@endsection
