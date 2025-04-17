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

/*
 * FormRequestを使用するために元のログインコントローラを継承
*/
class CustomLoginController extends AuthenticatedSessionController
{
    /**
     * LoginRequestをカスタムしたFormRequestに置き換えるために再定義
     */
    public function customStore(LoginRequest $request)
    {
        return $this->customLoginPipeline($request)->then(function ($request) {
            return app(LoginResponse::class);
        });
    }

    /**
     * 上記に付随してパイプライン処理を拡張実装。LoginRequestをカスタムしたFormRequestに置き換え
     */
    protected function customLoginPipeline(LoginRequest $request)
    {
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
            AttemptToAuthenticate::class,
            PrepareAuthenticatedSession::class,
        ]));
    }
}