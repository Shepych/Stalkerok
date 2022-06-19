<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function newsList(Request $request) {
        $articles = News::where('published', true)->orderByDesc('id')->paginate(2);

        if(isset($request->page)) {
            if($request->page > $articles->lastPage() || $request->page <= 0) {
                abort(404);
            }
        }

        # Редирект с '/news?page=1 на '/news'
        if($request->page ==  1) {
            return redirect('/news');
        }

        return view('news.list', [
            'articles' => $articles,
        ]);
    }

    public function newPage($url) {
        $article = News::where('url', $url)->first();
        if(!$article) {
            abort(404);
        }
        return view('news.page', [
            'article' => $article,
        ]);
    }
}
