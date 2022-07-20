@if(isset($comment))
    <div class="alert alert-dark mb-0" role="alert">
        Login: {{ $comment->user->name }}<br>
        <hr>
        {!! $comment->content !!}
        {{ $comment->updated_at }}
    </div>

    <form class="ajax__form" action="{{ route('moderator.comment', $comment->id) }}" method="post">
        @csrf
        <div class="d-flex justify-content-center flex-row mt-4">
            <button name="delete" value="true" style="margin-right: 25px" type="submit" class="btn btn-secondary btn-lg ">Удалить</button>
            <button name="next" value="true" type="submit" class="btn btn-primary btn-lg">Следующий</button>
        </div>
        <div class="mt-4">
            <textarea name="cause" placeholder="Причина удаления" class="form-control" rows="3"></textarea>
        </div>
    </form>
@else
    Новые комментарии отсутствуют
@endif
