<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\BreakTime;
use App\Models\User;

class GetAttendanceListTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_attendance_list(): void
    {
        $user = User::factory()->create([
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
        
        $attendance2 = Attendance::factory()->create([
            'clock_in' => Carbon::now()->addDays(1),
            'clock_out'=> Carbon::now()->addDays(1)->addHours(9),
            'user_id' => $user->id,
        ]);

        $break2 = BreakTime::factory()->create([
            'break_start' => Carbon::now()->addDays(1)->addHours(1),
            'break_end' => Carbon::now()->addDays(1)->addHours(2),
            'attendance_id' => $attendance2->id,
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

        $response = $this->get('/attendance/list');

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

    public function test_this_month(): void
    {
        $user = User::factory()->create([
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

        $response = $this->get('/attendance/list');

        $response->assertSee($attendance1->clock_in->format('Y/m'));
    }

    public function test_previous_month(): void
    {
        $user = User::factory()->create([
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

        $attendance2 = Attendance::factory()->create([
            'clock_in' => Carbon::now()->subMonths(1),
            'clock_out'=> Carbon::now()->subMonths(1)->addHours(9),
            'user_id' => $user->id,
        ]);

        $break2 = BreakTime::factory()->create([
            'break_start' => Carbon::now()->subMonths(1)->addHours(1),
            'break_end' => Carbon::now()->subMonths(1)->addHours(2),
            'attendance_id' => $attendance2->id,
        ]);

        $userData = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/login/store", $userData);

        $response = $this->get('/attendance/list');

        $prevMonth = Carbon::now()->subMonth()->format('Y-m');

        $response = $this->get(route('attendance.list',['month' => $prevMonth]));

        $response->assertSee($attendance2->clock_in->format('Y/m'));

        $response->assertSee($attendance2->clock_in->format('m/d'));

        $response->assertSee($attendance2->clock_in->format('H:i'));

        $response->assertSee($attendance2->clock_out->format('H:i'));

        $totalBreakMinutes2 = $break2->break_start->diffInMinutes($break2->break_end);

        $breakHours2 = floor($totalBreakMinutes2 / 60);
        $breakMinutes2 = str_pad($totalBreakMinutes2 % 60, 2, '0', STR_PAD_LEFT);

        $response->assertSee($breakHours2 . ":" . $breakMinutes2);

        $workDuration2 = $attendance2->clock_in->diffInMinutes($attendance2->clock_out) - $totalBreakMinutes2;
        $workDuration2 = max(0, $workDuration2);
        $workHours2 = floor($workDuration2 / 60);
        $workMinutes2 = str_pad($workDuration2 % 60, 2, '0', STR_PAD_LEFT);

        $response->assertSee($workHours2 . ":" . $workMinutes2);
    }

    public function test_next_month(): void
    {
        $user = User::factory()->create([
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

        $attendance2 = Attendance::factory()->create([
            'clock_in' => Carbon::now()->addMonths(1),
            'clock_out'=> Carbon::now()->addMonths(1)->addHours(9),
            'user_id' => $user->id,
        ]);

        $break2 = BreakTime::factory()->create([
            'break_start' => Carbon::now()->addMonths(1)->addHours(1),
            'break_end' => Carbon::now()->addMonths(1)->addHours(2),
            'attendance_id' => $attendance2->id,
        ]);

        $userData = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->post("/login/store", $userData);

        $response = $this->get('/attendance/list');

        $nextMonth = Carbon::now()->addMonth()->format('Y-m');

        $response = $this->get(route('attendance.list',['month' => $nextMonth]));

        $response->assertSee($attendance2->clock_in->format('Y/m'));

        $response->assertSee($attendance2->clock_in->format('m/d'));

        $response->assertSee($attendance2->clock_in->format('H:i'));

        $response->assertSee($attendance2->clock_out->format('H:i'));

        $totalBreakMinutes2 = $break2->break_start->diffInMinutes($break2->break_end);

        $breakHours2 = floor($totalBreakMinutes2 / 60);
        $breakMinutes2 = str_pad($totalBreakMinutes2 % 60, 2, '0', STR_PAD_LEFT);

        $response->assertSee($breakHours2 . ":" . $breakMinutes2);

        $workDuration2 = $attendance2->clock_in->diffInMinutes($attendance2->clock_out) - $totalBreakMinutes2;
        $workDuration2 = max(0, $workDuration2);
        $workHours2 = floor($workDuration2 / 60);
        $workMinutes2 = str_pad($workDuration2 % 60, 2, '0', STR_PAD_LEFT);

        $response->assertSee($workHours2 . ":" . $workMinutes2);
    }

    public function test_detail(): void
    {
        $user = User::factory()->create([
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

        $response = $this->get('/attendance/list');

        $response = $this->get("/attendance/$attendance1->id");

        $response->assertSee('勤怠詳細');
        $response->assertSee($attendance1->clock_in->format('Y年'));
        $response->assertSee($attendance1->clock_in->format('n月j日'));
    }
}