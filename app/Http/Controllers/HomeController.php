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

    # СПИСОК УВЕДОМЛЕНИЙ
    public function notifications() {
        # Когда переходим на эту страницу - производим все вычисления
        $alerts = Notification::list();

        return view('user.notifications', [
            'alerts' => $alerts,
        ]);
    }
}
