<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomRegisterController;
use App\Http\Controllers\CustomLoginController;



Route::middleware('auth'/*,'verified'*/)->group(function(){
Route::get('/', function () { return view('index'); });
Route::get('/attendance', function () { return view('attendance'); });
});

/*
会員登録とログイン画面は
App\Providers\FortifyServiceProvider::boot() 内にルートとして定義
*/
Route::Post('/register/store',[CustomRegisterController::class,'customStore']);
Route::Post('/login/store',[CustomLoginController::class,'customStore']);