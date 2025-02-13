<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class EditUserInformationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_information_initial_values_are_correct()
    {
        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
            'image' => 'profile-img/test_user1.jpg',
            'post_code' => '111-1111',
            'address' => '東京都テスト区テスト1-1-1',
            'building' => 'サンプルビル101',
        ]);

        $this->actingAs($user1);

        $response = $this->get(route('user.edit'));
        $response->assertStatus(200);

        $response->assertSee('テストユーザー1');
        $response->assertSee('111-1111');
        $response->assertSee('東京都テスト区テスト1-1-1');
        $response->assertSee('サンプルビル101');
        $response->assertSee(asset('storage/profile-img/test_user1.jpg'));
    }
}
