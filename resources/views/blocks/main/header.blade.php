<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('index') }}">Stalkerok.ru</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Переключатель навигации">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarColor01">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a data-active="mods" class="nav-link {{ request()->is('mods/*') || request()->is('mods') ? 'active' : null }}" aria-current="page" href="{{ route('mods.list') }}">Моды</a>
                </li>
                <li class="nav-item">
                    <a data-active="news" class="nav-link {{ request()->is('news/*') || request()->is('news') ? 'active' : null }}" href="{{ route('news.list') }}">Новости</a>
                </li>
                <li class="nav-item">
                    <a data-active="forum" class="nav-link {{ request()->is('forum/*') || request()->is('forum') ? 'active' : null }}" href="{{ route('forum') }}">Форум</a>
                </li>
                <li class="nav-item">
                    <a data-active="store" class="nav-link" href="{{ route('store') }}">Магазин</a>
                </li>
                @if(Auth::user())
                    <li class="nav-item">
                        <a data-active="profile" class="nav-link {{ request()->is('pda/*') ? 'active' : null }}" href="{{ route('profile', Auth::id()) }}">Профиль</a>
                    </li>
                @else
                    <li class="nav-item">
                        <a data-active="auth" class="nav-link" href="{{ route('login') }}">Авторизация</a>
                    </li>
                @endif
            </ul>
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Поиск" aria-label="Поиск">
                <button class="btn btn-outline-light" type="submit">Поиск</button>
            </form>
        </div>
    </div>
</nav>
