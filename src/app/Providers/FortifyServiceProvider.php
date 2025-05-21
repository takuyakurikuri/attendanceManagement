<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Illuminate\Support\Facades\Auth;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse {
        public function toResponse($request)
        {
            return redirect('/attendance');
        }
    });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::loginView(function () {
            if(request()->is('admin/*')){
                return view('auth.admin_login');
            }
            return view('auth.login');
        });

        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::createUsersUsing(CreateNewUser::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        //カスタムログイン処理
        app()->singleton(LoginResponse::class,function() {
            return new class implements LoginResponse {
                public function toResponse($request)
                {
                    if(Auth::guard('admin')){
                        return redirect('/attendance/list');
                    }
                    return redirect('/attendance');
                }
            };
        });

        //カスタム登録処理
        app()->singleton(RegisterResponse::class,function() {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    return redirect()->route('verification.notice');
                }
            };
        });

        //カスタムログアウト処理
        app()->singleton(LogoutResponse::class, function () {
            return new class implements LogoutResponse {
                public function toResponse($request)
                {
                    return redirect('/login');
                }
            };
        });
    }
}