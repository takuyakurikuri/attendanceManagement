<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\BreakCorrection;
use Carbon\Carbon;
use App\Models\BreakTime;
use App\Models\User;

class AdminApproveRequestTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_wait_approve_list(): void
    {
        User::factory()->create([
            'email' => 'superuser@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
            'role' => 1,
        ]);
        
        $user1 = User::factory()->create(['name'=>"test1"]);
        $user2 = User::factory()->create(['name'=>"test2"]);
        $user3 = User::factory()->create(['name'=>"test3"]);

       $attendance3 = Attendance::factory()->create([
            'clock_in' =>Carbon::create(2025, 5, 20, 10, 0, 0),
            'clock_out'=> Carbon::create(2025, 5, 20, 10, 0, 0)->addHours(9),
            'user_id' => $user1->id,
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
            'user_id' => $user2->id,
        ]);

        //休憩は20分の想定
        $break5 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 21, 10, 0, 0)->addMinutes(10),
            'break_end' => Carbon::create(2025, 5, 21, 10, 0, 0)->addMinutes(30),
            'attendance_id' => $attendance4->id,
        ]);

        $attendance5 = Attendance::factory()->create([
            'clock_in' =>Carbon::create(2025, 5, 21, 10, 0, 0),
            'clock_out'=> Carbon::create(2025, 5, 21, 10, 0, 0)->addHours(9),
            'user_id' => $user3->id,
        ]);

        //休憩は20分の想定
        $break6 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 21, 10, 0, 0)->addMinutes(10),
            'break_end' => Carbon::create(2025, 5, 21, 10, 0, 0)->addMinutes(30),
            'attendance_id' => $attendance5->id,
        ]);
        
        $userData = [
            'email' => 'superuser@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/admin/login/store", $userData);

        $AttendanceCorrection1 = AttendanceCorrection::create( [
            'clock_in' => $attendance3->clock_in,
            'clock_out' => $attendance3->clock_out,
            'reason' => "testcase1",
            'status' => 1,//申請中ステータスは'1'に設定
            'admin_id' => 1,//SUは'1'に設定
            'user_id' => $user1->id,
            'attendance_id' => $attendance3->id,
        ]);

        BreakCorrection::create([
            'break_start' => $break3->break_start,
            'break_end' => $break3->break_end,
            'attendance_correction_id' => $AttendanceCorrection1->id
        ]);

        BreakCorrection::create([
            'break_start' => $break4->break_start,
            'break_end' => $break4->break_end,
            'attendance_correction_id' => $AttendanceCorrection1->id
        ]);

        $AttendanceCorrection2 = AttendanceCorrection::create( [
            'clock_in' => $attendance4->clock_in,
            'clock_out' => $attendance4->clock_out,
            'reason' => "testcase2",
            'status' => 1,//申請中ステータスは'1'に設定
            'admin_id' => 1,//SUは'1'に設定
            'user_id' => $user2->id,
            'attendance_id' => $attendance4->id,
        ]);

        BreakCorrection::create([
            'break_start' => $break5->break_start,
            'break_end' => $break5->break_end,
            'attendance_correction_id' => $AttendanceCorrection2->id
        ]);

        $AttendanceCorrection3 = AttendanceCorrection::create( [
            'clock_in' => $attendance5->clock_in,
            'clock_out' => $attendance5->clock_out,
            'reason' => "testcase3",
            'status' => 1,//申請中ステータスは'1'に設定
            'admin_id' => 1,//SUは'1'に設定
            'user_id' => $user3->id,
            'attendance_id' => $attendance5->id,
        ]);

        BreakCorrection::create([
            'break_start' => $break6->break_start,
            'break_end' => $break6->break_end,
            'attendance_correction_id' => $AttendanceCorrection3->id
        ]);

        $response = $this->get("/stamp_correction_request/list");

        $response->assertSee('testcase');
        $response->assertSee('testcase2');
        $response->assertSee('testcase3');

        $response->assertSee($user1->name);
        $response->assertSee($user2->name);
        $response->assertSee($user3->name);
        
    }

    public function test_approved_list(): void
    {
        User::factory()->create([
            'email' => 'superuser@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
            'role' => 1,
        ]);
        
        $user1 = User::factory()->create(['name'=>"test1"]);
        $user2 = User::factory()->create(['name'=>"test2"]);
        $user3 = User::factory()->create(['name'=>"test3"]);

       $attendance3 = Attendance::factory()->create([
            'clock_in' =>Carbon::create(2025, 5, 20, 10, 0, 0),
            'clock_out'=> Carbon::create(2025, 5, 20, 10, 0, 0)->addHours(9),
            'user_id' => $user1->id,
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
            'user_id' => $user2->id,
        ]);

        //休憩は20分の想定
        $break5 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 21, 10, 0, 0)->addMinutes(10),
            'break_end' => Carbon::create(2025, 5, 21, 10, 0, 0)->addMinutes(30),
            'attendance_id' => $attendance4->id,
        ]);

        $attendance5 = Attendance::factory()->create([
            'clock_in' =>Carbon::create(2025, 5, 21, 10, 0, 0),
            'clock_out'=> Carbon::create(2025, 5, 21, 10, 0, 0)->addHours(9),
            'user_id' => $user3->id,
        ]);

        //休憩は20分の想定
        $break6 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 21, 10, 0, 0)->addMinutes(10),
            'break_end' => Carbon::create(2025, 5, 21, 10, 0, 0)->addMinutes(30),
            'attendance_id' => $attendance5->id,
        ]);
        
        $userData = [
            'email' => 'superuser@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/admin/login/store", $userData);

        $AttendanceCorrection1 = AttendanceCorrection::create( [
            'clock_in' => $attendance3->clock_in,
            'clock_out' => $attendance3->clock_out,
            'reason' => "testcase",
            'status' => 2,
            'admin_id' => 1,//SUは'1'に設定
            'user_id' => $user1->id,
            'attendance_id' => $attendance3->id,
        ]);

        BreakCorrection::create([
            'break_start' => $break3->break_start,
            'break_end' => $break3->break_end,
            'attendance_correction_id' => $AttendanceCorrection1->id
        ]);

        BreakCorrection::create([
            'break_start' => $break4->break_start,
            'break_end' => $break4->break_end,
            'attendance_correction_id' => $AttendanceCorrection1->id
        ]);

        $AttendanceCorrection2 = AttendanceCorrection::create( [
            'clock_in' => $attendance4->clock_in,
            'clock_out' => $attendance4->clock_out,
            'reason' => "testcase2",
            'status' => 2,
            'admin_id' => 1,//SUは'1'に設定
            'user_id' => $user2->id,
            'attendance_id' => $attendance4->id,
        ]);

        BreakCorrection::create([
            'break_start' => $break5->break_start,
            'break_end' => $break5->break_end,
            'attendance_correction_id' => $AttendanceCorrection2->id
        ]);

        $AttendanceCorrection3 = AttendanceCorrection::create( [
            'clock_in' => $attendance5->clock_in,
            'clock_out' => $attendance5->clock_out,
            'reason' => "testcase3",
            'status' => 2,
            'admin_id' => 1,//SUは'1'に設定
            'user_id' => $user3->id,
            'attendance_id' => $attendance5->id,
        ]);

        BreakCorrection::create([
            'break_start' => $break6->break_start,
            'break_end' => $break6->break_end,
            'attendance_correction_id' => $AttendanceCorrection3->id
        ]);

        $response = $this->get("/stamp_correction_request/list?tab=approved");

        $response->assertSee('testcase');
        $response->assertSee('testcase2');
        $response->assertSee('testcase3');

        $response->assertSee($user1->name);
        $response->assertSee($user2->name);
        $response->assertSee($user3->name);
        
    }

    public function test_request_detail(): void
    {
        User::factory()->create([
            'email' => 'superuser@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
            'role' => 1,
        ]);
        
        $user1 = User::factory()->create(['name'=>"test1"]);

       $attendance3 = Attendance::factory()->create([
            'clock_in' =>Carbon::create(2025, 5, 20, 10, 0, 0),
            'clock_out'=> Carbon::create(2025, 5, 20, 10, 0, 0)->addHours(9),
            'user_id' => $user1->id,
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
            'email' => 'superuser@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/admin/login/store", $userData);

        $AttendanceCorrection1 = AttendanceCorrection::create( [
            'clock_in' => $attendance3->clock_in,
            'clock_out' => $attendance3->clock_out,
            'reason' => "testcase",
            'status' => 1,//申請中ステータスは'1'に設定
            'admin_id' => 1,//SUは'1'に設定
            'user_id' => $user1->id,
            'attendance_id' => $attendance3->id,
        ]);

        $breakCorrection1 = BreakCorrection::create([
            'break_start' => $break3->break_start,
            'break_end' => $break3->break_end,
            'attendance_correction_id' => $AttendanceCorrection1->id
        ]);

        $breakCorrection2 = BreakCorrection::create([
            'break_start' => $break4->break_start,
            'break_end' => $break4->break_end,
            'attendance_correction_id' => $AttendanceCorrection1->id
        ]);


        $response = $this->get("/stamp_correction_request/approve/$AttendanceCorrection1->id");

        $response->assertSee('testcase');
        $response->assertSee($AttendanceCorrection1->clock_in->format('Y年'));
        $response->assertSee($AttendanceCorrection1->clock_in->format('n月j日'));
        $response->assertSee($AttendanceCorrection1->clock_in->format('H:i'));
        $response->assertSee($AttendanceCorrection1->clock_out->format('H:i'));
        
        $response->assertSee($breakCorrection1->break_start->format('H:i'));
        $response->assertSee($breakCorrection2->break_start->format('H:i'));
        $response->assertSee($breakCorrection1->break_end->format('H:i'));
        $response->assertSee($breakCorrection2->break_end->format('H:i'));

        $response->assertSee($user1->name);
    }

    public function test_approve_request(): void
    {
        User::factory()->create([
            'email' => 'superuser@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
            'role' => 1,
        ]);
        
        $user1 = User::factory()->create(['name'=>"test1"]);

       $attendance3 = Attendance::factory()->create([
            'clock_in' =>Carbon::create(2025, 5, 20, 10, 0, 0),
            'clock_out'=> Carbon::create(2025, 5, 20, 10, 0, 0)->addHours(9),
            'user_id' => $user1->id,
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
            'email' => 'superuser@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/admin/login/store", $userData);

        $AttendanceCorrection1 = AttendanceCorrection::create( [
            'clock_in' => $attendance3->clock_in->addMinutes(5),
            'clock_out' => $attendance3->clock_out->addMinutes(5),
            'reason' => "testcase",
            'status' => 1,//申請中ステータスは'1'に設定
            'admin_id' => 1,//SUは'1'に設定
            'user_id' => $user1->id,
            'attendance_id' => $attendance3->id,
        ]);

        $breakCorrection1 = BreakCorrection::create([
            'break_start' => $break3->break_start->addMinutes(10),
            'break_end' => $break3->break_end->addMinutes(10),
            'attendance_correction_id' => $AttendanceCorrection1->id
        ]);

        $breakCorrection2 = BreakCorrection::create([
            'break_start' => $break4->break_start->addMinutes(10),
            'break_end' => $break4->break_end->addMinutes(10),
            'attendance_correction_id' => $AttendanceCorrection1->id
        ]);


        $response = $this->get("/stamp_correction_request/approve/$AttendanceCorrection1->id");

        $Data = [
            'attendanceCorrection_id' => $AttendanceCorrection1->id,
            
        ];

        $response = $this->post("/stamp_correction_request/approve", $Data);

        $this->assertDatabaseHas('attendances', [
            'clock_in' => $attendance3->clock_in->addMinutes(5),
            'clock_out' => $attendance3->clock_out->addMinutes(5),
            'user_id' => $user1->id,
        ]);

        $this->assertDatabaseHas('break_times', [
            'break_start' => $break3->break_start->addMinutes(10),
            'break_end' => $break3->break_end->addMinutes(10),
            'attendance_id' => $attendance3->id
        ]);

        $this->assertDatabaseHas('break_times', [
            'break_start' => $break4->break_start->addMinutes(10),
            'break_end' => $break4->break_end->addMinutes(10),
            'attendance_id' => $attendance3->id
        ]);
        
    }
}