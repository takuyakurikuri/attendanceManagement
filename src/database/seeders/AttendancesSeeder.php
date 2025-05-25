<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;

class AttendancesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('id', '>', 1)->get(); // id=2〜10
        $yesterday = Carbon::today()->subDays(1);
        $period = CarbonPeriod::create('2025-03-01', $yesterday);

        foreach ($users as $user) {
            foreach ($period as $date) {
                if ($date->isWeekend()) continue; 

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