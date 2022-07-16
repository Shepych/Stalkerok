<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\This;

class Ajax extends Controller
{
    public static function message($message, $success = false) {
        return response()->json(['message' => $message, 'message_type' => self::messageType($success)]);
    }

    public static function dd($obj) {
        return response()->json(['dd' => $obj]);
    }

    public static function redirect($url) {
        return response()->json(['url' => $url]);
    }

    public static function valid($validate) {
        $validateMessage = '<ul style="margin-bottom: 0;padding:0">';

        if(is_array($validate)) {
            foreach ($validate as $message) {
                $validateMessage.= '<li>' . $message. '</li>';
            }
        } else {
            $validateMessage.= $validate;
        }

        $validateMessage.= '</ul>';
        return response()->json(['message' => $validateMessage, 'message_type' => self::messageType()], JSON_UNESCAPED_UNICODE);
    }

    public static function messageType($success = false) {
        switch ($success) {
            case true:
                $messageType = 'success';
                break;
            case false:
                $messageType = 'error';
                break;
            default:
                $messageType = 'error';
        }

        return $messageType;
    }
}
