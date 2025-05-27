<?php

namespace Database\Seeders;

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
        $attendances = Attendance::inRandomOrder()->take(20)->get();

        foreach ($attendances as $attendance) {
            $date = Carbon::parse($attendance->clock_in)->toDateString();

            $correctedClockIn = Carbon::parse($date . ' 09:00:00');
            $correctedClockOut = Carbon::parse($date . ' 18:00:00');

            AttendanceCorrection::create([
                'attendance_id' => $attendance->id,
                'user_id' => $attendance->user_id,
                'clock_in' => $correctedClockIn,
                'clock_out' => $correctedClockOut,
                'reason' => fake()->sentence(),
                'status' => random_int(1, 2),
                'admin_id' => 1,
            ]);
        }
    }
}