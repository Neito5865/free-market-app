<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    // 名前が入力されていない場合、バリデーションメッセージが表示される
    public function test_shows_validation_message_when_name_is_missing()
    {
        // 会員登録ページへアクセス
        $response = $this->get('/register');
        $response->assertStatus(200);

        // 必要なデータを準備
        $data = [
            'email' => 'test1@example.com',
            'password' => 'password1',
            'password_confirmation' => 'password1',
        ];

        // 会員登録のリクエストを送信
        $response = $this->post('/register', $data);

        // 検証：バリデーションメッセージが表示されるか
        $response->assertSessionHasErrors(['name' => 'お名前を入力してください']);

        // リダイレクト先が正しいか確認
        $response->assertRedirect('/register');
    }

    // メールアドレスが入力されていない場合、バリデーションメッセージが表示される
    public function test_shows_validation_message_when_email_is_missing()
    {
        // 会員登録ページへアクセス
        $response = $this->get('/register');
        $response->assertStatus(200);

        // 必要なデータを準備
        $data = [
            'name' => 'test_user1',
            'password' => 'password1',
            'password_confirmation' => 'password1',
        ];

        // 会員登録のリクエストを送信
        $response = $this->post('/register', $data);

        // 検証：バリデーションメッセージが表示されるか
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);

        // リダイレクト先が正しいか確認
        $response->assertRedirect('/register');
    }

    // パスワードが入力されていない場合、バリデーションメッセージが表示される
    public function test_shows_validation_message_when_password_is_missing()
    {
        // 会員登録ページへアクセス
        $response = $this->get('/register');
        $response->assertStatus(200);

        // 必要なデータを準備
        $data = [
            'name' => 'test_user1',
            'email' => 'test1@example.com',
        ];

        // 会員登録のリクエストを送信
        $response = $this->post('/register', $data);

        // 検証：バリデーションメッセージが表示されるか
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);

        // リダイレクト先が正しいか確認
        $response->assertRedirect('/register');
    }

    // パスワードが7文字以下の場合、バリデーションメッセージが表示される
    public function test_shows_validation_message_when_password_is_under7()
    {
        // 会員登録ページへアクセス
        $response = $this->get('/register');
        $response->assertStatus(200);

        // 必要なデータを準備
        $data = [
            'name' => 'test_user1',
            'email' => 'test1@example.com',
            'password' => 'pass',
            'password_confirmation' => 'pass',
        ];

        // 会員登録のリクエストを送信
        $response = $this->post('/register', $data);

        // 検証：バリデーションメッセージが表示されるか
        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);

        // リダイレクト先が正しいか確認
        $response->assertRedirect('/register');
    }

    // パスワードと確認用パスワードと一致しない場合、バリデーションメッセージが表示される
    public function test_shows_validation_message_when_password_not_match()
    {
        // 会員登録ページへアクセス
        $response = $this->get('/register');
        $response->assertStatus(200);

        // 必要なデータを準備
        $data = [
            'name' => 'test_user1',
            'email' => 'test1@example.com',
            'password' => 'password1',
            'password_confirmation' => 'password2',
        ];

        // 会員登録のリクエストを送信
        $response = $this->post('/register', $data);

        // 検証：バリデーションメッセージが表示されるか
        $response->assertSessionHasErrors(['password_confirmation' => 'パスワードと一致しません']);

        // リダイレクト先が正しいか確認
        $response->assertRedirect('/register');
    }

    // 会員登録され、ログイン画面に遷移
    public function test_create_user()
    {
        // 会員登録ページへアクセス
        $response = $this->get('/register');
        $response->assertStatus(200);

        // 必要なデータを準備
        $data = [
            'name' => 'test_user1',
            'email' => 'test1@example.com',
            'password' => 'password1',
            'password_confirmation' => 'password1',
        ];

        // 会員登録のリクエストを送信
        $response = $this->post('/register', $data);

        // リダイレクト先が正しいか確認
        $response->assertRedirect('/email/verify');

        // データベースにユーザーが登録されているか
        $this->assertDatabaseHas('users', [
            'name' => 'test_user1',
            'email' => 'test1@example.com',
        ]);

        // 登録されたパスワードがハッシュ化されているか確認
        $user = \App\Models\User::where('email', 'test1@example.com')->first();
        $this->assertTrue(\Hash::check('password1', $user->password));

        // ユーザーを認証済みに設定
        $user->markEmailAsVerified();

        // 認証状態でトップページにアクセスできることを確認
        $this->actingAs($user)
            ->get('/')
            ->assertStatus(200);
    }
}
