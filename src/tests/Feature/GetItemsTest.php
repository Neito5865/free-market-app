<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Address;
use App\Models\Purchase;

class GetItemsTest extends TestCase
{
    // テスト実行前に以下を実行する必要あり
    // php artisan migrate:fresh --env=testing
    // php artisan db:seed --env=testing

    public function test_get_all_items()
    {
        // 商品一覧ページへアクセス
        $response = $this->get('/');
        $response->assertStatus(200);

        // 商品データがレスポンスに含まれることを確認
        $items = Item::all();
        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }

    public function test_purchased_items_are_marked_as_sold()
    {
        // 商品一覧ページにアクセス
        $response = $this->get('/');
        $response->assertStatus(200);

        // 購入済み商品が「Sold」と表示されることを確認
        $response->assertSee('SOLD');
    }

    public function test_my_sell_product_is_not_display()
    {
        // シーダーデータにデータがあるか
        $this->assertDatabaseHas('users', [
            'id' => 1,
            'email' => 'user1@example.com',
        ]);

        // user_id=1に紐づく商品のデータを取得
        $userItems = Item::where('user_id', 1)->get();

        // シーダーデータのユーザーを取得
        $user = User::find(1);

        // ユーザーをログイン状態に設定
        $this->actingAs($user);

        // ログイン後にログイン中のユーザーを確認
        $this->assertEquals(auth()->id(), 1);

        // 商品一覧ページにアクセス
        $response = $this->get('/');
        $response->assertStatus(200);

        // user_id=1の商品名がレスポンスに含まれていないことを確認
        foreach ($userItems as $item) {
            $response->assertDontSee($item->name);
        }
    }
}
