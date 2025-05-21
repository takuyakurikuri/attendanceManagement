<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\BreakTime;

class ConfirmStatusTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_off_duty(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
        ]);

        $response = $this->get('/login');

        $response->assertStatus(200);

        $userData = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/login/store", $userData);

        $response->assertRedirect('/attendance');
        $response = $this->get('/attendance');

        $response->assertSee('勤務外');
    }

    public function test_at_work(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
        ]);

        $attendance = Attendance::factory()->create([
            'clock_in' => Carbon::now(),
            'clock_out'=> null,
            'user_id' => $user->id,
        ]);

        $response = $this->get('/login');

        $response->assertStatus(200);

        $userData = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/login/store", $userData);

        $response->assertRedirect('/attendance');
        $response = $this->get('/attendance');

        $response->assertSee('出勤中');
    }

    public function test_at_Break(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
        ]);

        $attendance = Attendance::factory()->create([
            'clock_in' => Carbon::now(),
            'clock_out'=> null,
            'user_id' => $user->id,
        ]);

        BreakTime::factory()->create([
            'break_start' => Carbon::now()->addHour(),
            'break_end' => null,
            'attendance_id' => $attendance->id,
        ]);

        $response = $this->get('/login');

        $response->assertStatus(200);

        $userData = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/login/store", $userData);

        $response->assertRedirect('/attendance');
        $response = $this->get('/attendance');

        $response->assertSee('休憩中');
    }

    public function test_finished_work(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
        ]);

        $attendance = Attendance::factory()->create([
            'clock_in' => Carbon::now(),
            'clock_out'=> Carbon::now()->addHours(9),
            'user_id' => $user->id,
        ]);

        $response = $this->get('/login');

        $response->assertStatus(200);

        $userData = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/login/store", $userData);

        $response->assertRedirect('/attendance');
        $response = $this->get('/attendance');

        $response->assertSee('退勤済');
    }
}