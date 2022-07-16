<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Moderator
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
        if (!Auth::user()->hasRole('moderator')) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Недостаточно прав']);
            }
            return abort(403);
        }

        return $next($request);
    }
}
