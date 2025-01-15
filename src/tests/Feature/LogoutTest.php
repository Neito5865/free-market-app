<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    // ログイアウトができる
    public function test_logout()
    {
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

        // ログイン状態を確認
        $this->assertTrue(Auth::check());

        // ログインしたユーザーが正しいか確認
        $this->assertEquals($user->id, Auth::id());

        // ログアウトのリクエストを送信
        $response = $this->post('/logout');

        // ログアウトしたか確認
        $this->assertFalse(Auth::check());

        // リダイレクト先が正しいか
        $response->assertRedirect('/');
    }
}
