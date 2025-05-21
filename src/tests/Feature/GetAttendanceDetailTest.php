<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\BreakTime;
use App\Models\User;

class GetAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_get_name(): void
    {
        $user = User::factory()->create([
            'name' => 'tester',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
        ]);

        $attendance1 = Attendance::factory()->create([
            'clock_in' => Carbon::now(),
            'clock_out'=> Carbon::now()->addHours(9),
            'user_id' => $user->id,
        ]);

        $break1 = BreakTime::factory()->create([
            'break_start' => Carbon::now()->addHours(1),
            'break_end' => Carbon::now()->addHours(2),
            'attendance_id' => $attendance1->id,
        ]);

        $userData = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/login/store", $userData);

        $response = $this->get("/attendance/$attendance1->id");

        $response->assertSee($user->name);
    }

    public function test_get_date(): void
    {
        $user = User::factory()->create([
            'name' => 'tester',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
        ]);

        $attendance1 = Attendance::factory()->create([
            'clock_in' => Carbon::now(),
            'clock_out'=> Carbon::now()->addHours(9),
            'user_id' => $user->id,
        ]);

        $break1 = BreakTime::factory()->create([
            'break_start' => Carbon::now()->addHours(1),
            'break_end' => Carbon::now()->addHours(2),
            'attendance_id' => $attendance1->id,
        ]);

        $userData = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/login/store", $userData);

        $response = $this->get("/attendance/$attendance1->id");

        $response->assertSee($attendance1->clock_in->format('n月j日'));
    }

    public function test_get_clock(): void
    {
        $user = User::factory()->create([
            'name' => 'tester',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
        ]);

        $attendance1 = Attendance::factory()->create([
            'clock_in' => Carbon::now(),
            'clock_out'=> Carbon::now()->addHours(9),
            'user_id' => $user->id,
        ]);

        $break1 = BreakTime::factory()->create([
            'break_start' => Carbon::now()->addHours(1),
            'break_end' => Carbon::now()->addHours(2),
            'attendance_id' => $attendance1->id,
        ]);

        $userData = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/login/store", $userData);

        $response = $this->get("/attendance/$attendance1->id");

        $response->assertSee($attendance1->clock_in->format('H:i'));
        $response->assertSee($attendance1->clock_out->format('H:i'));
    }

    public function test_get_break(): void
    {
        $user = User::factory()->create([
            'name' => 'tester',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
        ]);

       $attendance3 = Attendance::factory()->create([
            'clock_in' => Carbon::now()->addDays(2),
            'clock_out'=> Carbon::now()->addDays(2)->addHours(9),
            'user_id' => $user->id,
        ]);

        //休憩は20分の想定
        $break3 = BreakTime::factory()->create([
            'break_start' => Carbon::now()->addDays(2)->addMinutes(10),
            'break_end' => Carbon::now()->addDays(2)->addMinutes(20),
            'attendance_id' => $attendance3->id,
        ]);

        $break4 = BreakTime::factory()->create([
            'break_start' => Carbon::now()->addDays(2)->addMinutes(30),
            'break_end' => Carbon::now()->addDays(2)->addMinute(40),
            'attendance_id' => $attendance3->id,
        ]);

        $userData = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/login/store", $userData);

        $response = $this->get("/attendance/$attendance3->id");

        $response->assertSee($break3->break_start->format('H:i'));
        $response->assertSee($break3->break_end->format('H:i'));

        $response->assertSee($break4->break_start->format('H:i'));
        $response->assertSee($break4->break_end->format('H:i'));
    }
}