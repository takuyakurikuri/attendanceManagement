<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AttendanceCorrection;
use Illuminate\Support\Carbon;
use App\Models\Attendance;

class AttendanceCorrectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 既存の勤怠データを取得（必要なら条件を絞ってもOK）
        $attendances = Attendance::inRandomOrder()->take(20)->get();

        foreach ($attendances as $attendance) {
            // 日付を取得（clock_inから年月日を抜き出す）
            $date = Carbon::parse($attendance->clock_in)->toDateString();

            // 9:00 〜 18:00 に補正
            $correctedClockIn = Carbon::parse($date . ' 09:00:00');
            $correctedClockOut = Carbon::parse($date . ' 18:00:00');

            // 修正申請のダミーデータ作成
            AttendanceCorrection::create([
                'attendance_id' => $attendance->id,
                'user_id' => $attendance->user_id,
                'clock_in' => $correctedClockIn,
                'clock_out' => $correctedClockOut,
                'reason' => fake()->sentence(),
                'status' => random_int(1, 2),
                'admin_id' => 1, // 適当なadmin ID
            ]);
        }
    }
}