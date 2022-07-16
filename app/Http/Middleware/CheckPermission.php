<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    public function handle(Request $request, Closure $next)
    {
        # Если пользователь - гость
        if (Auth::guest()) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Необходима авторизация', 'message_type' => 'error']);
            }
            return abort(404);
        }

        # Если пользователь - заблокирован
        if(Auth::user()->hasRole('blocked')) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Аккаунт заблокирован', 'message_type' => 'error']);
            }

            return abort(404);
        }

        return $next($request);
    }
}
