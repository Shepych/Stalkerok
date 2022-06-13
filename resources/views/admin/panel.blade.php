@extends('layouts.admin')

@section('content')
    <h1>Панель администратора</h1>
    <div style="padding:20px;background-color: darkslategrey;color: white;border-radius: 10px;margin-bottom: 10px;width: 400px;">
        <h2 style="margin-top: 0;">Новости</h2>
        <a style="color: greenyellow;" href="{{ route('new.create') }}">Добавить новость</a><br style="margin-bottom: 20px;">
        <a style="color: greenyellow" href="{{ route('admin.news.list') }}">Список новостей</a>
    </div>
    <div style="padding:20px;background-color: darkslateblue;color: white;border-radius: 10px;margin-bottom: 10px;width: 400px;">
        <h2 style="margin-top: 0;">Моды</h2>
        <a style="color: greenyellow;" href="{{ route('mod.create') }}">Добавить мод</a><br style="margin-bottom: 20px;">
        <a style="color: greenyellow" href="{{ route('admin.mods.list') }}">Список модов</a>
    </div>
@endsection
