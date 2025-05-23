<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Verified;

class EmailVerifyTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_send_email()
    {

        Notification::fake();

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

        $user = User::where('email', 'test@example.com')->first();

        $response->assertRedirect('/email/verify');

        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );
    }

    public function test_go_verify_site()//Duskが必要？
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

        $user = User::where('email', 'test@example.com')->first();

        $response->assertRedirect('/email/verify');

        $response->assertStatus(302);

    }

    public function test_complete_verify()
    {

        Event::fake();

        $user = User::factory()->create([
            'email' => 'verifytest@example.com',
            'email_verified_at' => Null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->getEmailForVerification()), 
            ]
        );

        $this->assertFalse($user->hasVerifiedEmail());

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect('/attendance');

        Event::assertDispatched(Verified::class);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());

        $homeResponse = $this->actingAs($user)->get('/attendance');
        $homeResponse->assertStatus(200);

    }
}