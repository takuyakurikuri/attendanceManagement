<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class MultiGuardAuth
{

    protected $guards = ['web','admin'];
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if ($request->routeIs('login') || $request->routeIs('admin.login')) {
        return $next($request);
    }
        if (Auth::check()) {
            return $next($request);
        }

        if (Auth::guard('admin')->check()) {
            return $next($request);
        }

        // 認証されていない場合
        return redirect('/login'); //一般ユーザーへのリダイレクトを想定する
    }
}