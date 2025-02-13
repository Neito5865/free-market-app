<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;
use App\Models\Condition;
use App\Models\Item;
use App\Models\Address;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;

class GetMyListTest extends TestCase
{
    use RefreshDatabase;

    public function test_favorite_items_is_show()
    {
        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
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

        $user2 = User::factory()->create([
            'name' => 'テストユーザー2',
            'email' => 'test2@test.com',
            'password' => bcrypt('password'),
        ]);

        DB::table('favorites')->insert([
            'user_id' => $user2->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs($user2);

        $response = $this->get(route('item.index', ['page' => 'mylist']));
        $response->assertStatus(200);

        $response->assertSee($item->name);
    }

    public function test_sold_item_is_marked()
    {
        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
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

        $user2 = User::factory()->create([
            'name' => 'テストユーザー2',
            'email' => 'test2@test.com',
            'password' => bcrypt('password'),
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

        DB::table('favorites')->insert([
            'user_id' => $user2->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs($user2);

        $response = $this->get(route('item.index', ['page' => 'mylist']));
        $response->assertStatus(200);

        $response->assertSee('SOLD');
    }

    public function test_my_sell_product_not_display()
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

        DB::table('favorites')->insert([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('item.index', ['page' => 'mylist']));
        $response->assertStatus(200);

        $response->assertDontSee($item->name);
    }

    public function test_not_authenticated_is_not_display()
    {
        $response = $this->post('/logout');

        $this->assertFalse(Auth::check());

        $response = $this->get(route('item.index', ['page' => 'mylist']));
        $response->assertStatus(200);

        $response->assertSee('該当する商品が見つかりませんでした。');
        $response->assertDontSee('<div class= "item-card">', false);
    }
}
