<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
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