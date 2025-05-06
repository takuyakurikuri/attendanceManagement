<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\User;

class AdminAttendanceController extends Controller
{
    public function memberList(Request $request){
        $day = $request->input('day');
        $date = $day ? Carbon::createFromFormat('Y-m-d',$day) : Carbon::today();
        // $users = User::where('id',"!=", 1)->get();
        // $attendances = Attendance::with('breakTimes')->whereDate('clock_in',$date->format('Y-m-d'))->get();
        $users = User::where('id', '!=', 1)
        ->with(['attendances' => function ($query) use ($date) {
            $query->with('breakTimes')
                  ->whereDate('clock_in', $date->format('Y-m-d'));
        }])
        ->get();
        return view('member_list',compact('date','users'));
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

    public function staffList() {
        $users = User::where('id', '!=', 1)->get();
        return view('staff_list',compact('users'));
    }
    
    public function adminChangeApplicationList(){
        return view('admin_change_application_list');
    }

    public function staffAttendanceList(Request $request, $user_id){
        $user = User::find($user_id);
        $month = $request->input('month');
        $date = $month ? Carbon::createFromFormat('Y-m',$month) : Carbon::today();
        $week = ['日','月','火','水','木','金','土'];
        $attendances = Attendance::with('breakTimes')->where('user_id',$user->id)->whereYear('clock_in',$date->format('Y'))->whereMonth('clock_in',$date->format('m'))->get();
        return view('staff_attendance_list', compact('attendances','date','week','user'));
    }
}