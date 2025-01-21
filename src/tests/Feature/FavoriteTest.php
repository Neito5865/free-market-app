<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class FavoriteTest extends TestCase
{
    // テスト実行前に以下を実行する必要あり
    // php artisan migrate:fresh --env=testing
    // php artisan db:seed --env=testing

    public function test_favorite()
    {
        // user_id=1のユーザーを取得
        $user = User::find(1);

        // item_id=5の商品を取得
        $item = Item::find(5);

        // ログイン状態に設定する
        $this->actingAs($user);

        // 商品詳細ページへアクセス
        $response = $this->get(route('item.show', $item->id));
        $response->assertStatus(200);

        // いいねを押下する前の状態を確認
        $initialFavoriteCount = DB::table('favorites')
            ->where('item_id', $item->id)
            ->count();
        $this->assertEquals(0, $initialFavoriteCount); // いいね数が0であることを確認

        // いいね登録リクエストを送信
        $response = $this->post(route('favorite', $item->id));

        // いいねを押下した後の状態を確認
        $updatedFavoriteCount = DB::table('favorites')
            ->where('item_id', $item->id)
            ->count();
        $this->assertEquals(1, $updatedFavoriteCount);

        // 商品詳細ページへアクセス
        $response = $this->get(route('item.show', $item->id));
        // いいね数がいいね登録後の状態になっているか
        $response->assertSee((string) $updatedFavoriteCount);
    }

    public function test_favorite_icon_changes_when_favorited()
    {
        // user_id=1のユーザーを取得
        $user = User::find(2);

        // item_id=5の商品を取得
        $item = Item::find(6);

        // ログイン状態に設定する
        $this->actingAs($user);

        // 商品詳細ページへアクセス
        $response = $this->get(route('item.show', $item->id));
        $response->assertStatus(200);

        // いいね前のアイコンのクラスを確認
        $response->assertSee('favorite-icon__form-btn'); // クラスが存在するか
        $response->assertDontSee('favorited'); // いいね済みクラスがないことを確認

        // いいねリクエストを送信
        $response = $this->post(route('favorite', $item->id));

        // 商品詳細ページへアクセス
        $response = $this->get(route('item.show', $item->id));

        // いいね後のアイコンのクラスを確認
        $response->assertSee('favorite-icon__form-btn favorited');
    }

    public function test_unfavorite()
    {
        // user_id=1のユーザーを取得
        $user = User::find(1);

        // item_id=5の商品を取得
        $item = Item::find(5);

        // ログイン状態に設定する
        $this->actingAs($user);

        // 商品詳細ページへアクセス
        $response = $this->get(route('item.show', $item->id));
        $response->assertStatus(200);

        // いいねを解除する前の状態を確認
        $initialFavoriteCount = DB::table('favorites')
            ->where('item_id', $item->id)
            ->count();
        $this->assertEquals(1, $initialFavoriteCount);

        // いいね解除リクエストを送信
        $response = $this->delete(route('unfavorite', $item->id));

        // いいねを解除した後の状態を確認
        $updatedFavoriteCount = DB::table('favorites')
            ->where('item_id', $item->id)
            ->count();
        $this->assertEquals(0, $updatedFavoriteCount);

        // 商品詳細ページへアクセス
        $response = $this->get(route('item.show', $item->id));
        // いいね数がいいね登録後の状態になっているか
        $response->assertSee((string) $updatedFavoriteCount);
    }
}
