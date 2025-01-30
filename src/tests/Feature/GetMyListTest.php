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
        // ユーザー1（出品者）を作成
        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
        ]);

        // テスト用の商品状態を作成
        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        // テスト用の商品を作成
        $item = Item::create([
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'これはテスト用の商品です。',
            'user_id' => $user1->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test.jpg'
        ]);

        // ユーザー2（いいねする人）を作成
        $user2 = User::factory()->create([
            'name' => 'テストユーザー2',
            'email' => 'test2@test.com',
            'password' => bcrypt('password'),
        ]);

        // favoritesテーブルのレコードを作成
        DB::table('favorites')->insert([
            'user_id' => $user2->id,
            'item_id' => $item->id,
        ]);

        // ユーザー2をログイン状態に設定
        $this->actingAs($user2);

        // マイリストページへアクセス
        $response = $this->get(route('item.index', ['page' => 'mylist']));
        $response->assertStatus(200);

        // ログインユーザーがいいねした商品が表示されていることを確認
        $response->assertSee($item->name);
    }

    public function test_sold_item_is_marked()
    {
        // ユーザー1（出品者）を作成
        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
        ]);

        // テスト用の商品状態を作成
        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        // テスト用の商品を作成
        $item = Item::create([
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'これはテスト用の商品です。',
            'user_id' => $user1->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test.jpg'
        ]);

        // ユーザー2（購入者）を作成
        $user2 = User::factory()->create([
            'name' => 'テストユーザー2',
            'email' => 'test2@test.com',
            'password' => bcrypt('password'),
        ]);

        // テスト用の送付先を作成
        $address = Address::create([
            'name' => '送付先ユーザー',
            'post_code' => '123-4567',
            'address' => 'テスト県テスト区テスト1-1-1',
            'building' => 'テストマンション'
        ]);

        // 購入データの作成
        $purchase = Purchase::create([
            'user_id' => $user2->id,
            'item_id' => $item->id,
            'address_id' => $address->id,
            'payment_method' => 1
        ]);

        // favoritesテーブルのレコードを作成
        DB::table('favorites')->insert([
            'user_id' => $user2->id,
            'item_id' => $item->id,
        ]);

        // ユーザーをログイン状態に設定
        $this->actingAs($user2);

        // マイリストページへアクセス
        $response = $this->get(route('item.index', ['page' => 'mylist']));
        $response->assertStatus(200);

        // 購入済み商品が「Sold」と表示されることを確認
        $response->assertSee('SOLD');
    }

    public function test_my_sell_product_not_display()
    {
        // ユーザー（出品者）を作成
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
        ]);

        // テスト用の商品状態を作成
        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        // テスト用の商品を作成
        $item = Item::create([
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'これはテスト用の商品です。',
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test.jpg'
        ]);

        // favoritesテーブルのレコードを作成
        DB::table('favorites')->insert([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // ユーザーをログイン状態に設定
        $this->actingAs($user);

        // マイリストページへアクセス
        $response = $this->get(route('item.index', ['page' => 'mylist']));
        $response->assertStatus(200);

        // 出品した商品名がレスポンスに含まれていないことを確認
        $response->assertDontSee($item->name);
    }

    public function test_not_authenticated_is_not_display()
    {
        // ログアウトのリクエストを送信
        $response = $this->post('/logout');

        // ログアウトしたか確認
        $this->assertFalse(Auth::check());

        // マイリストページへアクセス
        $response = $this->get(route('item.index', ['page' => 'mylist']));
        $response->assertStatus(200);

        // マイリストにメッセージが表示されていることを確認
        $response->assertSee('該当する商品が見つかりませんでした。');
        // 商品が表示されていないことを確認
        $response->assertDontSee('<div class= "item-card">', false);
    }
}
