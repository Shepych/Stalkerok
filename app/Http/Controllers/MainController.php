<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class MainController extends Controller
{
    # ГЛАВНАЯ СТРАНИЦА
    public function index() {
        return view('index', [
            'activePage' => 'index',
        ]);
    }

    # СТРАНИЦА ПРОФИЛЯ
    public function profile($id) {
        $pda = User::where('id', $id)->first();

        if(!$pda) {
            abort(404);
        }

        $moderation = ModeratorController::generate();
        $notifications = Notification::count();

        return view('user.home', [
            'moderation' => $moderation,
            'notifications' => $notifications,
            'pda' => $pda,
            'activePage' => 'profile',
        ]);
    }
}
