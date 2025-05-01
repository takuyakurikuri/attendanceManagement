<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    // public function handle($request, Closure $next, ...$guards)
    // {
    //     foreach ($guards as $guard) {
    //         if (Auth::guard($guard)->check()) {
    //             return $next($request);
    //         }
    //     }

    //     //カスタム箇所認証失敗時のリダイレクト先をここで制御
    //     if ($request->is('admin/*')) {
    //         return redirect()->route('admin.login');
    //     }
    //     return redirect()->route('login');
    // }

    //カスタムリダイレクト処理
    protected function redirectTo($request)
    {
        if(static::$redirectToCallback){
            if ($request->is('admin/*')) {
                return route('admin.login');
            }
            return route('login');
        }
    }
}