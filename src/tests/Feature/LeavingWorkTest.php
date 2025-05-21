<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Carbon;

class LeavingWorkTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_end_work(): void
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

        $userData = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/login/store", $userData);

        $response = $this->get('/attendance');

        $response->assertSee('退勤');

        $response = $this->patch("/attendance/clockOut", ['attendance_id' => $attendance->id]);

        $response = $this->get('/attendance');

        $response->assertSee('退勤済');

    }

    public function test_end_work_check(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
        ]);

        $userData = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/login/store", $userData);

        $response = $this->get('/attendance');

        $response = $this->post("/attendance/clockIn");
        
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('user_id',$user->id)->whereDate('clock_in',$today)->first();
        
        $response = $this->get('/attendance');

        $response = $this->patch("/attendance/clockOut", ['attendance_id' => $attendance->id]);;

        $response = $this->get('/attendance/list');

        $attendance = Attendance::where('user_id',$user->id)->whereDate('clock_in',$today)->first();

        $response->assertSee($attendance->clock_out->format('H:i'));

    }
}