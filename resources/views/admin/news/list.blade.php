<h1>Список новостей</h1>
<a href="{{ route('admin.panel') }}">На главную</a>

@foreach($list as $new)
    <div style="margin-bottom: 20px;background-color: #4a5568;color: #1a202c">
        <h3>{{ $new->title }}</h3>
        <p>{{ $new->content }}</p>
        <img src="{{ $new->img }}" width="200px">
        <hr>
        <a href="{{ route('new.update', $new->id) }}">Изменить</a>
        <form action="{{ route('new.delete', $new->id) }}" method="post">
            @csrf
            <input type="submit" value="Удалить">
        </form>
    </div>
@endforeach
