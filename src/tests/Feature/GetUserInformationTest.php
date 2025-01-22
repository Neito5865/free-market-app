<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Condition;
use App\Models\Item;
use App\Models\Address;
use App\Models\Purchase;

class GetUserInformationTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_user_information()
    {
        // テストユーザー1を作成
        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
            'image' => 'profile-img/test_user1.jpg'
        ]);

        // テストユーザー2を作成
        $user2 = User::factory()->create([
            'name' => 'テストユーザー2',
            'email' => 'test2@test.com',
            'password' => bcrypt('password'),
            'image' => 'profile-img/test_user2.jpg'
        ]);

        // テスト用の商品状態を作成
        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        // ユーザー1が出品する商品1を作成
        $sellItem = Item::create([
            'name' => '出品用テスト商品',
            'price' => 1000,
            'description' => 'これはテスト用の商品です。',
            'user_id' => $user1->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test1.jpg'
        ]);

        // ユーザー2が出品する商品2を作成
        $buyItem = Item::create([
            'name' => '購入用テスト商品',
            'price' => 1000,
            'description' => 'これはテスト用の商品です。',
            'user_id' => $user2->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test2.jpg'
        ]);

        // ユーザー1が購入時の送付先データを作成
        $address = Address::create([
            'name' => '送付先ユーザー',
            'post_code' => '111-1111',
            'address' => '東京都テスト区テスト1-1-1',
            'building' => 'テストマンション',
        ]);

        // ユーザー1が商品2を購入
        Purchase::create([
            'user_id' => $user1->id,
            'item_id' => $buyItem->id,
            'address_id' => $address->id,
            'payment_method' => 1,
        ]);

        // ユーザー1をログイン状態に設定
        $user1 = $user1->fresh();
        $this->actingAs($user1, 'web');

        // マイページを開く
        $response = $this->get(route('user.show'));
        $response->assertStatus(200);

        // プロフィール情報が表示されているか確認
        $expectedStyle = "background-image: url('" . asset('storage/' . $user1->image) . "');";
        $response->assertSee($expectedStyle, false);
        $response->assertSee($user1->name);

        // 出品した商品が表示されているか確認
        $response->assertSee('<img src="' . asset('storage/' . $sellItem->image) . '"', false);
        $response->assertSee($sellItem->name);

        // 購入した商品タブを開く
        $response = $this->get(route('user.show') . '?tab=buy');
        $response->assertStatus(200);

        // 購入した商品が表示されているか確認
        $response->assertSee('<img src="' . asset('storage/' . $buyItem->image) . '"', false);
        $response->assertSee($buyItem->name);
    }
}
