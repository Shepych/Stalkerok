@if($review)
    <div class="alert alert-{{ defineReviewColor($review->rating) }} mb-0" role="alert">
        Login: {{ $review->user->name }}<br>
        <hr>
        {!! $review->content !!}
        {{ $review->updated_at }}
    </div>
    <form class="ajax__form" action="{{ route('moderator.review', $review->id) }}" method="post">
        @csrf
        <div class="d-flex justify-content-center flex-row mt-4">
            <button onclick="return confirm('Точно удалить ?')" name="delete" value="true" style="margin-right: 25px" type="submit" class="btn btn-secondary btn-lg ">Удалить</button>
            <button name="next" value="true" type="submit" class="btn btn-primary btn-lg">Следующий</button>
        </div>
        <div class="mt-4">
            <textarea name="cause" placeholder="Причина удаления" class="form-control" rows="3"></textarea>
        </div>
    </form>
@else
    Новые отзывы отсутствуют
@endif
