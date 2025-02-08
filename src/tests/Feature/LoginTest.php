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

        // ログインページへアクセス
        $response = $this->get('/login');
        $response->assertStatus(200);

        // 必要なデータを準備
        $data = [
            'password' => 'password',
        ];

        // 会員登録のリクエストを送信
        $response = $this->post('/login', $data);

        // 検証：バリデーションメッセージが表示されるか
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);

        // リダイレクト先が正しいか確認
        $response->assertRedirect('/login');
    }

    public function test_shows_validation_message_when_password_is_missing()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        // ログインページへアクセス
        $response = $this->get('/login');
        $response->assertStatus(200);

        // 必要なデータを準備
        $data = [
            'email' => 'user1@example.com',
        ];

        // ログインのリクエストを送信
        $response = $this->post('/login', $data);

        // 検証：バリデーションメッセージが表示されるか
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);

        // リダイレクト先が正しいか確認
        $response->assertRedirect('/login');
    }

    // 登録情報にない情報が入力された場合、バリデーションメッセージが表示される
    public function test_shows_validation_message_when_not_in_registration()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        // ログインページへアクセス
        $response = $this->get('/login');
        $response->assertStatus(200);

        // 必要なデータを準備
        $data = [
            'email' => 'user100@example.com',
            'password' => 'password',
        ];

        // 会員登録のリクエストを送信
        $response = $this->post('/login', $data);

        // 検証：バリデーションメッセージが表示されるか
        $response->assertSessionHasErrors(['email' => __('auth.failed')]);

        // リダイレクト先が正しいか確認
        $response->assertRedirect('/login');
    }

    // 正しい情報が入力された場合、ログインが実行される
    public function test_login()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        // テスト用のユーザーを作成
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // ログインページへアクセス
        $response = $this->get('/login');
        $response->assertStatus(200);

        // 必要なデータを準備
        $data = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        // ログインのリクエストを送信
        $response = $this->post('/login', $data);

        // リダイレクト先が正しいか確認
        $response->assertRedirect('/?page=mylist');

        // ログイン状態を確認
        $this->assertTrue(Auth::check());

        // ログインしたユーザーが正しいか確認
        $this->assertEquals($user->id, Auth::id());
    }
}
