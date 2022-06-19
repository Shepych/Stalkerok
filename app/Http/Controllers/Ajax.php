<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\This;

class Ajax extends Controller
{
    public static function message($message) {
        return json_encode(['message' => $message], JSON_UNESCAPED_UNICODE);
    }

    public static function dd($obj) {
        return json_encode(['dd' => $obj], JSON_UNESCAPED_UNICODE);
    }

    public static function redirect($url) {
        return json_encode(['url' => $url], JSON_UNESCAPED_UNICODE);
    }

    public static function valid($validate) {
        $validateMessage = '<ul style="margin-bottom: 0;padding:0">';
        foreach ($validate->all() as $message) {
            $validateMessage.= '<li>' . $message. '</li>';
        }
        $validateMessage.= '</ul>';
        return json_encode(['message' => $validateMessage], JSON_UNESCAPED_UNICODE);
    }
}
