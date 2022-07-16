@extends('layouts.main')

@section('content')
    <div class="container d-flex justify-content-center align-items-center flex-column">
        <h1 class="m-4">Список модов</h1>
        <div class="d-flex">
            @foreach($mods as $mod)
                <a class="m-2 bg-dark bg-gradient" href="{{ route('mod.page', $mod->url) }}" style="display:flex;flex-direction: column;justify-content: center;align-items: center; margin-bottom: 20px;width: 400px;background-color: #4a5568;border-radius: 10px;padding: 20px;text-decoration: none">
                    <h3 style="margin-top: 0;color: white">{{ $mod->title }}</h3>
                    <img class="img-fluid w-100" src="{{ $mod->img }}">

                    <div style="display: flex">
                        @foreach($tags as $tag)
                            @if(array_search($tag->id, convertTagsFromString($mod->tags)) !== false)
                                <button class="btn btn-primary mt-3" type="submit">{{ $tag->title }}</button>
                            @endif
                        @endforeach
                    </div>
                </a>
            @endforeach
        </div>

        <div>
            {{ $mods->links('pagination.classic') }}
        </div>
    </div>
@endsection
