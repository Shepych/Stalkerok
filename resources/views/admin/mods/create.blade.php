@extends('layouts.admin')

@section('content')
    <h1>Создание мода</h1>
    @if(session('success'))
        <div style="background-color: green">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->post->any())
        <div style="background-color: red;color:white">
        @foreach($errors->post->all() as $error)
                {{ $error }}<br>
        @endforeach
        </div>
    @endif
    <form style="display:flex;flex-direction:column;margin-bottom:20px" action="{{ route('mod.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="text" name="title" value="{{ $mod->title }}" placeholder="Название"><br>

        <textarea name="content" id="" cols="30" rows="10">{{ $mod->content }}</textarea>

        <label>Обложка <br>
            <input type="file" name="cover">
        </label>

        <label>Скриншоты <br>
            <input type="file" multiple="multiple" name="screenshots[]">
        </label>

        <label>Размер модификации <br>
            <input type="text" name="memory">
        </label>

        <label>Видео гайд <br>
            <input type="text" name="video">
        </label>

        <div style="background-color: #4a5568;color: white;padding: 20px;border-radius: 10px;width: 300px">
            <label>
                <input type="text" name="torrent"> Torrent
            </label><br>
            <label>
                <input type="text" name="yandex"> Yandex
            </label><br>
            <label>
                <input type="text" name="google"> Google
            </label>
        </div>

        <label>Тэги <br>
            <select multiple="multiple" name="tags" id="">
            @foreach($tags as $tag)
                <option value="{{ $tag->id }}">{{ $tag->title }}</option>
            @endforeach
            </select>
        </label>

        <label>Платформа <br>
            <select name="platform">
                <option value="1">Тень Чернобыля</option>
                <option value="2">Чистое Небо</option>
                <option value="3">Зов Припяти</option>
            </select>
        </label>

        <input type="submit" value="Добавить модификацию" style="width: 200px">
    </form>

    <a href="{{ route('admin.mods.list') }}">Назад к списку модов</a><br>
    <a href="{{ route('admin.panel') }}">На главную</a>
@endsection
