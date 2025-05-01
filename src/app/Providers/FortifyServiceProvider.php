<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use App\Actions\Fortify\AdminLoginResponse;
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

        //認証ガードの切り替え
        // Fortify::authenticateUsing(function (Request $request) {
        //     $credentials = $request->only('email','password');

        //     if(request()->is('/admin/*')){
        //         if(Auth::guard('admin')->attempt($credentials)){
        //             return Auth::guard('admin')->user();
        //         }
        //     } else {
        //         if(Auth::guard('web')->attempt($credentials)){
        //             return Auth::guard('web')->user();
        //         }
        //     }
        //     return null;
        // });

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

        //管理者を考慮したカスタムログイン処理
        // app()->singleton(LoginResponse::class, function () {
        //     return new class implements LoginResponse {
        //         public function toResponse($request)
        //         {
        //             if (Auth::guard('admin')->check()) {
        //                 return redirect()->intended('/admin/dashboard');
        //             }

        //             return redirect()->intended('/attendance');
        //         }
        //     };
        // });

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