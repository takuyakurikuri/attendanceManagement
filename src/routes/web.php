<?php

use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CorrectionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomRegisterController;
use App\Http\Controllers\CustomLoginController;
use App\Http\Middleware\MultiGuardAuth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

Route::middleware(['auth','verified'])->group(function(){
    //Route::get('/', function () { return view('index'); });
    Route::get('/attendance', [AttendanceController::class,'attendanceStatus']);
    Route::Post('/attendance/clockIn',[AttendanceController::class,'ClockIn']);
    Route::patch('/attendance/clockOut',[AttendanceController::class,'clockOut']);
    Route::Post('/attendance/breakStart',[AttendanceController::class,'breakStart']);
    Route::patch('/attendance/breakEnd',[AttendanceController::class,'breakEnd']);
    Route::get('/attendance/list', [AttendanceController::class,'attendanceList'])->name('attendance.list');
    //Route::get('/attendance/{attendance_id}', [AttendanceController::class,'attendanceDetail']);
    // Route::get('/stamp_correction_request/list', [CorrectionController::class,'changeApplicationList']);
    // Route::post('/attendance/modify', [CorrectionController::class,'apply']);
});

//メール認証3点セット
Route::get('/email/verify', function () {
    return view('auth.verify_email');
})->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function ($id) {
    $user = User::find($id);

    if ($user->hasVerifiedEmail()) {
            return redirect('/login')->with('message', 'すでに認証済みです。');
        }

    //emailVerifyはログイン済みユーザーを想定しているので強制書き込み
    $user->forceFill([
            'email_verified_at' => Carbon::now(),
        ])->save();

    Auth::login($user);

    return redirect('/attendance')->with('message', 'メール認証が完了しました。');
    
})->middleware('signed')->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', '認証用のリンクを再送しました');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
//メール認証3点セットここまで

Route::get('/login', function () { return view('auth.login');})->name('login');
Route::get('/register', function () { return view('auth.register');});

Route::Post('/register/store',[CustomRegisterController::class,'customStore']);
Route::Post('/login/store',[CustomLoginController::class,'customStore']);

Route::get('/admin/login', function () { return view('auth.admin_login');})->name('admin.login');
Route::Post('/admin/login/store',[CustomLoginController::class,'customStore']);

Route::middleware('auth:admin')->group(function(){
    Route::get('/admin/attendance/list', [AdminAttendanceController::class,'memberList'])->name('member.list');
    Route::post('/admin/logout', [AdminAttendanceController::class,'adminLogout']);
    Route::get('/admin/staff/list',[AdminAttendanceController::class,'staffList']);
    // Route::get('/stamp_correction_request/list', [AdminAttendanceController::class,'adminChangeApplicationList']);
    Route::get('/admin/attendance/staff/{user_id}', [AdminAttendanceController::class,'staffAttendanceList'])->name('admin.attendance.list');
    //Route::get('/attendance/{attendance_id}', [AttendanceController::class,'attendanceDetail']);
    Route::get('/stamp_correction_request/approve/{attendance_correct_request}',[CorrectionController::class,'requestDetail'])->name('attendance.correct.request');
    Route::post('/stamp_correction_request/approve',[CorrectionController::class,'approveRequest'])->name('approve.request');
    Route::post('/admin/attendance/staff/csv',[AdminAttendanceController::class,'exportCsv']);
});

Route::middleware(MultiGuardAuth::class)->group(function(){
    Route::get('/attendance/{attendance_id}', [AttendanceController::class,'attendanceDetail']);
    Route::post('/attendance/modify', [CorrectionController::class,'apply']);
    Route::get('/stamp_correction_request/list', [CorrectionController::class,'changeApplicationList']);
});

// Route::prefix('admin')->name('admin.')->group(function () {
//     // 管理者ログインページ
//     Route::get('login', [AdminAttendanceController::class, 'adminLogin'])->name('login');

//     // ログアウト
//     Route::post('logout', [AdminAttendanceController::class, 'adminLogout'])->name('logout');

//     // 認証後にアクセスできるページ
//     Route::middleware('auth:admin')->group(function () {
//         Route::get('attendance/list', [AdminAttendanceController::class, 'memberList'])->name('attendance.list');
//     });
// });