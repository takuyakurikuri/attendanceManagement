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
        $admin = Auth::guard('admin')->user();

        //管理者はその場で修正
        if($admin){

            $attendance->update([
                'clock_in' => $newClockIn,
                'clock_out' => $newClockOut,
            ]);

            foreach($request->breakTime_id as $i => $id){
                $break = BreakTime::find($id);
                if(!$break) continue;

                $date = $break->break_start->format('Y-m-d');

                $newStart = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $request->break_start[$i]);
                $newEnd = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $request->break_end[$i]);

                $break->update([
                    'break_start' => $newStart,
                    'break_end' => $newEnd,
                ]);
            }

            return redirect()->route('admin.attendance.list',['user_id' => $attendance->user_id, 'month' => $attendance->clock_in->format('Y-m')])->with('message', '管理者権限で勤怠を修正を申請しました');


        //一般ユーザーは申請する
        }else{

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

    }

    public function changeApplicationList(Request $request){

        if(Auth::guard('admin')->check()){
            if($request->tab == 'approved'){
                $attendanceCorrections = AttendanceCorrection::where('user_id','!=',1)->where('status',2)->get();
            }else{
                $attendanceCorrections = AttendanceCorrection::where('user_id','!=',1)->where('status',1)->get();
            }
        }else {
            if($request->tab == 'approved'){
                $attendanceCorrections = AttendanceCorrection::where('user_id',Auth::user()->id)->where('status',2)->get();
            }else{
                $attendanceCorrections = AttendanceCorrection::where('user_id',Auth::user()->id)->where('status',1)->get();
            }
        }

        return view('stamp_correction_request',compact('attendanceCorrections'));
    }

    public function requestDetail($attendance_correct_request){
        $attendanceCorrection = AttendanceCorrection::find($attendance_correct_request);
        return view('request_detail',compact('attendanceCorrection'));
    }

    public function approveRequest(Request $request){
        $attendanceCorrection = AttendanceCorrection::find($request->attendanceCorrection_id);
        $attendance = Attendance::find($attendanceCorrection->attendance_id);

        $breakTimes = BreakTime::where('attendance_id',$attendance->id)->get();
        foreach($breakTimes as $i => $breakTime){
            breakTime::find($breakTime->id)->update([
                'break_start' => $attendanceCorrection->breakCorrections[$i]->break_start,
                'break_end' => $attendanceCorrection->breakCorrections[$i]->break_end,
            ]);
        }

        $attendance->update([
            'clock_in' => $attendanceCorrection->clock_in,
            'clock_out' => $attendanceCorrection->clock_out,
        ]);

        $attendanceCorrection->update(['status' => 2,]);
        
        return redirect()->route('attendance.correct.request',['attendance_correct_request' => $attendanceCorrection->id]);
    }
}