<?php

namespace App\Http\Controllers;

use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Str;
use App\Http\Requests\RegisterRequest;

/*
 * FormRequestを使用するために継承
*/
class CustomRegisterController extends RegisteredUserController
{

    public function customStore(RegisterRequest $request,
                          CreatesNewUsers $creator): RegisterResponse
    {
        if (config('fortify.lowercase_usernames') && $request->has(Fortify::username())) {
            $request->merge([
                Fortify::username() => Str::lower($request->{Fortify::username()}),
            ]);
        }

        $user = $creator->create($request->all());

        $user->sendEmailVerificationNotification();

        $this->guard->login($user, $request->boolean('remember'));

        return app(RegisterResponse::class);
    }
}