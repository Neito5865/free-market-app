<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Middleware\VerifyCsrfToken;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_shows_validation_message_when_email_is_missing()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $response = $this->get('/login');
        $response->assertStatus(200);

        $data = [
            'password' => 'password',
        ];

        $response = $this->post('/login', $data);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);

        $response->assertRedirect('/login');
    }

    public function test_shows_validation_message_when_password_is_missing()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $response = $this->get('/login');
        $response->assertStatus(200);

        $data = [
            'email' => 'user1@example.com',
        ];
        $response = $this->post('/login', $data);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);

        $response->assertRedirect('/login');
    }

    // 登録情報にない情報が入力された場合、バリデーションメッセージが表示される
    public function test_shows_validation_message_when_not_in_registration()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $response = $this->get('/login');
        $response->assertStatus(200);

        $data = [
            'email' => 'user100@example.com',
            'password' => 'password',
        ];
        $response = $this->post('/login', $data);

        $response->assertSessionHasErrors(['email' => __('auth.failed')]);

        $response->assertRedirect('/login');
    }

    // 正しい情報が入力された場合、ログインが実行される
    public function test_login()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->get('/login');
        $response->assertStatus(200);

        $data = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];
        $response = $this->post('/login', $data);

        $response->assertRedirect('/?page=mylist');

        $this->assertTrue(Auth::check());

        $this->assertEquals($user->id, Auth::id());
    }
}
