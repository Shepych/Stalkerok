<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

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

    # ГЛАВНАЯ СТРАНИЦА
    public function index() {
        return view('index', [
            'activePage' => 'index',
        ]);
    }

    # СПИСОК УВЕДОМЛЕНИЙ
    public function notifications() {
        # Когда переходим на эту страницу - производим все вычисления
        $alerts = Notification::list();

        return view('user.notifications', [
            'alerts' => $alerts,
        ]);
    }
}
