<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    # СПИСОК ТОПИКОВ
    public function forum(Request $request) {
        return Forum::generate($request);
    }

    # СТРАНИЦА ТОПИКА
    public function topic(Request $request, $url) {
        return Forum::topic($request, $url);
    }

    # СОЗДАНИЕ ТОПИКА
    public function createTopic(Request $request) {
        # Если POST метод
        if($request->isMethod('POST')) {
            $result = Forum::topicCreate($request);
            if(!$result) {
                return Ajax::valid(Forum::$error);
            }

            return Ajax::redirect('/forum/' . $result);
        }

        # Если GET метод
        return view('forum.topic_create', [
            'activePage' => 'forum',
        ]);
    }

    # ОТПРАВКА КОММЕНТАРИЯ
    public function sendComment(Request $request, $id) {
        return Forum::send($request, $id);
    }
}
