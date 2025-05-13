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

    public function exportCsv(Request $request){

        $week = ['日','月','火','水','木','金','土'];
        $month = $request->input('date');
        $date = Carbon::createFromFormat('Y-m',$month);
        $attendances = Attendance::with('breakTimes')->where('user_id',$request->user_id)->whereYear('clock_in',$date->format('Y'))->whereMonth('clock_in',$date->format('m'))->get();

        $fileName = $request->user_name .'attendance_' . $request->date . '.csv';
        return response()->streamDownload(function () use ($attendances, $week) {
            $handle = fopen('php://output','w');
            fputcsv($handle,['日付','出勤','退勤','休憩','合計']);
            foreach($attendances as $attendance){
            
                $totalBreakMinutes = $attendance->breakTimes->sum(function ($break) {
                    return $break->break_end && $break->break_start ? $break->break_start->diffInMinutes($break->break_end) : 0;
                });
                $breakHours = floor($totalBreakMinutes / 60);
                $breakMinutes = str_pad($totalBreakMinutes % 60, 2, '0', STR_PAD_LEFT);
                
                $workDuration = 0;
                if ($attendance->clock_in && $attendance->clock_out) {
                    $workDuration = $attendance->clock_in->diffInMinutes($attendance->clock_out) - $totalBreakMinutes;
                    $workDuration = max(0, $workDuration);
                }
                $workHours = floor($workDuration / 60);
                $workMinutes = str_pad($workDuration % 60, 2, '0', STR_PAD_LEFT);
                
                fputcsv($handle, [
                    $attendance->clock_in->format('m/d') . "(" .$week[$attendance->clock_in->format('w')] . ")",
                    $attendance->clock_in->format('H:i'),
                    $attendance->clock_out->format('H:i'),
                    $breakHours . ":" . $breakMinutes,
                    $workHours . ":" . $workMinutes,
                ]);
            }
            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }
}