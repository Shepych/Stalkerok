<?php

namespace App\Http\Controllers;

use App\Models\News;
use http\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class NewsController extends Controller
{
    # Страница «Список статей»
    public function newsList(Request $request) {
        return News::packageList($request);
    }

    # Страница «Статья»
    public function newPage(Request $request, $url) {
        return News::package($request, $url);
    }

    # Комментирование статьи
    public function newComment(Request $request, $id) {
        return News::send($request, $id);
    }
}
