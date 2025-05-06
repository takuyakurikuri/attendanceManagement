<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\BreakTime;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class AttendanceController extends Controller
{

    public function attendanceStatus(){
        $user = Auth::user();
        $today = Carbon::today();
        $getAttendance = Attendance::where('user_id',$user->id)->whereDate('clock_in',$today);
        $attendance = $getAttendance->first();
        $clockOut = $getAttendance->whereNotNull('clock_out')->first();

        $isWorkingToday = $attendance !== null;
        $isBreaking = false;
        $isClockOut = false;

        if($attendance) {
            $isBreaking = breakTime::where('attendance_id',$attendance->id)->whereNull('break_end')->exists();
        }

        if($clockOut) {
            $isClockOut = true;
        }
        return view('attendance',compact('isWorkingToday','attendance','isBreaking','isClockOut'));
    }

    public function attendanceList(Request $request){
        $month = $request->input('month');
        $date = $month ? Carbon::createFromFormat('Y-m',$month) : Carbon::today();
        $week = ['日','月','火','水','木','金','土'];
        $attendances = Attendance::with('breakTimes')->where('user_id',Auth::id())->whereYear('clock_in',$date->format('Y'))->whereMonth('clock_in',$date->format('m'))->get();
        return view('attendance_list', compact('attendances','date','week'));
    }

    public function attendanceDetail($attendance_id){
        $attendance = Attendance::find($attendance_id);
        $breakTimes = BreakTime::where('attendance_id',$attendance_id)->get();
        // $user = Auth::user();
        $user = $attendance->user;
        $attendanceCorrection = AttendanceCorrection::where('attendance_id',$attendance->id)->first();
        return view('attendance_detail',compact('attendance','breakTimes','user','attendanceCorrection'));
    }

    public function clockIn(){
        $user = Auth::user();
        Attendance::create([
            'user_id' => $user->id,
            'clock_in' => now(),
        ]);
        
        return redirect('/attendance')->with('message','出勤しました');
    }

    public function clockOut(Request $request){
        $user = Auth::user();
        Attendance::find($request->attendance_id)->update([
            'clock_out' => now(),
        ]);
        
        return redirect('/attendance')->with('message','退勤しました');
    }

    public function breakStart(Request $request){
        BreakTime::create([
            'attendance_id' => $request->attendance_id,
            'break_start' => now(),
        ]);
        
        return redirect('/attendance')->with('message','休憩に入りました');
    }

    public function breakEnd(Request $request){
        BreakTime::where('attendance_id',$request->attendance_id)->whereNull('break_end')->update([
            'break_end' => now(),
        ]);
        
        return redirect('/attendance')->with('message','休憩から戻りました');
    }
}