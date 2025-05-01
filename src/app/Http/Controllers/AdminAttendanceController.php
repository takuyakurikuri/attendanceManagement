<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAttendanceController extends Controller
{
    public function memberList(){
        return view('member_list');
    }

    public function adminLogin(){
        return view('auth.admin_login');
    }

    public function adminLogout(Request $request)
    {
        // 管理者ガードでログアウト
        Auth::guard('admin')->logout();

        // セッションの再生成
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ログインページへリダイレクト
        return redirect('/admin/login');
    }
}