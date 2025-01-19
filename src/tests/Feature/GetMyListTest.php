<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class GetMyListTest extends TestCase
{
    // テスト実行前に以下を実行する必要あり
    // php artisan migrate:fresh --env=testing
    // php artisan db:seed --env=testing

    public function test_favorite_items_is_show()
    {
        // user_id=1のユーザーを取得
        $user = User::find(1);

        // item_id=10の商品のデータを取得
        $item = Item::find(10);

        // ユーザーをログイン状態に設定
        $this->actingAs($user);

        // favoritesテーブルのレコードを作成
        DB::table('favorites')->insert([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // マイリストページへアクセス
        $response = $this->get('/?page=mylist');
        $response->assertStatus(200);

        // ログインユーザーがいいねした商品が表示されていることを確認
        $response->assertSee($item->name);
    }

    public function test_sold_item_is_marked()
    {
        // user_id=1のユーザーを取得
        $user = User::find(1);

        // item_id=10の商品のデータを取得
        $item = Item::find(3);

        // ユーザーをログイン状態に設定
        $this->actingAs($user);

        // favoritesテーブルのレコードを作成
        DB::table('favorites')->insert([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // マイリストページへアクセス
        $response = $this->get('/?page=mylist');
        $response->assertStatus(200);

        // 購入済み商品が「Sold」と表示されることを確認
        $response->assertSee('SOLD');
    }

    public function test_my_sell_product_not_display()
    {
        // user_id=1のユーザーを取得
        $user = User::find(1);

        // ユーザーをログイン状態に設定
        $this->actingAs($user);

        // user_id=1に紐づく商品のデータを取得
        $userItems = Item::where('user_id', 1)->get();

        // マイリストページへアクセス
        $response = $this->get('/?page=mylist');
        $response->assertStatus(200);

        // user_id=1の商品名がレスポンスに含まれていないことを確認
        foreach ($userItems as $item) {
            $response->assertDontSee($item->name);

        }
    }

    public function test_not_authenticated_is_not_display()
    {
        // ログアウトのリクエストを送信
        $response = $this->post('/logout');

        // ログアウトしたか確認
        $this->assertFalse(Auth::check());

        // マイリストページへアクセス
        $response = $this->get('/?page=mylist');
        $response->assertStatus(200);

        // マイリストにメッセージが表示されていることを確認
        $response->assertSee('該当する商品が見つかりませんでした。');
        // 商品が表示されていないことを確認
        $response->assertDontSee('<div class= "item-card">', false);
    }
}
