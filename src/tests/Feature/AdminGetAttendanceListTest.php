<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Carbon;

class AdminGetAttendanceListTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_all_user(): void
    {
        
        $user1 = User::factory()->create(['name'=>"test1"]);
        $user2 = User::factory()->create(['name'=>"test2"]);
        $user3 = User::factory()->create(['name'=>"test3"]);
        

        $attendance1 = Attendance::factory()->create([
            'clock_in' => Carbon::create(2025, 5, 21, 10, 0, 0),
            'clock_out'=> Carbon::create(2025, 5, 21, 10, 0, 0)->addHours(9),
            'user_id' => $user1->id,
        ]);

        $break1 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 21, 10, 0, 0)->addHours(1),
            'break_end' => Carbon::create(2025, 5, 21, 10, 0, 0)->addHours(2),
            'attendance_id' => $attendance1->id,
        ]);
        
        $attendance2 = Attendance::factory()->create([
            'clock_in' => Carbon::create(2025, 5, 21, 10, 0, 0),
            'clock_out'=> Carbon::create(2025, 5, 21, 10, 0, 0)->addHours(9),
            'user_id' => $user2->id,
        ]);

        $break2 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 21, 10, 0, 0)->addHours(1),
            'break_end' => Carbon::create(2025, 5, 21, 10, 0, 0)->addHours(2),
            'attendance_id' => $attendance2->id,
        ]);

        $attendance3 = Attendance::factory()->create([
            'clock_in' => Carbon::create(2025, 5, 21, 10, 0, 0),
            'clock_out'=> Carbon::create(2025, 5, 21, 10, 0, 0)->addHours(9),
            'user_id' => $user3->id,
        ]);

        //休憩は20分の想定
        $break3 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 21, 10, 0, 0)->addMinutes(10),
            'break_end' => Carbon::create(2025, 5, 21, 10, 0, 0)->addMinutes(20),
            'attendance_id' => $attendance3->id,
        ]);

        $break4 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 21, 10, 0, 0)->addMinutes(30),
            'break_end' => Carbon::create(2025, 5, 21, 10, 0, 0)->addMinute(40),
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

        $response = $this->get("admin/attendance/list");

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

        $response->assertSee($user1->name);
        $response->assertSee($user2->name);
        $response->assertSee($user3->name);
    }

    public function test_today(): void
    {
        
        $user1 = User::factory()->create(['name'=>"test1"]);
        $user2 = User::factory()->create(['name'=>"test2"]);
        $user3 = User::factory()->create(['name'=>"test3"]);
        

        $attendance1 = Attendance::factory()->create([
            'clock_in' => Carbon::create(2025, 5, 21, 10, 0, 0),
            'clock_out'=> Carbon::create(2025, 5, 21, 10, 0, 0)->addHours(9),
            'user_id' => $user1->id,
        ]);

        $break1 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 21, 10, 0, 0)->addHours(1),
            'break_end' => Carbon::create(2025, 5, 21, 10, 0, 0)->addHours(2),
            'attendance_id' => $attendance1->id,
        ]);
        
        $attendance2 = Attendance::factory()->create([
            'clock_in' => Carbon::create(2025, 5, 21, 10, 0, 0),
            'clock_out'=> Carbon::create(2025, 5, 21, 10, 0, 0)->addHours(9),
            'user_id' => $user2->id,
        ]);

        $break2 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 21, 10, 0, 0)->addHours(1),
            'break_end' => Carbon::create(2025, 5, 21, 10, 0, 0)->addHours(2),
            'attendance_id' => $attendance2->id,
        ]);

        $attendance3 = Attendance::factory()->create([
            'clock_in' => Carbon::create(2025, 5, 21, 10, 0, 0),
            'clock_out'=> Carbon::create(2025, 5, 21, 10, 0, 0)->addHours(9),
            'user_id' => $user3->id,
        ]);

        //休憩は20分の想定
        $break3 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 21, 10, 0, 0)->addMinutes(10),
            'break_end' => Carbon::create(2025, 5, 21, 10, 0, 0)->addMinutes(20),
            'attendance_id' => $attendance3->id,
        ]);

        $break4 = BreakTime::factory()->create([
            'break_start' => Carbon::create(2025, 5, 21, 10, 0, 0)->addMinutes(30),
            'break_end' => Carbon::create(2025, 5, 21, 10, 0, 0)->addMinute(40),
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

        $response = $this->get("admin/attendance/list");

        $response->assertSee(Carbon::today()->format('Y/m/d'));
    }

    public function test_previous_day(): void
    {
        
        $user1 = User::factory()->create(['name'=>"test1"]);
        $user2 = User::factory()->create(['name'=>"test2"]);
        $user3 = User::factory()->create(['name'=>"test3"]);
        

        $now = Carbon::now();
        $attendance1 = Attendance::factory()->create([
            'clock_in' => $now->copy()->hour(10)->minute(0)->second(0)->subDays(1),
            'clock_out'=> $now->copy()->hour(10)->minute(0)->second(0)->subDays(1)->addHours(9),
            'user_id' => $user1->id,
        ]);

        $break1 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->subDays(1)->addHours(1),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->subDays(1)->addHours(2),
            'attendance_id' => $attendance1->id,
        ]);
        
        $attendance2 = Attendance::factory()->create([
            'clock_in' => $now->copy()->hour(10)->minute(0)->second(0)->subDays(1),
            'clock_out'=> $now->copy()->hour(10)->minute(0)->second(0)->subDays(1)->addHours(9),
            'user_id' => $user2->id,
        ]);

        $break2 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->subDays(1)->addHours(1),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->subDays(1)->addHours(2),
            'attendance_id' => $attendance2->id,
        ]);

        $attendance3 = Attendance::factory()->create([
            'clock_in' => $now->copy()->hour(10)->minute(0)->second(0)->subDays(1),
            'clock_out'=> $now->copy()->hour(10)->minute(0)->second(0)->subDays(1)->addHours(9),
            'user_id' => $user3->id,
        ]);

        //休憩は20分の想定
        $break3 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->subDays(1)->addMinutes(10),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->subDays(1)->addMinutes(20),
            'attendance_id' => $attendance3->id,
        ]);

        $break4 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->subDays(1)->addMinutes(30),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->subDays(1)->addMinute(40),
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

        $response = $this->get("admin/attendance/list");

        $prevDay = $now->copy()->subDay()->format('Y-m-d');

        $response = $this->get(route('member.list',['day' => $prevDay]));

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

        $response->assertSee($user1->name);
        $response->assertSee($user2->name);
        $response->assertSee($user3->name);

        $response->assertSee($now->copy()->subDay()->format('Y/m/d'));
    }

    public function test_tomorrow(): void
    {
        
        $user1 = User::factory()->create(['name'=>"test1"]);
        $user2 = User::factory()->create(['name'=>"test2"]);
        $user3 = User::factory()->create(['name'=>"test3"]);
        

        $now = Carbon::now();
        $attendance1 = Attendance::factory()->create([
            'clock_in' => $now->copy()->hour(10)->minute(0)->second(0)->addDays(1),
            'clock_out'=> $now->copy()->hour(10)->minute(0)->second(0)->addDays(1)->addHours(9),
            'user_id' => $user1->id,
        ]);

        $break1 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addDays(1)->addHours(1),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addDays(1)->addHours(2),
            'attendance_id' => $attendance1->id,
        ]);
        
        $attendance2 = Attendance::factory()->create([
            'clock_in' => $now->copy()->hour(10)->minute(0)->second(0)->addDays(1),
            'clock_out'=> $now->copy()->hour(10)->minute(0)->second(0)->addDays(1)->addHours(9),
            'user_id' => $user2->id,
        ]);

        $break2 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addDays(1)->addHours(1),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addDays(1)->addHours(2),
            'attendance_id' => $attendance2->id,
        ]);

        $attendance3 = Attendance::factory()->create([
            'clock_in' => $now->copy()->hour(10)->minute(0)->second(0)->addDays(1),
            'clock_out'=> $now->copy()->hour(10)->minute(0)->second(0)->addDays(1)->addHours(9),
            'user_id' => $user3->id,
        ]);

        //休憩は20分の想定
        $break3 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addDays(1)->addMinutes(10),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addDays(1)->addMinutes(20),
            'attendance_id' => $attendance3->id,
        ]);

        $break4 = BreakTime::factory()->create([
            'break_start' => $now->copy()->hour(10)->minute(0)->second(0)->addDays(1)->addMinutes(30),
            'break_end' => $now->copy()->hour(10)->minute(0)->second(0)->addDays(1)->addMinute(40),
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

        $response = $this->get("admin/attendance/list");

        $nextDay = $now->copy()->addDay()->format('Y-m-d');

        $response = $this->get(route('member.list',['day' => $nextDay]));

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

        $response->assertSee($user1->name);
        $response->assertSee($user2->name);
        $response->assertSee($user3->name);

        $response->assertSee($now->copy()->addDay()->format('Y/m/d'));
    }
}