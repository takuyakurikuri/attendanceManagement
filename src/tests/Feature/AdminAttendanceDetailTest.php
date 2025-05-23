<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Carbon;

class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_get_detail(): void
    {
        $user1 = User::factory()->create(['name'=>"test1"]);

        $now = Carbon::now();

        $attendance3 = Attendance::factory()->create([
            'clock_in' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1),
            'clock_out'=> $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addHours(9),
            'user_id' => $user1->id,
        ]);

        //休憩は20分の想定
        $break3 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addMinutes(10),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addMinutes(20),
            'attendance_id' => $attendance3->id,
        ]);

        $break4 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addMinutes(30),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addMinute(40),
            'attendance_id' => $attendance3->id,
        ]);

        User::factory()->create([
            'email' => 'superuser@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
            'role' => 1,
        ]);
        
        $userData = [
            'email' => 'superuser@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/admin/login/store", $userData);

        $response = $this->get("/attendance/$attendance3->id");


        $response->assertSee($attendance3->clock_in->format('Y年'));
        $response->assertSee($attendance3->clock_out->format('n月j日'));
        
        $response->assertSee($attendance3->clock_in->format('H:i'));
        $response->assertSee($attendance3->clock_out->format('H:i'));

        $response->assertSee($break3->break_start->format('H:i'));
        $response->assertSee($break3->break_end->format('H:i'));

        $response->assertSee($break4->break_start->format('H:i'));
        $response->assertSee($break4->break_end->format('H:i'));
    }

    public function test_attending_after_reaving_time(): void
    {
        $user1 = User::factory()->create(['name'=>"test1"]);

        $now = Carbon::now();

        $attendance3 = Attendance::factory()->create([
            'clock_in' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1),
            'clock_out'=> $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addHours(9),
            'user_id' => $user1->id,
        ]);

        //休憩は20分の想定
        $break3 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addMinutes(10),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addMinutes(20),
            'attendance_id' => $attendance3->id,
        ]);

        $break4 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addMinutes(30),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addMinute(40),
            'attendance_id' => $attendance3->id,
        ]);

        User::factory()->create([
            'email' => 'superuser@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
            'role' => 1,
        ]);
        
        $userData = [
            'email' => 'superuser@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/admin/login/store", $userData);

        $response = $this->get("/attendance/$attendance3->id");


        $requestData = [
            "attendance_id" => $attendance3->id,
            "clock_in" => $attendance3->clock_in->addHours(10)->format('H:i'),
            "clock_out" => $attendance3->clock_out->format('H:i'),
            "breakTime_id" => [$break3->id, $break4->id],
            "break_start" => [
                $break3->break_start->format('H:i'),
                $break4->break_start->format('H:i'),
            ],
            "break_end" => [
                $break3->break_end->format('H:i'),
                $break4->break_end->format('H:i'),
            ],
            "reason" => "test",
        ];

        $response = $this->post("/attendance/modify", $requestData);

        $response->assertSessionHasErrors(['clock_out']);
        $response = $this->get("/attendance/$attendance3->id");
        $response->assertSee("出勤時間もしくは退勤時間が不適切な値です");
    }

    public function test_validation_breaking_start_after_reaving_time(): void
    {
        $user1 = User::factory()->create(['name'=>"test1"]);

        $now = Carbon::now();

        $attendance3 = Attendance::factory()->create([
            'clock_in' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1),
            'clock_out'=> $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addHours(9),
            'user_id' => $user1->id,
        ]);

        //休憩は20分の想定
        $break3 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addMinutes(10),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addMinutes(20),
            'attendance_id' => $attendance3->id,
        ]);

        $break4 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addMinutes(30),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addMinute(40),
            'attendance_id' => $attendance3->id,
        ]);

        User::factory()->create([
            'email' => 'superuser@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
            'role' => 1,
        ]);
        
        $userData = [
            'email' => 'superuser@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/admin/login/store", $userData);

        $response = $this->get("/attendance/$attendance3->id");


        $requestData = [
            "attendance_id" => $attendance3->id,
            "clock_in" => $attendance3->clock_in->format('H:i'),
            "clock_out" => $attendance3->clock_out->format('H:i'),
            "breakTime_id" => [$break3->id, $break4->id],
            "break_start" => [
                $break3->break_start->addHours(10)->format('H:i'),
                $break4->break_start->format('H:i'),
            ],
            "break_end" => [
                $break3->break_end->format('H:i'),
                $break4->break_end->format('H:i'),
            ],
            "reason" => "test",
        ];

        $response = $this->post("/attendance/modify", $requestData);

        $response->assertSessionHasErrors(['break_end.0']);
        $response = $this->get("/attendance/$attendance3->id");
        $response->assertSee("出勤時間もしくは退勤時間が不適切な値です");

    }

    public function test_validation_breaking_end_after_reaving_time(): void
    {
        $user1 = User::factory()->create(['name'=>"test1"]);

        $now = Carbon::now();

        $attendance3 = Attendance::factory()->create([
            'clock_in' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1),
            'clock_out'=> $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addHours(9),
            'user_id' => $user1->id,
        ]);

        //休憩は20分の想定
        $break3 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addMinutes(10),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addMinutes(20),
            'attendance_id' => $attendance3->id,
        ]);

        $break4 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addMinutes(30),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addMinute(40),
            'attendance_id' => $attendance3->id,
        ]);

        User::factory()->create([
            'email' => 'superuser@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
            'role' => 1,
        ]);
        
        $userData = [
            'email' => 'superuser@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/admin/login/store", $userData);

        $response = $this->get("/attendance/$attendance3->id");


        $requestData = [
            "attendance_id" => $attendance3->id,
            "clock_in" => $attendance3->clock_in->format('H:i'),
            "clock_out" => $attendance3->clock_out->format('H:i'),
            "breakTime_id" => [$break3->id, $break4->id],
            "break_start" => [
                $break3->break_start->format('H:i'),
                $break4->break_start->format('H:i'),
            ],
            "break_end" => [
                $break3->break_end->addHours(10)->format('H:i'),
                $break4->break_end->format('H:i'),
            ],
            "reason" => "test",
        ];

        $response = $this->post("/attendance/modify", $requestData);

        $response->assertSessionHasErrors(['break_end.0']);
        $response = $this->get("/attendance/$attendance3->id");
        $response->assertSee("出勤時間もしくは退勤時間が不適切な値です");

    }

    public function test_validation_reason(): void
    {
        $user1 = User::factory()->create(['name'=>"test1"]);

        $now = Carbon::now();

        $attendance3 = Attendance::factory()->create([
            'clock_in' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1),
            'clock_out'=> $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addHours(9),
            'user_id' => $user1->id,
        ]);

        //休憩は20分の想定
        $break3 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addMinutes(10),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addMinutes(20),
            'attendance_id' => $attendance3->id,
        ]);

        $break4 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addMinutes(30),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addMinute(40),
            'attendance_id' => $attendance3->id,
        ]);

        User::factory()->create([
            'email' => 'superuser@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
            'role' => 1,
        ]);
        
        $userData = [
            'email' => 'superuser@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/admin/login/store", $userData);

        $response = $this->get("/attendance/$attendance3->id");


        $requestData = [
            "attendance_id" => $attendance3->id,
            "clock_in" => $attendance3->clock_in->format('H:i'),
            "clock_out" => $attendance3->clock_out->format('H:i'),
            "breakTime_id" => [$break3->id, $break4->id],
            "break_start" => [
                $break3->break_start->format('H:i'),
                $break4->break_start->format('H:i'),
            ],
            "break_end" => [
                $break3->break_end->format('H:i'),
                $break4->break_end->format('H:i'),
            ],
            "reason" => "",
        ];

        $response = $this->post("/attendance/modify", $requestData);

        $response->assertSessionHasErrors(['reason']);
        $response = $this->get("/attendance/$attendance3->id");
        $response->assertSee("備考欄を入力して下さい");

    }
}