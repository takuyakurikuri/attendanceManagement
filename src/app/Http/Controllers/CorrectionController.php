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
use App\Models\User;

class CorrectionController extends Controller
{
    public function apply(AttendanceCorrectionRequest $request){

        $attendance = Attendance::find($request->attendance_id);
        $originalDate = $attendance->clock_in->format('Y-m-d');
        $newClockIn = Carbon::createFromFormat('Y-m-d H:i', $originalDate . ' ' . $request->clock_in);
        $newClockOut = Carbon::createFromFormat('Y-m-d H:i', $originalDate . ' ' . $request->clock_out);
        $admin = Auth::guard('admin')->user();

        //管理者はその場で修正できる
        if($admin){

            //ビュー側でpatchメソッドとして送信してコントローラー処理を分けるのもアリかも。コントローラー側で完結させる場合はこれでOK？
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

            return redirect()->route('admin.attendance.list',['user_id' => $attendance->user_id, 'month' => $attendance->clock_in->format('Y-m')]);
            // return redirect('admin/staff/list')->with('message', '管理者権限で勤怠を修正を申請しました');

        //一般ユーザーは申請をする
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

    public function changeApplicationList(){

        $admin = Auth::guard('admin')->user();
        if($admin){
            $attendanceCorrections = AttendanceCorrection::where('user_id','!=',1)->get();
        }else {
            $user = Auth::user();
            $attendanceCorrections = AttendanceCorrection::where('user_id',$user->id)->get();
        }

        return view('stamp_correction_request',compact('attendanceCorrections'));
        // $user = Auth::user();
        // $attendanceCorrections = AttendanceCorrection::where('user_id',$user->id)->get();
        // return view('stamp_correction_request',compact('user','attendanceCorrections'));
    }

    public function requestDetail(){
        return view('request_detail');
    }
}