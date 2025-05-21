<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Carbon;

class BreakTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_break(): void
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

        $response->assertSee('休憩入');

        $response = $this->post("/attendance/breakStart", ['attendance_id' => $attendance->id]);

        $response = $this->get('/attendance');

        $response->assertSee('休憩中');

        
    }

    public function test_any_break(): void
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

        $response->assertSee('休憩入');

        $response = $this->post("/attendance/breakStart", ['attendance_id' => $attendance->id]);

        $response = $this->get('/attendance');

        $response = $this->patch("/attendance/breakEnd", ['attendance_id' => $attendance->id]);

        $response = $this->get('/attendance');

        $response->assertSee('休憩入');

        
    }

    public function test_break_end(): void
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

        $response->assertSee('休憩入');

        $response = $this->post("/attendance/breakStart", ['attendance_id' => $attendance->id]);

        $response = $this->get('/attendance');

        $response->assertSee('休憩戻');

        $response = $this->patch("/attendance/breakEnd", ['attendance_id' => $attendance->id]);

        $response = $this->get('/attendance');

        $response->assertSee('出勤中');

        
    }

    public function test_any_break_end(): void
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

        $response->assertSee('休憩入');

        $response = $this->post("/attendance/breakStart", ['attendance_id' => $attendance->id]);

        $response = $this->get('/attendance');

        $response->assertSee('休憩戻');

        $response = $this->patch("/attendance/breakEnd", ['attendance_id' => $attendance->id]);

        $response = $this->get('/attendance');

        $response->assertSee('休憩入');

        $response = $this->post("/attendance/breakStart", ['attendance_id' => $attendance->id]);

        $response = $this->get('/attendance');

        $response->assertSee('休憩戻');

    }

    public function test_break_check(): void
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

        $response->assertSee('休憩入');

        $response = $this->post("/attendance/breakStart", ['attendance_id' => $attendance->id]);

        $response = $this->get('/attendance');

        $response->assertSee('休憩戻');

        $response = $this->patch("/attendance/breakEnd", ['attendance_id' => $attendance->id]);

        $response = $this->get('/attendance/list');

        $response->assertSee($attendance->clock_in->format('m/d'));
    }
}