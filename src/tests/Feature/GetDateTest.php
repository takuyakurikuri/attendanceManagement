<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;

class GetDateTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_getDate()
    {
        Carbon::setTestNow(Carbon::create(2025, 5, 20, 10, 0));
        
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

        $response->assertSee('10:00');

        Carbon::setTestNow();
    }
}