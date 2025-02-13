<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Middleware\VerifyCsrfToken;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_shows_validation_message_when_name_is_missing()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $response = $this->get('/register');
        $response->assertStatus(200);

        $data = [
            'email' => 'test1@example.com',
            'password' => 'password1',
            'password_confirmation' => 'password1',
        ];
        $response = $this->post('/register', $data);

        $response->assertSessionHasErrors(['name' => 'お名前を入力してください']);

        $response->assertRedirect('/register');
    }

    public function test_shows_validation_message_when_email_is_missing()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $response = $this->get('/register');
        $response->assertStatus(200);

        $data = [
            'name' => 'test_user1',
            'password' => 'password1',
            'password_confirmation' => 'password1',
        ];
        $response = $this->post('/register', $data);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);

        $response->assertRedirect('/register');
    }

    public function test_shows_validation_message_when_password_is_missing()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $response = $this->get('/register');
        $response->assertStatus(200);

        $data = [
            'name' => 'test_user1',
            'email' => 'test1@example.com',
        ];
        $response = $this->post('/register', $data);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);

        $response->assertRedirect('/register');
    }

    public function test_shows_validation_message_when_password_is_under7()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $response = $this->get('/register');
        $response->assertStatus(200);

        $data = [
            'name' => 'test_user1',
            'email' => 'test1@example.com',
            'password' => 'pass',
            'password_confirmation' => 'pass',
        ];
        $response = $this->post('/register', $data);

        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);

        $response->assertRedirect('/register');
    }

    public function test_shows_validation_message_when_password_not_match()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $response = $this->get('/register');
        $response->assertStatus(200);

        $data = [
            'name' => 'test_user1',
            'email' => 'test1@example.com',
            'password' => 'password1',
            'password_confirmation' => 'password2',
        ];
        $response = $this->post('/register', $data);

        $response->assertSessionHasErrors(['password_confirmation' => 'パスワードと一致しません']);

        $response->assertRedirect('/register');
    }

    public function test_create_user()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $response = $this->get('/register');
        $response->assertStatus(200);

        $data = [
            'name' => 'test_user1',
            'email' => 'test1@example.com',
            'password' => 'password1',
            'password_confirmation' => 'password1',
        ];

        $response = $this->post('/register', $data);

        $response->assertRedirect('/email/verify');

        $this->assertDatabaseHas('users', [
            'name' => 'test_user1',
            'email' => 'test1@example.com',
        ]);

        $user = \App\Models\User::where('email', 'test1@example.com')->first();
        $this->assertTrue(\Hash::check('password1', $user->password));

        $user->markEmailAsVerified();

        $this->actingAs($user)
            ->get('/')
            ->assertStatus(200);
    }
}
