<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BreakTime;
use App\Models\Attendance;

class BreakTimesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attendances = Attendance::all();

        foreach ($attendances as $attendance) {
            $clockIn = $attendance->clock_in;
            $clockOut = $attendance->clock_out;

            if (!$clockOut) continue;

            $totalBreak = rand(30, 60); // 合計30〜60分
            $breakCount = rand(1, 3);
            $remaining = $totalBreak;

            for ($i = 0; $i < $breakCount; $i++) {
                $minutes = $i === $breakCount - 1 ? $remaining : rand(10, $remaining - ($breakCount - $i - 1) * 10);
                $remaining -= $minutes;

                $breakStart = $clockIn->copy()->addMinutes(rand(60, 240));
                $breakEnd = (clone $breakStart)->addMinutes($minutes);

                // 勤務時間内に収まるように調整
                if ($breakEnd->gt($clockOut)) {
                    $breakEnd = $clockOut->copy();
                }

                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start' => $breakStart,
                    'break_end' => $breakEnd,
                ]);
            }
        }
    }
}