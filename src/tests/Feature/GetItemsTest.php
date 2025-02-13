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

class GetItemsTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_all_items()
    {
        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
        ]);

        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        $item1 = Item::create([
            'name' => 'テスト商品A',
            'price' => 1000,
            'description' => 'これはテスト用の商品Aです。',
            'user_id' => $user1->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test1.jpg'
        ]);
        $item2 = Item::create([
            'name' => 'テスト商品B',
            'price' => 2000,
            'description' => 'これはテスト用の商品Bです。',
            'user_id' => $user1->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test2.jpg'
        ]);
        $item3 = Item::create([
            'name' => 'テスト商品C',
            'price' => 3000,
            'description' => 'これはテスト用の商品Cです。',
            'user_id' => $user1->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test3.jpg'
        ]);

        $response = $this->get(route('item.index'));
        $response->assertStatus(200);

        $response->assertSee($item1->name);
        $response->assertSee($item2->name);
        $response->assertSee($item3->name);
    }

    public function test_purchased_items_are_marked_as_sold()
    {
        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
        ]);

        $user2 = User::factory()->create([
            'name' => 'テストユーザー2',
            'email' => 'test2@test.com',
            'password' => bcrypt('password'),
        ]);

        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        $item = Item::create([
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'これはテスト用の商品です。',
            'user_id' => $user1->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test.jpg'
        ]);

        $address = Address::create([
            'name' => '送付先ユーザー',
            'post_code' => '123-4567',
            'address' => 'テスト県テスト区テスト1-1-1',
            'building' => 'テストマンション'
        ]);

        $purchase = Purchase::create([
            'user_id' => $user2->id,
            'item_id' => $item->id,
            'address_id' => $address->id,
            'payment_method' => 1
        ]);

        $response = $this->get(route('item.index'));
        $response->assertStatus(200);
        $response->assertSee('SOLD');
    }

    public function test_my_sell_product_is_not_display()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
        ]);

        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        $item = Item::create([
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'これはテスト用の商品です。',
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test.jpg'
        ]);

        $this->actingAs($user);

        $response = $this->get(route('item.index'));
        $response->assertStatus(200);

        $response->assertDontSee($item->name);
    }
}
