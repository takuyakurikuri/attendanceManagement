<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\BreakCorrection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\BreakTime;
use App\Http\Requests\AttendanceCorrectionRequest;

class CorrectionController extends Controller
{
    public function apply(AttendanceCorrectionRequest $request){
        $attendance = Attendance::find($request->attendance_id);
        $originalDate = $attendance->clock_in->format('Y-m-d');
        $newClockIn = Carbon::createFromFormat('Y-m-d H:i', $originalDate . ' ' . $request->clock_in);
        $newClockOut = Carbon::createFromFormat('Y-m-d H:i', $originalDate . ' ' . $request->clock_out);

        $attendanceCorrection = AttendanceCorrection::create([
            'clock_in' => $newClockIn,
            'clock_out' => $newClockOut,
            'reason' => $request->reason,
            'status' => 1,//申請中ステータスは'1'に設定
            'admin_id' => 1,//SUは'1'に設定
            'user_id' => Auth::id(),
            'attendance_id' => $request->attendance_id,
        ]);

        foreach($request->breakTime_id as $i => $id){
            $break = BreakTime::find($id);
            if(!$break) continue;

            $date = $break->break_start->format('Y-m-d');

            $newStart = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $request->break_start[$i]);
            $newEnd = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $request->break_end[$i]);

            BreakCorrection::create([
                'break_start' => $newStart,
                'break_end' => $newEnd,
                'attendance_correction_id' => $attendanceCorrection->id,
            ]);
        }

        return redirect('attendance/list')->with('message', '勤怠修正を申請しました');
    }

    public function changeApplicationList(){
        $user = Auth::user();
        $attendanceCorrections = AttendanceCorrection::where('user_id',$user->id)->get();
        return view('stamp_correction_request',compact('user','attendanceCorrections'));
    }
}