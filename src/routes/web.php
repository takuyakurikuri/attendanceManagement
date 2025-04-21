<?php

use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomRegisterController;
use App\Http\Controllers\CustomLoginController;



Route::middleware('auth'/*,'verified'*/)->group(function(){
    //Route::get('/', function () { return view('index'); });
    Route::get('/attendance', [AttendanceController::class,'attendanceStatus']);
    Route::Post('/attendance/clockIn',[AttendanceController::class,'ClockIn']);
    Route::patch('/attendance/clockOut',[AttendanceController::class,'clockOut']);
    Route::Post('/attendance/breakStart',[AttendanceController::class,'breakStart']);
    Route::patch('/attendance/breakEnd',[AttendanceController::class,'breakEnd']);
    Route::get('/attendance/list', [AttendanceController::class,'attendanceList']);
});

/*
会員登録とログイン画面は
App\Providers\FortifyServiceProvider::boot() 内にルートとして定義
*/
Route::Post('/register/store',[CustomRegisterController::class,'customStore']);
Route::Post('/login/store',[CustomLoginController::class,'customStore']);