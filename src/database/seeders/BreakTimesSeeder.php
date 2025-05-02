<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BreakTime;
use Illuminate\Support\Carbon;
use App\Models\Attendance;

class BreakTimesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $startDate = Carbon::create(2025, 4, 1);

        // $count = 1;
        // for ($i = 1; $i < 10; $i++) {
        //     for ($j = 0; $j < 20; $j++) {
        //         $date = $startDate->copy()->addDays($j);
        //         $breakDate = $startDate->copy()->addDays($j);

        //         // ランダムな時間（08:30〜09:00）
        //         $minutesToAdd = rand(0, 30);
        //         $datetime = $date->setTime(11, 30)->addMinutes($minutesToAdd);
        //         $datetimeOut = $breakDate->setTime(12, 30)->addMinutes($minutesToAdd);

        //         BreakTime::factory()->create([
        //             'attendance_id' => $count,
        //             'break_start' => $datetime,
        //             'break_end' => $datetimeOut,
        //         ]);
        //         $count++;
        //     }
        // }

        // $count = 1;
        
        // $startDate = Carbon::create(2025, 2, 1);
        // for ($j = 0; $j < 20; $j++) {
        //     $date = $startDate->copy()->addDays($j);
        //     $breakDate = $startDate->copy()->addDays($j);

        //     // ランダムな時間（08:30〜09:00）
        //     $minutesToAdd = rand(0, 30);
        //     $datetime = $date->setTime(11, 30)->addMinutes($minutesToAdd);
        //     $datetimeOut = $breakDate->setTime(12, 30)->addMinutes($minutesToAdd);

        //     BreakTime::factory()->create([
        //         'attendance_id' => $count,
        //         'break_start' => $datetime,
        //         'break_end' => $datetimeOut,
        //     ]);
        //     $count++;
        // }

        // $startDate = Carbon::create(2025, 3, 1);
        // for ($j = 0; $j < 20; $j++) {
        //     $date = $startDate->copy()->addDays($j);
        //     $breakDate = $startDate->copy()->addDays($j);

        //     // ランダムな時間（08:30〜09:00）
        //     $minutesToAdd = rand(0, 30);
        //     $datetime = $date->setTime(11, 30)->addMinutes($minutesToAdd);
        //     $datetimeOut = $breakDate->setTime(12, 30)->addMinutes($minutesToAdd);

        //     BreakTime::factory()->create([
        //         'attendance_id' => $count,
        //         'break_start' => $datetime,
        //         'break_end' => $datetimeOut,
        //     ]);
        //     $count++;
        // }

        // $startDate = Carbon::create(2025, 4, 1);
        // for ($j = 0; $j < 20; $j++) {
        //     $date = $startDate->copy()->addDays($j);
        //     $breakDate = $startDate->copy()->addDays($j);

        //     // ランダムな時間（08:30〜09:00）
        //     $minutesToAdd = rand(0, 30);
        //     $datetime = $date->setTime(11, 30)->addMinutes($minutesToAdd);
        //     $datetimeOut = $breakDate->setTime(12, 30)->addMinutes($minutesToAdd);

        //     BreakTime::factory()->create([
        //         'attendance_id' => $count,
        //         'break_start' => $datetime,
        //         'break_end' => $datetimeOut,
        //     ]);
        //     $count++;
        // }

        // $startDate = Carbon::create(2025, 5, 1);
        // for ($j = 0; $j < 20; $j++) {
        //     $date = $startDate->copy()->addDays($j);
        //     $breakDate = $startDate->copy()->addDays($j);

        //     // ランダムな時間（08:30〜09:00）
        //     $minutesToAdd = rand(0, 30);
        //     $datetime = $date->setTime(11, 30)->addMinutes($minutesToAdd);
        //     $datetimeOut = $breakDate->setTime(12, 30)->addMinutes($minutesToAdd);

        //     BreakTime::factory()->create([
        //         'attendance_id' => $count,
        //         'break_start' => $datetime,
        //         'break_end' => $datetimeOut,
        //     ]);
        //     $count++;
        // }

        // $startDate = Carbon::create(2025, 6, 1);
        // for ($j = 0; $j < 20; $j++) {
        //     $date = $startDate->copy()->addDays($j);
        //     $breakDate = $startDate->copy()->addDays($j);

        //     // ランダムな時間（08:30〜09:00）
        //     $minutesToAdd = rand(0, 30);
        //     $datetime = $date->setTime(11, 30)->addMinutes($minutesToAdd);
        //     $datetimeOut = $breakDate->setTime(12, 30)->addMinutes($minutesToAdd);

        //     BreakTime::factory()->create([
        //         'attendance_id' => $count,
        //         'break_start' => $datetime,
        //         'break_end' => $datetimeOut,
        //     ]);
        //     $count++;
        // }

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