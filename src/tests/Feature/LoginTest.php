<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{

    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_validation_email()
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
            'email' => '',
            'password' => 'password',
        ];

        $response = $this->post("/login/store", $userData);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_validation_password()
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
            'password' => '',
        ];

        $response = $this->post("/login/store", $userData);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_validation_no_user()
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
            'email' => 'testtest@example.com',
            'password' => 'passwordpassword',
        ];

        $response = $this->post("/login/store", $userData);

        $response->assertSessionHasErrors(['email']);
    }
}