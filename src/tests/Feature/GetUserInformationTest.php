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
        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
            'image' => 'profile-img/test_user1.jpg'
        ]);

        $user2 = User::factory()->create([
            'name' => 'テストユーザー2',
            'email' => 'test2@test.com',
            'password' => bcrypt('password'),
            'image' => 'profile-img/test_user2.jpg'
        ]);

        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        $sellItem = Item::create([
            'name' => '出品用テスト商品',
            'price' => 1000,
            'description' => 'これはテスト用の商品です。',
            'user_id' => $user1->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test1.jpg'
        ]);

        $buyItem = Item::create([
            'name' => '購入用テスト商品',
            'price' => 1000,
            'description' => 'これはテスト用の商品です。',
            'user_id' => $user2->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test2.jpg'
        ]);

        $address = Address::create([
            'name' => '送付先ユーザー',
            'post_code' => '111-1111',
            'address' => '東京都テスト区テスト1-1-1',
            'building' => 'テストマンション',
        ]);

        Purchase::create([
            'user_id' => $user1->id,
            'item_id' => $buyItem->id,
            'address_id' => $address->id,
            'payment_method' => 1,
        ]);

        $user1 = $user1->fresh();
        $this->actingAs($user1, 'web');

        $response = $this->get(route('user.show'));
        $response->assertStatus(200);

        $expectedStyle = "background-image: url('" . asset('storage/' . $user1->image) . "');";
        $response->assertSee($expectedStyle, false);
        $response->assertSee($user1->name);

        $response->assertSee('<img src="' . asset('storage/' . $sellItem->image) . '"', false);
        $response->assertSee($sellItem->name);

        $response = $this->get(route('user.show') . '?tab=buy');
        $response->assertStatus(200);

        $response->assertSee('<img src="' . asset('storage/' . $buyItem->image) . '"', false);
        $response->assertSee($buyItem->name);
    }
}
