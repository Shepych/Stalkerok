@extends('layouts.main')

@section('content')
    <h1>Список новостей</h1>
    @foreach($articles as $article)
        <a href="{{ route('new.page', $article->url) }}" style="display:flex;flex-direction: column;justify-content: center;align-items: center; margin-bottom: 20px;width: 400px;background-color: #4a5568;border-radius: 10px;padding: 20px;">
            <h3 style="margin-top: 0;color: white">{{ $article->title }}</h3>
            <img src="{{ $article->img }}" width="200px">
        </a>
    @endforeach

    <div>
        {{ $articles->links('pagination.classic') }}
    </div>
@endsection
