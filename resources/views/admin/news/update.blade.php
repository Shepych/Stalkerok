@extends('layouts.admin')

@section('content')
    <h1>Изменение данных у новости</h1>

    @if(session('success'))
        <div style="background-color: green;color: white;font-size: 20px">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->post->any())
        <div class="alert alert-danger" style="margin-left: 7px;margin-right: 7px;">
            <ul style="margin-bottom: 0;">
                @foreach ($errors->post->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('new.update', $article->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="text" name="title" placeholder="Заголовок" value="{{ $article->title }}"><br>
        <textarea name="content" id="" cols="30" rows="10">{{ $article->content }}</textarea><br>
        <img src="{{ $article->img }}" width="200px" alt="">
        <input type="file" name="cover"><br>
        <input type="submit" value="Сохранить изменения">
    </form>

    <a href="{{ route('admin.news.list') }}">Назад к списку</a>
    <a href="{{ route('admin.panel') }}">На главную</a>
@endsection
