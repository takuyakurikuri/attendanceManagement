<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_validation_name() :void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);

        $userData = [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'testpassword',
            'password_confirmation' => 'testpassword',
        ];

        $response = $this->post('/register/store', $userData);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_validation_email()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);

        $userData = [
            'name' => 'test太郎',
            'email' => '',
            'password' => 'testpassword',
            'password_confirmation' => 'testpassword',
        ];

        $response = $this->post('/register/store', $userData);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_validation_password_under_7digits()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);

        $userData = [
            'name' => 'test太郎',
            'email' => 'test@example.com',
            'password' => 'test123',
            'password_confirmation' => 'test123',
        ];

        $response = $this->post('/register/store', $userData);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_validation_password_confirmation()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);

        $userData = [
            'name' => 'test太郎',
            'email' => 'test@example.com',
            'password' => 'test123',
            'password_confirmation' => 'test456',
        ];

        $response = $this->post('/register/store', $userData);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_validation_password()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);

        $userData = [
            'name' => 'test太郎',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ];

        $response = $this->post('/register/store', $userData);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_register_success()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);

        $userData = [
            'name' => 'test太郎',
            'email' => 'test@example.com',
            'password' => 'test1234',
            'password_confirmation' => 'test1234',
        ];

        $response = $this->post('/register/store', $userData);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect('/email/verify');
    }
}