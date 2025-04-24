<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BreakTime;
use Illuminate\Support\Carbon;

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

        $count = 1;
        
        $startDate = Carbon::create(2025, 2, 1);
        for ($j = 0; $j < 20; $j++) {
            $date = $startDate->copy()->addDays($j);
            $breakDate = $startDate->copy()->addDays($j);

            // ランダムな時間（08:30〜09:00）
            $minutesToAdd = rand(0, 30);
            $datetime = $date->setTime(11, 30)->addMinutes($minutesToAdd);
            $datetimeOut = $breakDate->setTime(12, 30)->addMinutes($minutesToAdd);

            BreakTime::factory()->create([
                'attendance_id' => $count,
                'break_start' => $datetime,
                'break_end' => $datetimeOut,
            ]);
            $count++;
        }

        $startDate = Carbon::create(2025, 3, 1);
        for ($j = 0; $j < 20; $j++) {
            $date = $startDate->copy()->addDays($j);
            $breakDate = $startDate->copy()->addDays($j);

            // ランダムな時間（08:30〜09:00）
            $minutesToAdd = rand(0, 30);
            $datetime = $date->setTime(11, 30)->addMinutes($minutesToAdd);
            $datetimeOut = $breakDate->setTime(12, 30)->addMinutes($minutesToAdd);

            BreakTime::factory()->create([
                'attendance_id' => $count,
                'break_start' => $datetime,
                'break_end' => $datetimeOut,
            ]);
            $count++;
        }

        $startDate = Carbon::create(2025, 4, 1);
        for ($j = 0; $j < 20; $j++) {
            $date = $startDate->copy()->addDays($j);
            $breakDate = $startDate->copy()->addDays($j);

            // ランダムな時間（08:30〜09:00）
            $minutesToAdd = rand(0, 30);
            $datetime = $date->setTime(11, 30)->addMinutes($minutesToAdd);
            $datetimeOut = $breakDate->setTime(12, 30)->addMinutes($minutesToAdd);

            BreakTime::factory()->create([
                'attendance_id' => $count,
                'break_start' => $datetime,
                'break_end' => $datetimeOut,
            ]);
            $count++;
        }

        $startDate = Carbon::create(2025, 5, 1);
        for ($j = 0; $j < 20; $j++) {
            $date = $startDate->copy()->addDays($j);
            $breakDate = $startDate->copy()->addDays($j);

            // ランダムな時間（08:30〜09:00）
            $minutesToAdd = rand(0, 30);
            $datetime = $date->setTime(11, 30)->addMinutes($minutesToAdd);
            $datetimeOut = $breakDate->setTime(12, 30)->addMinutes($minutesToAdd);

            BreakTime::factory()->create([
                'attendance_id' => $count,
                'break_start' => $datetime,
                'break_end' => $datetimeOut,
            ]);
            $count++;
        }

        $startDate = Carbon::create(2025, 6, 1);
        for ($j = 0; $j < 20; $j++) {
            $date = $startDate->copy()->addDays($j);
            $breakDate = $startDate->copy()->addDays($j);

            // ランダムな時間（08:30〜09:00）
            $minutesToAdd = rand(0, 30);
            $datetime = $date->setTime(11, 30)->addMinutes($minutesToAdd);
            $datetimeOut = $breakDate->setTime(12, 30)->addMinutes($minutesToAdd);

            BreakTime::factory()->create([
                'attendance_id' => $count,
                'break_start' => $datetime,
                'break_end' => $datetimeOut,
            ]);
            $count++;
        }
    }
}