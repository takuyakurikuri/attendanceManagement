<?php

namespace Database\Seeders;

use Database\Factories\AttendanceFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Attendance;
use Illuminate\Support\Carbon;

class AttendancesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startDate = Carbon::create(2025, 4, 1);

        for($i = 2; $i < 11; $i++){
            for ($j = 0; $j < 20; $j++) {
                $date = $startDate->copy()->addDays($j);
                $endDate = $startDate->copy()->addDays($j);

                // ランダムな時間（08:30〜09:00）
                $minutesToAdd = rand(0, 30);
                $datetime = $date->setTime(8, 30)->addMinutes($minutesToAdd);
                $datetimeOut = $endDate->setTime(17, 30)->addMinutes($minutesToAdd);

                Attendance::factory()->create([
                    'user_id' => $i,
                    'clock_in' => $datetime,
                    'clock_out' => $datetimeOut,
                ]);
            }
        }

    }
}