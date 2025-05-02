<?php

namespace Database\Seeders;

use Database\Factories\AttendanceFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Attendance;
use Illuminate\Support\Carbon;
use App\Models\User;
use Carbon\CarbonPeriod;

class AttendancesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //$startDate = Carbon::create(2025, 4, 1);

        // for($i = 2; $i < 11; $i++){
        //     for ($j = 0; $j < 20; $j++) {
        //         $date = $startDate->copy()->addDays($j);
        //         $endDate = $startDate->copy()->addDays($j);

        //         // ランダムな時間（08:30〜09:00）
        //         $minutesToAdd = rand(0, 30);
        //         $datetime = $date->setTime(8, 30)->addMinutes($minutesToAdd);
        //         $datetimeOut = $endDate->setTime(17, 30)->addMinutes($minutesToAdd);

        //         Attendance::factory()->create([
        //             'user_id' => $i,
        //             'clock_in' => $datetime,
        //             'clock_out' => $datetimeOut,
        //         ]);
        //     }
        // }

        // $startDate = Carbon::create(2025, 2, 1);

        // for ($j = 0; $j < 20; $j++) {
        //     $date = $startDate->copy()->addDays($j);
        //     $endDate = $startDate->copy()->addDays($j);

        //     // ランダムな時間（08:30〜09:00）
        //     $minutesToAdd = rand(0, 30);
        //     $datetime = $date->setTime(8, 30)->addMinutes($minutesToAdd);
        //     $datetimeOut = $endDate->setTime(17, 30)->addMinutes($minutesToAdd);

        //     Attendance::factory()->create([
        //         'user_id' => 2,
        //         'clock_in' => $datetime,
        //         'clock_out' => $datetimeOut,
        //     ]);
        // }

        // $startDate = Carbon::create(2025, 3, 1);

        // for ($j = 0; $j < 20; $j++) {
        //     $date = $startDate->copy()->addDays($j);
        //     $endDate = $startDate->copy()->addDays($j);

        //     // ランダムな時間（08:30〜09:00）
        //     $minutesToAdd = rand(0, 30);
        //     $datetime = $date->setTime(8, 30)->addMinutes($minutesToAdd);
        //     $datetimeOut = $endDate->setTime(17, 30)->addMinutes($minutesToAdd);

        //     Attendance::factory()->create([
        //         'user_id' => 2,
        //         'clock_in' => $datetime,
        //         'clock_out' => $datetimeOut,
        //     ]);
        // }

        // $startDate = Carbon::create(2025, 4, 1);

        // for ($j = 0; $j < 20; $j++) {
        //     $date = $startDate->copy()->addDays($j);
        //     $endDate = $startDate->copy()->addDays($j);

        //     // ランダムな時間（08:30〜09:00）
        //     $minutesToAdd = rand(0, 30);
        //     $datetime = $date->setTime(8, 30)->addMinutes($minutesToAdd);
        //     $datetimeOut = $endDate->setTime(17, 30)->addMinutes($minutesToAdd);

        //     Attendance::factory()->create([
        //         'user_id' => 2,
        //         'clock_in' => $datetime,
        //         'clock_out' => $datetimeOut,
        //     ]);
        // }
        

        // $startDate = Carbon::create(2025, 5, 1);

        // for ($j = 0; $j < 20; $j++) {
        //     $date = $startDate->copy()->addDays($j);
        //     $endDate = $startDate->copy()->addDays($j);

        //     // ランダムな時間（08:30〜09:00）
        //     $minutesToAdd = rand(0, 30);
        //     $datetime = $date->setTime(8, 30)->addMinutes($minutesToAdd);
        //     $datetimeOut = $endDate->setTime(17, 30)->addMinutes($minutesToAdd);

        //     Attendance::factory()->create([
        //         'user_id' => 2,
        //         'clock_in' => $datetime,
        //         'clock_out' => $datetimeOut,
        //     ]);
        // }

        // $startDate = Carbon::create(2025, 6, 1);

        // for ($j = 0; $j < 20; $j++) {
        //     $date = $startDate->copy()->addDays($j);
        //     $endDate = $startDate->copy()->addDays($j);

        //     // ランダムな時間（08:30〜09:00）
        //     $minutesToAdd = rand(0, 30);
        //     $datetime = $date->setTime(8, 30)->addMinutes($minutesToAdd);
        //     $datetimeOut = $endDate->setTime(17, 30)->addMinutes($minutesToAdd);

        //     Attendance::factory()->create([
        //         'user_id' => 2,
        //         'clock_in' => $datetime,
        //         'clock_out' => $datetimeOut,
        //     ]);
        // }
        
        $users = User::where('id', '>', 1)->get(); // id=2〜10
        $period = CarbonPeriod::create('2025-03-01', '2025-05-01');

        foreach ($users as $user) {
            foreach ($period as $date) {
                if ($date->isWeekend()) continue; // 任意で土日除外

                $clockIn = $date->copy()->setTime(rand(8, 9), rand(0, 59));
                $clockOut = (clone $clockIn)->addHours(8)->addMinutes(rand(0, 30));

                Attendance::create([
                    'user_id' => $user->id,
                    'clock_in' => $clockIn,
                    'clock_out' => $clockOut,
                ]);
            }
        }
    }
}