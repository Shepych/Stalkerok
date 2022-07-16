@extends('layouts.admin')

@section('content')
    <h1>Список модов</h1>
    <a href="{{ route('admin.panel') }}">На главную</a>

    @foreach($list as $mod)
        <div style="margin-bottom: 10px;background-color: #4a5568;color: white;padding: 10px;">
            <h3>{{ $mod->title }}</h3>
            <p>{!! $mod->content !!}</p>
            <div style="display: flex">
                <img src="{{ $mod->img }}" width="200px" style="margin-right: 100px">
                <div style="display: flex;background-color: darkgoldenrod;padding: 10px;border-radius: 10px">
                    @foreach($mod->images as $image)
                        <img src="{{ $image->href }}" style="width: 140px;" alt="">
                    @endforeach
                </div>
            </div>

            <hr>
            <a href="{{ route('admin.mod.update', $mod->id) }}">Изменить</a>
            <form action="{{ route('admin.mod.delete', $mod->id) }}" method="post">
                @csrf
                <input type="submit" onclick="return window.confirm('Подтвердите удаление');" value="Удалить">
            </form>
        </div>
    @endforeach
@endsection
