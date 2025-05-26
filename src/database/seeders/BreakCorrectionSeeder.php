<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AttendanceCorrection;
use Illuminate\Support\Carbon;
use App\Models\BreakCorrection;

class BreakCorrectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $corrections = AttendanceCorrection::all();

        foreach ($corrections as $correction) {
            $attendance = $correction->attendance;

            if (!$attendance) continue;

            $breakTimes = $attendance->breakTimes;

            if ($breakTimes->isEmpty()) continue;

            $breakCount = $breakTimes->count();
            $remainingMinutes = 60;

            // 各BreakTimeに対して1件のBreakCorrectionを作る
            foreach ($breakTimes as $index => $breakTime) {
                $minutes = ($index === $breakCount - 1)
                    ? $remainingMinutes
                    : rand(10, min(30, $remainingMinutes - ($breakCount - $index - 1) * 10));

                $remainingMinutes -= $minutes;

                $breakDate = Carbon::parse($breakTime->break_start)->toDateString();
                $start = Carbon::parse($breakDate . ' ' . rand(12, 15) . ':' . rand(0, 59));
                $end = (clone $start)->addMinutes($minutes);

                BreakCorrection::create([
                    'attendance_correction_id' => $correction->id,
                    'break_start' => $start,
                    'break_end' => $end,
                ]);
            }
        }
    }
}