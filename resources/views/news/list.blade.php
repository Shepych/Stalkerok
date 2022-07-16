@extends('layouts.main')

@section('content')
    <div class="d-flex justify-content-center align-items-center align-content-center flex-column mb-4">
        <h1 class="mt-4">Список новостей</h1>
        <div class="container d-flex justify-content-center align-content-center">
            @foreach($articles as $article)
                <a class="m-2" style="display: inline-block" href="{{ route('new.page', $article->url) }}">
                    <div class="card" style="height: 300px;width: 18rem;">
                        <img src="{{ $article->img }}" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">{{ $article->title }}</h5>
                            <div class="btn btn-primary">Читать</div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div>
            {{ $articles->links('pagination.classic') }}
        </div>
    </div>
@endsection

@section('js')
    <script src="/js/main/main.js"></script>
@endsection
