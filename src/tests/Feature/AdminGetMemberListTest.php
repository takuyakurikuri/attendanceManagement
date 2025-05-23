<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\BreakTime;
use App\Models\User;

class AdminGetMemberListTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_get_member_data(): void
    {

        User::factory()->create([
            'email' => 'superuser@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
            'role' => 1,
        ]);
        
        $users = User::factory()->count(9)->create();
        
        $userData = [
            'email' => 'superuser@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/admin/login/store", $userData);

        $response = $this->get("/admin/staff/list");

        foreach($users as $user){
            $response->assertSee($user->name);
            $response->assertSee($user->email);
        }
    }

    public function test_get_member_this_month(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
        ]);

        $now = Carbon::now();

        $attendance1 = Attendance::factory()->create([
            'clock_in' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1),
            'clock_out'=> $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addHours(9),
            'user_id' => $user->id,
        ]);

        $break1 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addHours(1),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addHours(2),
            'attendance_id' => $attendance1->id,
        ]);
        
        $attendance2 = Attendance::factory()->create([
            'clock_in' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(1),
            'clock_out'=> $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(1)->addHours(9),
            'user_id' => $user->id,
        ]);

        $break2 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(1)->addHours(1),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(1)->addHours(2),
            'attendance_id' => $attendance2->id,
        ]);

        $attendance3 = Attendance::factory()->create([
            'clock_in' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(2),
            'clock_out'=> $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(2)->addHours(9),
            'user_id' => $user->id,
        ]);

        //休憩は20分の想定
        $break3 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(2)->addMinutes(10),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(2)->addMinutes(20),
            'attendance_id' => $attendance3->id,
        ]);

        $break4 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(2)->addMinutes(30),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(2)->addMinute(40),
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

        $response = $this->get("/admin/attendance/staff/$user->id");

        $response->assertSee($attendance1->clock_in->format('m/d'));
        $response->assertSee($attendance2->clock_in->format('m/d'));
        $response->assertSee($attendance3->clock_in->format('m/d'));
        
        $response->assertSee($attendance1->clock_in->format('H:i'));
        $response->assertSee($attendance2->clock_in->format('H:i'));
        $response->assertSee($attendance3->clock_in->format('H:i'));

        $response->assertSee($attendance1->clock_out->format('H:i'));
        $response->assertSee($attendance2->clock_out->format('H:i'));
        $response->assertSee($attendance3->clock_out->format('H:i'));

        $totalBreakMinutes1 = $break1->break_start->diffInMinutes($break1->break_end);
        $totalBreakMinutes2 = $break2->break_start->diffInMinutes($break2->break_end);
        
        $totalBreakMinutes3_1 = $break3->break_start->diffInMinutes($break3->break_end);
        $totalBreakMinutes3_2 = $break4->break_start->diffInMinutes($break4->break_end);
        $totalBreakMinutes3 = $totalBreakMinutes3_1 + $totalBreakMinutes3_2;


        $breakHours1 = floor($totalBreakMinutes1 / 60);
        $breakMinutes1 = str_pad($totalBreakMinutes1 % 60, 2, '0', STR_PAD_LEFT);
        $breakHours2 = floor($totalBreakMinutes2 / 60);
        $breakMinutes2 = str_pad($totalBreakMinutes2 % 60, 2, '0', STR_PAD_LEFT);
        $breakHours3 = floor($totalBreakMinutes3 / 60);
        $breakMinutes3 = str_pad($totalBreakMinutes3 % 60, 2, '0', STR_PAD_LEFT);

        $response->assertSee($breakHours1 . ":" . $breakMinutes1);
        $response->assertSee($breakHours2 . ":" . $breakMinutes2);
        $response->assertSee($breakHours3 . ":" . $breakMinutes3);

        $workDuration1 = $attendance1->clock_in->diffInMinutes($attendance1->clock_out) - $totalBreakMinutes1;
        $workDuration1 = max(0, $workDuration1);
        $workHours1 = floor($workDuration1 / 60);
        $workMinutes1 = str_pad($workDuration1 % 60, 2, '0', STR_PAD_LEFT);

        $workDuration2 = $attendance2->clock_in->diffInMinutes($attendance2->clock_out) - $totalBreakMinutes2;
        $workDuration2 = max(0, $workDuration2);
        $workHours2 = floor($workDuration2 / 60);
        $workMinutes2 = str_pad($workDuration2 % 60, 2, '0', STR_PAD_LEFT);

        $workDuration3 = $attendance3->clock_in->diffInMinutes($attendance3->clock_out) - $totalBreakMinutes3;
        $workDuration3 = max(0, $workDuration3);
        $workHours3 = floor($workDuration3 / 60);
        $workMinutes3 = str_pad($workDuration3 % 60, 2, '0', STR_PAD_LEFT);

        $response->assertSee($workHours1 . ":" . $workMinutes1);
        $response->assertSee($workHours2 . ":" . $workMinutes2);
        $response->assertSee($workHours3 . ":" . $workMinutes3);
    }

    public function test_get_member_previous_month(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
        ]);

        $now = Carbon::now();

        $attendance1 = Attendance::factory()->create([
            'clock_in' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->subMonths(1),
            'clock_out'=> $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addHours(9)->subMonths(1),
            'user_id' => $user->id,
        ]);

        $break1 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addHours(1)->subMonths(1),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addHours(2)->subMonths(1),
            'attendance_id' => $attendance1->id,
        ]);
        
        $attendance2 = Attendance::factory()->create([
            'clock_in' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(1)->subMonths(1),
            'clock_out'=> $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(1)->addHours(9)->subMonths(1),
            'user_id' => $user->id,
        ]);

        $break2 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(1)->addHours(1)->subMonths(1),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(1)->addHours(2)->subMonths(1),
            'attendance_id' => $attendance2->id,
        ]);

        $attendance3 = Attendance::factory()->create([
            'clock_in' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(2)->subMonths(1),
            'clock_out'=> $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(2)->addHours(9)->subMonths(1),
            'user_id' => $user->id,
        ]);

        //休憩は20分の想定
        $break3 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(2)->addMinutes(10)->subMonths(1),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(2)->addMinutes(20)->subMonths(1),
            'attendance_id' => $attendance3->id,
        ]);

        $break4 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(2)->addMinutes(30)->subMonths(1),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(2)->addMinute(40)->subMonths(1),
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

        $response = $this->get("/admin/attendance/staff/$user->id");

        $prevMonth = $now->copy()->subMonth()->format('Y-m');


        $response = $this->get(route('admin.attendance.list',['user_id'=>$user->id,'month' => $prevMonth]));

        $response->assertSee($attendance1->clock_in->format('m/d'));
        $response->assertSee($attendance2->clock_in->format('m/d'));
        $response->assertSee($attendance3->clock_in->format('m/d'));
        
        $response->assertSee($attendance1->clock_in->format('H:i'));
        $response->assertSee($attendance2->clock_in->format('H:i'));
        $response->assertSee($attendance3->clock_in->format('H:i'));

        $response->assertSee($attendance1->clock_out->format('H:i'));
        $response->assertSee($attendance2->clock_out->format('H:i'));
        $response->assertSee($attendance3->clock_out->format('H:i'));

        $totalBreakMinutes1 = $break1->break_start->diffInMinutes($break1->break_end);
        $totalBreakMinutes2 = $break2->break_start->diffInMinutes($break2->break_end);
        
        $totalBreakMinutes3_1 = $break3->break_start->diffInMinutes($break3->break_end);
        $totalBreakMinutes3_2 = $break4->break_start->diffInMinutes($break4->break_end);
        $totalBreakMinutes3 = $totalBreakMinutes3_1 + $totalBreakMinutes3_2;


        $breakHours1 = floor($totalBreakMinutes1 / 60);
        $breakMinutes1 = str_pad($totalBreakMinutes1 % 60, 2, '0', STR_PAD_LEFT);
        $breakHours2 = floor($totalBreakMinutes2 / 60);
        $breakMinutes2 = str_pad($totalBreakMinutes2 % 60, 2, '0', STR_PAD_LEFT);
        $breakHours3 = floor($totalBreakMinutes3 / 60);
        $breakMinutes3 = str_pad($totalBreakMinutes3 % 60, 2, '0', STR_PAD_LEFT);

        $response->assertSee($breakHours1 . ":" . $breakMinutes1);
        $response->assertSee($breakHours2 . ":" . $breakMinutes2);
        $response->assertSee($breakHours3 . ":" . $breakMinutes3);

        $workDuration1 = $attendance1->clock_in->diffInMinutes($attendance1->clock_out) - $totalBreakMinutes1;
        $workDuration1 = max(0, $workDuration1);
        $workHours1 = floor($workDuration1 / 60);
        $workMinutes1 = str_pad($workDuration1 % 60, 2, '0', STR_PAD_LEFT);

        $workDuration2 = $attendance2->clock_in->diffInMinutes($attendance2->clock_out) - $totalBreakMinutes2;
        $workDuration2 = max(0, $workDuration2);
        $workHours2 = floor($workDuration2 / 60);
        $workMinutes2 = str_pad($workDuration2 % 60, 2, '0', STR_PAD_LEFT);

        $workDuration3 = $attendance3->clock_in->diffInMinutes($attendance3->clock_out) - $totalBreakMinutes3;
        $workDuration3 = max(0, $workDuration3);
        $workHours3 = floor($workDuration3 / 60);
        $workMinutes3 = str_pad($workDuration3 % 60, 2, '0', STR_PAD_LEFT);

        $response->assertSee($workHours1 . ":" . $workMinutes1);
        $response->assertSee($workHours2 . ":" . $workMinutes2);
        $response->assertSee($workHours3 . ":" . $workMinutes3);
    }

    public function test_get_member_next_month(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
        ]);

        $now = Carbon::now();

        $attendance1 = Attendance::factory()->create([
            'clock_in' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addMonths(1),
            'clock_out'=> $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addHours(9)->addMonths(1),
            'user_id' => $user->id,
        ]);

        $break1 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addHours(1)->addMonths(1),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addHours(2)->addMonths(1),
            'attendance_id' => $attendance1->id,
        ]);
        
        $attendance2 = Attendance::factory()->create([
            'clock_in' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(1)->addMonths(1),
            'clock_out'=> $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(1)->addHours(9)->addMonths(1),
            'user_id' => $user->id,
        ]);

        $break2 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(1)->addHours(1)->addMonths(1),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(1)->addHours(2)->addMonths(1),
            'attendance_id' => $attendance2->id,
        ]);

        $attendance3 = Attendance::factory()->create([
            'clock_in' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(2)->addMonths(1),
            'clock_out'=> $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(2)->addHours(9)->addMonths(1),
            'user_id' => $user->id,
        ]);

        //休憩は20分の想定
        $break3 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(2)->addMinutes(10)->addMonths(1),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(2)->addMinutes(20)->addMonths(1),
            'attendance_id' => $attendance3->id,
        ]);

        $break4 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(2)->addMinutes(30)->addMonths(1),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(2)->addMinute(40)->addMonths(1),
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

        $response = $this->get("/admin/attendance/staff/$user->id");

        $nextMonth = $now->copy()->addMonth()->format('Y-m');


        $response = $this->get(route('admin.attendance.list',['user_id'=>$user->id,'month' => $nextMonth]));

        $response->assertSee($attendance1->clock_in->format('m/d'));
        $response->assertSee($attendance2->clock_in->format('m/d'));
        $response->assertSee($attendance3->clock_in->format('m/d'));
        
        $response->assertSee($attendance1->clock_in->format('H:i'));
        $response->assertSee($attendance2->clock_in->format('H:i'));
        $response->assertSee($attendance3->clock_in->format('H:i'));

        $response->assertSee($attendance1->clock_out->format('H:i'));
        $response->assertSee($attendance2->clock_out->format('H:i'));
        $response->assertSee($attendance3->clock_out->format('H:i'));

        $totalBreakMinutes1 = $break1->break_start->diffInMinutes($break1->break_end);
        $totalBreakMinutes2 = $break2->break_start->diffInMinutes($break2->break_end);
        
        $totalBreakMinutes3_1 = $break3->break_start->diffInMinutes($break3->break_end);
        $totalBreakMinutes3_2 = $break4->break_start->diffInMinutes($break4->break_end);
        $totalBreakMinutes3 = $totalBreakMinutes3_1 + $totalBreakMinutes3_2;


        $breakHours1 = floor($totalBreakMinutes1 / 60);
        $breakMinutes1 = str_pad($totalBreakMinutes1 % 60, 2, '0', STR_PAD_LEFT);
        $breakHours2 = floor($totalBreakMinutes2 / 60);
        $breakMinutes2 = str_pad($totalBreakMinutes2 % 60, 2, '0', STR_PAD_LEFT);
        $breakHours3 = floor($totalBreakMinutes3 / 60);
        $breakMinutes3 = str_pad($totalBreakMinutes3 % 60, 2, '0', STR_PAD_LEFT);

        $response->assertSee($breakHours1 . ":" . $breakMinutes1);
        $response->assertSee($breakHours2 . ":" . $breakMinutes2);
        $response->assertSee($breakHours3 . ":" . $breakMinutes3);

        $workDuration1 = $attendance1->clock_in->diffInMinutes($attendance1->clock_out) - $totalBreakMinutes1;
        $workDuration1 = max(0, $workDuration1);
        $workHours1 = floor($workDuration1 / 60);
        $workMinutes1 = str_pad($workDuration1 % 60, 2, '0', STR_PAD_LEFT);

        $workDuration2 = $attendance2->clock_in->diffInMinutes($attendance2->clock_out) - $totalBreakMinutes2;
        $workDuration2 = max(0, $workDuration2);
        $workHours2 = floor($workDuration2 / 60);
        $workMinutes2 = str_pad($workDuration2 % 60, 2, '0', STR_PAD_LEFT);

        $workDuration3 = $attendance3->clock_in->diffInMinutes($attendance3->clock_out) - $totalBreakMinutes3;
        $workDuration3 = max(0, $workDuration3);
        $workHours3 = floor($workDuration3 / 60);
        $workMinutes3 = str_pad($workDuration3 % 60, 2, '0', STR_PAD_LEFT);

        $response->assertSee($workHours1 . ":" . $workMinutes1);
        $response->assertSee($workHours2 . ":" . $workMinutes2);
        $response->assertSee($workHours3 . ":" . $workMinutes3);
    }

    public function test_get_attendance_detail(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' =>
            now(),
        ]);

        $now = Carbon::now();

        $attendance1 = Attendance::factory()->create([
            'clock_in' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1),
            'clock_out'=> $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addHours(9),
            'user_id' => $user->id,
        ]);

        $break1 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addHours(1),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addHours(2),
            'attendance_id' => $attendance1->id,
        ]);
        
        $attendance2 = Attendance::factory()->create([
            'clock_in' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(1),
            'clock_out'=> $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(1)->addHours(9),
            'user_id' => $user->id,
        ]);

        $break2 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(1)->addHours(1),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(1)->addHours(2),
            'attendance_id' => $attendance2->id,
        ]);

        $attendance3 = Attendance::factory()->create([
            'clock_in' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(2),
            'clock_out'=> $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(2)->addHours(9),
            'user_id' => $user->id,
        ]);

        //休憩は20分の想定
        $break3 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(2)->addMinutes(10),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(2)->addMinutes(20),
            'attendance_id' => $attendance3->id,
        ]);

        $break4 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(2)->addMinutes(30),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addHours(1)->addDays(2)->addMinute(40),
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

        $response = $this->get("/admin/attendance/staff/$user->id");

        $response = $this->get("/attendance/$attendance1->id");
        
        $response->assertStatus(200);
    }
}