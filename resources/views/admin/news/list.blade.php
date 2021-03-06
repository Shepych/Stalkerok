@extends('layouts.admin')

@section('content')
    <h1>Список новостей</h1>
    <a href="{{ route('admin.panel') }}">На главную</a>

    @foreach($list as $new)
        <div style="margin-bottom: 10px;background-color: #4a5568;padding: 10px;">
            <h3>{{ $new->title }}</h3>
            <p>{!! $new->content !!}</p>
            <img src="{{ $new->img }}" width="200px">
            <hr>
            <a href="{{ route('admin.new.update', $new->id) }}">Изменить</a>
            <form action="{{ route('admin.new.delete', $new->id) }}" method="post">
                @csrf
                <input type="submit" onclick="return window.confirm('Подтвердите удаление');" value="Удалить">
            </form>
        </div>
    @endforeach
@endsection
