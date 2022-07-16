@extends('layouts.main')

@section('content')
    <div class="container d-flex justify-content-center align-items-center">
        <h1 class="m-4">Форум</h1>
        <a href="{{ route('topic.create') }}">
            <input class="btn btn-primary" type="button" value="Создать тему">
        </a>
    </div>

    @foreach($topics as $topic)
        <a href="{{ route('topic', $topic->url ? $topic->url : 'null') }}" class="d-block alert alert-success" role="alert">
            {{ $topic->title }}
        </a>
    @endforeach

    <div class="container d-flex justify-content-center align-items-center">
        {{ $topics->links('pagination.classic') }}
    </div>
@endsection
