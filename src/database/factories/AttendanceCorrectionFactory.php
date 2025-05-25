<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttendanceCorrection>
 */
class AttendanceCorrectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'clock_in' => now(),
        'clock_out' => now(),
        'reason' => fake()->sentence(),
        'status' => random_int(1,2),
        'admin_id' => 1,
        'user_id' => random_int(2,10),
        'attendance_id' => 2,
        ];
    }
}