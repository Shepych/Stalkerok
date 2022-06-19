@extends('layouts.main')

@section('content')
    <h1>Список модов</h1>
    @foreach($mods as $mod)
        <a href="{{ route('mod.page', $mod->url) }}" style="display:flex;flex-direction: column;justify-content: center;align-items: center; margin-bottom: 20px;width: 400px;background-color: #4a5568;border-radius: 10px;padding: 20px;">
            <h3 style="margin-top: 0;color: white">{{ $mod->title }}</h3>
            <img src="{{ $mod->img }}" width="200px">

            <div style="display: flex">
                @foreach($tags as $tag)
                    @if(array_search($tag->id, convertTagsFromString($mod->tags)) !== false)
                        <span style="display: block; padding: 10px;background-color: darkslategrey;color:white;border-radius: 5px">{{ $tag->title }}</span>
                    @endif
                @endforeach
            </div>
        </a>
    @endforeach

    <div>
        {{ $mods->links('pagination.classic') }}
    </div>
@endsection
