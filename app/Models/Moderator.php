<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Moderator extends Model
{
    use HasFactory;

    # Вывод комментария при первой инициализации модерки
    public static function generateComment() {
        # Берём КОММЕНТАРИЙ уже модерируемый данным пользователем
        $comment = Comments::where('moderation', Auth::id())->orderByDesc('updated_at')->first();

        # Если такого нет - то берём свободный и присваимваем ему ID `moderation`
        if(!$comment) {
            return self::noModerateComment();
        }

        return $comment;
    }

    public static function generateReview() {
        # Берём ОТЗЫВ уже модерируемый данным пользователем
        $review = Reviews::where('moderation', Auth::id())->orderByDesc('updated_at')->first();
        # Если такого нет - то берём свободный и присваимваем ему ID `moderation`
        if(!$review) {
            return self::noModerateReview();
        }

        return $review;
    }

    # Берём немодерируемый комментарий
    public static function noModerateComment() {
        $comment = Comments::where('moderation', null)
            ->where('moderator_id', NULL)
            ->where('user_id', '!=', 1)
            ->where('topic_content', FALSE)
            ->orderByDesc('updated_at')
            ->first();
        if($comment) {
            $comment->moderation = Auth::id();
            $comment->save();
        }

        return $comment;
    }

    # Пропуск комментария
    public static function commentNext($id) {
        # Обновить поля у комментария
        $comment = Comments::where('id', $id)->first();
        $comment->moderator_id = Auth::id();
        $comment->moderation = null;
        $comment->save();

        return Moderator::noModerateComment();
    }

    # Берём немодерируемый отзыв
    public static function noModerateReview() {
        $review = Reviews::where('moderation', null)
            ->where('moderator_id', NULL)
            ->where('user_id', '!=', 1)
            ->orderByDesc('updated_at')
            ->first();
        if($review) {
            $review->moderation = Auth::id();
            $review->save();
        }

        return $review;
    }

    # Пропуск отзыва
    public static function reviewNext($id) {
        # Обновить поля у отзыва
        $review = Reviews::where('id', $id)->first();
        $review->moderator_id = Auth::id();
        $review->moderation = null;
        $review->save();

        return Moderator::noModerateReview();
    }

    # Обработка топика
    public static function generateTopic() {
        # Берём КОММЕНТАРИЙ уже модерируемый данным пользователем
        $topic = Forum::where('moderation', Auth::id())->orderByDesc('updated_at')->first();

        # Если такого нет - то берём свободный и присваимваем ему ID `moderation`
        if(!$topic) {
            return self::noModerateTopic();
        }

        return $topic;
    }

    # Берём немодерируемый топик
    public static function noModerateTopic() {
        $topic = Forum::where('moderation', null)
            ->where('moderator_id', NULL)
            ->where('user_id', '!=', 1)
            ->where('type', NULL)
            ->orderByDesc('updated_at')
            ->first();
        if($topic) {
            $topic->moderation = Auth::id();
            $topic->save();
        }

        return $topic;
    }

    # Пропуск топика
    public static function topicNext($id) {
        # Обновить поля у комментария
        $comment = Forum::where('id', $id)->first();
        $comment->moderator_id = Auth::id();
        $comment->moderation = null;
        $comment->save();

        return Moderator::noModerateTopic();
    }
}
