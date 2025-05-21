<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use Carbon\Carbon;
use App\Models\BreakTime;
use App\Models\User;

class RequestAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_validation_attending_after_reaving_time(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
        ]);

       $attendance3 = Attendance::factory()->create([
            'clock_in' => Carbon::create(2025, 5, 20, 10, 0, 0),
            'clock_out'=> Carbon::create(2025, 5, 20, 10, 0, 0)->addHours(9),
            'user_id' => $user->id,
        ]);

        //休憩は20分の想定
        $break3 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinutes(10),
            'break_end' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinutes(20),
            'attendance_id' => $attendance3->id,
        ]);

        $break4 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinutes(30),
            'break_end' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinute(40),
            'attendance_id' => $attendance3->id,
        ]);

        $userData = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/login/store", $userData);

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
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
        ]);

       $attendance3 = Attendance::factory()->create([
            'clock_in' => Carbon::create(2025, 5, 20, 10, 0, 0),
            'clock_out'=> Carbon::create(2025, 5, 20, 10, 0, 0)->addHours(9),
            'user_id' => $user->id,
        ]);

        //休憩は20分の想定
        $break3 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinutes(10),
            'break_end' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinutes(20),
            'attendance_id' => $attendance3->id,
        ]);

        $break4 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinutes(30),
            'break_end' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinute(40),
            'attendance_id' => $attendance3->id,
        ]);

        $userData = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/login/store", $userData);

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
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
        ]);

       $attendance3 = Attendance::factory()->create([
            'clock_in' => Carbon::create(2025, 5, 20, 10, 0, 0),
            'clock_out'=> Carbon::create(2025, 5, 20, 10, 0, 0)->addHours(9),
            'user_id' => $user->id,
        ]);

        //休憩は20分の想定
        $break3 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinutes(10),
            'break_end' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinutes(20),
            'attendance_id' => $attendance3->id,
        ]);

        $break4 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinutes(30),
            'break_end' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinute(40),
            'attendance_id' => $attendance3->id,
        ]);

        $userData = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/login/store", $userData);

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
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
        ]);

       $attendance3 = Attendance::factory()->create([
            'clock_in' => Carbon::create(2025, 5, 20, 10, 0, 0),
            'clock_out'=> Carbon::create(2025, 5, 20, 10, 0, 0)->addHours(9),
            'user_id' => $user->id,
        ]);

        //休憩は20分の想定
        $break3 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinutes(10),
            'break_end' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinutes(20),
            'attendance_id' => $attendance3->id,
        ]);

        $break4 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinutes(30),
            'break_end' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinute(40),
            'attendance_id' => $attendance3->id,
        ]);

        $userData = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/login/store", $userData);

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
    
    public function test_send_request(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
            'role' => 2,
        ]);

       $attendance3 = Attendance::factory()->create([
            'clock_in' =>Carbon::create(2025, 5, 20, 10, 0, 0),
            'clock_out'=> Carbon::create(2025, 5, 20, 10, 0, 0)->addHours(9),
            'user_id' => $user->id,
        ]);

        //休憩は20分の想定
        $break3 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinutes(10),
            'break_end' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinutes(20),
            'attendance_id' => $attendance3->id,
        ]);

        $break4 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinutes(30),
            'break_end' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinute(40),
            'attendance_id' => $attendance3->id,
        ]);

        $userData = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/login/store", $userData);

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
            "reason" => "test",
        ];

        $response = $this->post("/attendance/modify", $requestData);

        $attendanceCorrection = AttendanceCorrection::where('attendance_id',$attendance3->id)->first();

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

        $response = $this->get("/stamp_correction_request/approve/$attendanceCorrection->id");
        $response->assertSee($user->name);
        $response->assertSee('test');

        $response = $this->get("/stamp_correction_request/list");
        $response->assertSee($user->name);
        $response->assertSee('承認待ち');
        $response->assertSee($attendanceCorrection->created_at->format('Y/m/d'));
        
    }

    public function test_wait_approve(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
            'role' => 2,
        ]);

       $attendance3 = Attendance::factory()->create([
            'clock_in' =>Carbon::create(2025, 5, 20, 10, 0, 0),
            'clock_out'=> Carbon::create(2025, 5, 20, 10, 0, 0)->addHours(9),
            'user_id' => $user->id,
        ]);

        //休憩は20分の想定
        $break3 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinutes(10),
            'break_end' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinutes(20),
            'attendance_id' => $attendance3->id,
        ]);

        $break4 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinutes(30),
            'break_end' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinute(40),
            'attendance_id' => $attendance3->id,
        ]);

        $attendance4 = Attendance::factory()->create([
            'clock_in' =>Carbon::create(2025, 5, 21, 10, 0, 0),
            'clock_out'=> Carbon::create(2025, 5, 21, 10, 0, 0)->addHours(9),
            'user_id' => $user->id,
        ]);

        //休憩は20分の想定
        $break5 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 21, 10, 0, 0)->addMinutes(10),
            'break_end' => Carbon::create(2025, 5, 21, 10, 0, 0)->addMinutes(30),
            'attendance_id' => $attendance4->id,
        ]);

        $userData = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/login/store", $userData);

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
            "reason" => "test",
        ];

        $response = $this->post("/attendance/modify", $requestData);

        $response = $this->get("/attendance/$attendance4->id");

        $requestData = [
            "attendance_id" => $attendance4->id,
            "clock_in" => $attendance4->clock_in->format('H:i'),
            "clock_out" => $attendance4->clock_out->format('H:i'),
            "breakTime_id" => [$break5->id,],
            "break_start" => [
                $break5->break_start->format('H:i'),
            ],
            "break_end" => [
                $break5->break_end->format('H:i'),
            ],
            "reason" => "test2",
        ];

        $response = $this->post("/attendance/modify", $requestData);

        $response = $this->get("/stamp_correction_request/list");

        $response->assertSee('test');
        $response->assertSee('test2');
        
    }

    public function test_approved_request(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
            'role' => 2,
        ]);

       $attendance3 = Attendance::factory()->create([
            'clock_in' =>Carbon::create(2025, 5, 20, 10, 0, 0),
            'clock_out'=> Carbon::create(2025, 5, 20, 10, 0, 0)->addHours(9),
            'user_id' => $user->id,
        ]);

        //休憩は20分の想定
        $break3 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinutes(10),
            'break_end' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinutes(20),
            'attendance_id' => $attendance3->id,
        ]);

        $break4 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinutes(30),
            'break_end' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinute(40),
            'attendance_id' => $attendance3->id,
        ]);

        $attendance4 = Attendance::factory()->create([
            'clock_in' =>Carbon::create(2025, 5, 21, 10, 0, 0),
            'clock_out'=> Carbon::create(2025, 5, 21, 10, 0, 0)->addHours(9),
            'user_id' => $user->id,
        ]);

        //休憩は20分の想定
        $break5 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 21, 10, 0, 0)->addMinutes(10),
            'break_end' => Carbon::create(2025, 5, 21, 10, 0, 0)->addMinutes(30),
            'attendance_id' => $attendance4->id,
        ]);

        $userData = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/login/store", $userData);

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
            "reason" => "test3",
        ];

        $response = $this->post("/attendance/modify", $requestData);

        $attendanceCorrection1 = AttendanceCorrection::where('attendance_id',$attendance3->id)->first();

        $attendanceCorrection1->update([
            'status' => 2,
        ]);

        $response = $this->get("/attendance/$attendance4->id");

        $requestData = [
            "attendance_id" => $attendance4->id,
            "clock_in" => $attendance4->clock_in->format('H:i'),
            "clock_out" => $attendance4->clock_out->format('H:i'),
            "breakTime_id" => [$break5->id,],
            "break_start" => [
                $break5->break_start->format('H:i'),
            ],
            "break_end" => [
                $break5->break_end->format('H:i'),
            ],
            "reason" => "test4",
        ];

        $response = $this->post("/attendance/modify", $requestData);

        $attendanceCorrection2 = AttendanceCorrection::where('attendance_id',$attendance4->id)->first();

        $attendanceCorrection2->update([
            'status' => 2,
        ]);

        $response = $this->get("/stamp_correction_request/list?tab=approved");

        $response->assertSee('test3');
        $response->assertSee('test4');
        
    }

    public function test_to_detail(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
            'role' => 2,
        ]);

       $attendance3 = Attendance::factory()->create([
            'clock_in' =>Carbon::create(2025, 5, 20, 10, 0, 0),
            'clock_out'=> Carbon::create(2025, 5, 20, 10, 0, 0)->addHours(9),
            'user_id' => $user->id,
        ]);

        //休憩は20分の想定
        $break3 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinutes(10),
            'break_end' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinutes(20),
            'attendance_id' => $attendance3->id,
        ]);

        $break4 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinutes(30),
            'break_end' => Carbon::create(2025, 5, 20, 10, 0, 0)->addMinute(40),
            'attendance_id' => $attendance3->id,
        ]);

        $attendance4 = Attendance::factory()->create([
            'clock_in' =>Carbon::create(2025, 5, 21, 10, 0, 0),
            'clock_out'=> Carbon::create(2025, 5, 21, 10, 0, 0)->addHours(9),
            'user_id' => $user->id,
        ]);

        //休憩は20分の想定
        $break5 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 21, 10, 0, 0)->addMinutes(10),
            'break_end' => Carbon::create(2025, 5, 21, 10, 0, 0)->addMinutes(30),
            'attendance_id' => $attendance4->id,
        ]);

        $userData = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/login/store", $userData);

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
            "reason" => "test3",
        ];

        $response = $this->post("/attendance/modify", $requestData);

        $response = $this->get("/attendance/$attendance4->id");

        $requestData = [
            "attendance_id" => $attendance4->id,
            "clock_in" => $attendance4->clock_in->format('H:i'),
            "clock_out" => $attendance4->clock_out->format('H:i'),
            "breakTime_id" => [$break5->id,],
            "break_start" => [
                $break5->break_start->format('H:i'),
            ],
            "break_end" => [
                $break5->break_end->format('H:i'),
            ],
            "reason" => "test4",
        ];

        $response = $this->post("/attendance/modify", $requestData);

        $response = $this->get("/stamp_correction_request/list");

        $response = $this->get("/attendance/$attendance3->id");
        $response->assertStatus(200);

        $response = $this->get("/attendance/$attendance4->id");
        $response->assertStatus(200);
    }
}