<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Requests\LoginRequest;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\CanonicalizeUsername;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Illuminate\Routing\Pipeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/*
 * FormRequestを使用するために元のログインコントローラを継承
*/
class CustomLoginController extends AuthenticatedSessionController
{
    /**
     * LoginRequestをカスタムしたFormRequestに置き換えるために再定義
     */
    // public function customStore(LoginRequest $request)
    // {
    //     return $this->customLoginPipeline($request)->then(function ($request) {
    //         return app(LoginResponse::class);
    //     });
    // }

    public function customStore(LoginRequest $request)
    {

        return $this->customLoginPipeline($request)->then(function ($request) {
            $credentials = $request->only('email','password');
            $guard = $request->is('admin/*') ? 'admin': 'web';
            $role = $guard==='admin' ? 1 : 2;

            if(Auth::guard($guard)->attempt(array_merge($credentials,['role' => $role]))){

                //ここにメール認証入れればいけるかな？
                // $user = auth()->user();

                // if (!$user->hasVerifiedEmail()) {
                //     auth()->logout();
                //     return redirect()->route('verification.notice')
                //         ->with('error', 'メール認証を完了してください。');
                // }
                $request->session()->regenerate();
                return redirect()->intended($guard === 'admin' ? 'admin/attendance/list' : '/attendance');
            }
            
            return back()->withErrors([
                'email' => 'ログイン情報が正しくありません。',
            ]);
        });
    }

    /**
     * 上記に付随してパイプライン処理を拡張実装。LoginRequestをカスタムしたFormRequestに置き換え
     */
    protected function customLoginPipeline(LoginRequest $request)
    {

        $isAdmin = request()->is('admin/*');

        if (Fortify::$authenticateThroughCallback) {
            return (new Pipeline(app()))->send($request)->through(array_filter(
                call_user_func(Fortify::$authenticateThroughCallback, $request)
            ));
        }

        if (is_array(config('fortify.pipelines.login'))) {
            return (new Pipeline(app()))->send($request)->through(array_filter(
                config('fortify.pipelines.login')
            ));
        }

        return (new Pipeline(app()))->send($request)->through(array_filter([
            config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
            config('fortify.lowercase_usernames') ? CanonicalizeUsername::class : null,
            Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,
            //AttemptToAuthenticate::class,
            PrepareAuthenticatedSession::class,
        ]));
    }
}