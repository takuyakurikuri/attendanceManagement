<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Carbon;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_attendance(): void
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

        $response->assertSee('出勤');

        $response = $this->post("/attendance/clockIn");

        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
    }

    public function test_invisible_attendance_button(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
        ]);

        Attendance::factory()->create([
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
        $response->assertSee('お疲れ様でした。');
        $response->assertDontSee('出勤');
    }

    public function test_attendance_check(): void
    {
        $user = User::factory()->create([
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

        $response = $this->post("/attendance/clockIn");

        $today = Carbon::today()->toDateString();

        $recordExists = Attendance::where('user_id',$user->id)->whereDate('clock_in', $today)->exists();

        $this->assertTrue($recordExists);

    }
}