@extends('layouts.admin')

@section('content')
    <h1>Добавление статьи</h1>

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
    <form action="{{ route('admin.new.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="text" name="title" placeholder="Заголовок" value="{{ $article->title }}"><br>
        <textarea name="content" id="tinymce" cols="30" rows="10">{{ $article->topic()->content()->content }}</textarea><br>
        <input type="file" name="cover"><br>
        <input type="submit" value="Добавить">
    </form>

    <textarea id="code_content" rows="8" cols="80"></textarea><br>
    <button onclick="addCode()"type="button">Добавить CODE</button><br>

    <a href="{{ route('admin.panel') }}">На главную</a>
@endsection
