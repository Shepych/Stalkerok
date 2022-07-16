@extends('layouts.main')

@section('content')
<div class="container mt-4 mb-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if(Auth::id() == $pda->id)
                    <div class="d-flex">
                        <a href="{{ route('notifications') }}" class="btn btn-primary position-relative">
                            Входящие
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $notifications }}
                                <span class="visually-hidden">непрочитанные сообщения</span>
                            </span>
                        </a>

                        <a style="margin-left: auto" class="btn btn-primary position-relative">
                            Настройки
                        </a>
                    </div>
                    @endif

                    <h1 class="text-center">{{ $pda->name }}</h1>
                    @if($pda->hasRole('blocked'))
                    <div class="text-center" style="color: red"><b>ЗАБЛОКИРОВАН</b></div>
                    @endif

                    @if(Auth::id() == $pda->id)
                        <div class="d-flex">
                            @role('admin')
                                <a class="btn btn-success" target="_blank" href="/admin/panel">Админка</a>
                            @endrole
                            <a style="margin-left: auto; width:100px" class="btn btn-secondary d-flex" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Выход</a>
                            <form class="d-none" id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if(Auth::id() == $pda->id)
            @role('moderator')
            <div class="col-md-8 mt-4">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#comment" type="button" role="tab" aria-controls="comment" aria-selected="true">Комментарии</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#topic" type="button" role="tab" aria-controls="review" aria-selected="false">Топики</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#review" type="button" role="tab" aria-controls="review" aria-selected="false">Отзывы</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="false">Пользователи</button>
                    </li>
                </ul>

                <div style="border: 1px solid #dee2e6;border-top: 0" class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active  p-4" id="comment" role="tabpanel" aria-labelledby="home-tab">
                        @if($moderation['comment'])
                            <div class="alert alert-dark mb-0" role="alert">
                                Login: {{ $moderation['comment']->user->name }}<br>
                                <hr>
                                {!! $moderation['comment']->content !!}
                                {{ $moderation['comment']->updated_at }}
                            </div>
                        @else
                            Новые комментарии отсутствуют
                        @endif

                    </div>
                    <div class="tab-pane fade p-4" id="topic" role="tabpanel" aria-labelledby="profile-tab">
                        @if(isset($moderation['topic']))
                            <div class="alert alert-{{ defineReviewColor($moderation['review']->rating) }} mb-0" role="alert">
                                Login: {{ $moderation['review']->user->name }}<br>
                                <hr>
                                {!! $moderation['review']->content !!}
                                {{ $moderation['review']->updated_at }}
                            </div>
                            <form class="ajax__form" action="{{ route('moderator.review') }}" method="post">
                                @csrf
                                <div class="d-flex justify-content-center flex-row mt-4">
                                    <button name="delete" value="true" style="margin-right: 25px" type="submit" class="btn btn-secondary btn-lg ">Удалить</button>
                                    <button name="next" value="true" type="submit" class="btn btn-primary btn-lg">Следующий</button>
                                </div>
                                <div class="mt-4">
                                    <textarea name="cause" placeholder="Причина удаления" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                                </div>
                            </form>
                        @else
                            Новые темы отсутствуют
                        @endif
                    </div>
                    <div class="tab-pane fade p-4" id="review" role="tabpanel" aria-labelledby="profile-tab">
                        @if($moderation['review'])
                            <div class="alert alert-{{ defineReviewColor($moderation['review']->rating) }} mb-0" role="alert">
                                Login: {{ $moderation['review']->user->name }}<br>
                                <hr>
                                {!! $moderation['review']->content !!}
                                {{ $moderation['review']->updated_at }}
                            </div>
                            <form class="ajax__form" action="{{ route('moderator.review') }}" method="post">
                                @csrf
                                <div class="d-flex justify-content-center flex-row mt-4">
                                    <button name="delete" value="true" style="margin-right: 25px" type="submit" class="btn btn-secondary btn-lg ">Удалить</button>
                                    <button name="next" value="true" type="submit" class="btn btn-primary btn-lg">Следующий</button>
                                </div>
                                <div class="mt-4">
                                    <textarea name="cause" placeholder="Причина удаления" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                                </div>
                            </form>
                        @else
                            Новые отзывы отсутствуют
                        @endif
                    </div>
                    <div class="tab-pane fade p-4" id="users" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">@</span>
                            <input type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
                        </div>

                        <div class="d-flex">
                            <img src="/storage/avatars/avatar.jpg" class="img-thumbnail w-25" alt="...">

                            <div class="card w-100" aria-hidden="true" style="margin-left: 10px;margin-right: 10px">
                                <div class="card-body">
                                    <h5 class="card-title placeholder-glow">
                                        <span class="placeholder col-6"></span>
                                    </h5>
                                    <p class="card-text placeholder-glow">
                                        <span class="placeholder col-7"></span>
                                        <span class="placeholder col-4"></span>
                                        <span class="placeholder col-4"></span>
                                        <span class="placeholder col-6"></span>
                                        <span class="placeholder col-8"></span>
                                    </p>
                                    <a href="#" tabindex="-1" class="btn btn-primary disabled placeholder col-6"></a>
                                </div>
                            </div>

                            <div>
                                <form class="ajax__form" action="{{ route('moderator.block', 2) }}" method="post">
                                    @csrf
                                    <input type="submit" class="btn btn-danger w-100 mb-2 h-25" value="Заблокировать">
                                    <textarea class="form-control mb-2" name="cause" placeholder="Причина" cols="30" style="height: 115px"></textarea>
                                </form>

                                <form class="ajax__form" action="{{ route('moderator.unblock', 2) }}" method="post">
                                    @csrf
                                    <input type="submit" class="btn btn-success w-100 h-25" value="Разблокировать">
                                </form>
                            </div>
                        </div>



                    </div>
                </div>
            </div>
            @endrole
        @endif

        <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
            <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
            </symbol>
            <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
            </symbol>
            <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
            </symbol>
        </svg>
    </div>
</div>
@endsection

@section('js')
    <script src="/js/main/main.js"></script>
@endsection
