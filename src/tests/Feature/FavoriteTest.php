<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use Illuminate\Support\Facades\DB;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_favorite()
    {
        // テストユーザー1を作成
        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
        ]);

        // テストユーザー2を作成
        $user2 = User::factory()->create([
            'name' => 'テストユーザー2',
            'email' => 'test2@test.com',
            'password' => bcrypt('password'),
        ]);

        // テスト用の商品状態を作成
        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        // ユーザー2の出品商品を作成
        $item = Item::create([
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'これはテスト用の商品です。',
            'user_id' => $user2->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test.jpg'
        ]);

        // ユーザー1をログイン状態に設定する
        $this->actingAs($user1);

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
        // テストユーザー1を作成
        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
        ]);

        // テストユーザー2を作成
        $user2 = User::factory()->create([
            'name' => 'テストユーザー2',
            'email' => 'test2@test.com',
            'password' => bcrypt('password'),
        ]);

        // テスト用の商品状態を作成
        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        // ユーザー2の出品商品を作成
        $item = Item::create([
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'これはテスト用の商品です。',
            'user_id' => $user2->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test.jpg'
        ]);

        // ユーザー1をログイン状態に設定する
        $this->actingAs($user1);

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
        // テストユーザー1を作成
        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
        ]);

        // テストユーザー2を作成
        $user2 = User::factory()->create([
            'name' => 'テストユーザー2',
            'email' => 'test2@test.com',
            'password' => bcrypt('password'),
        ]);

        // テスト用の商品状態を作成
        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        // ユーザー2の出品商品を作成
        $item = Item::create([
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'これはテスト用の商品です。',
            'user_id' => $user2->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test.jpg'
        ]);

        // いいねを登録済みの状態にする
        DB::table('favorites')->insert([
            'user_id' => $user1->id,
            'item_id' => $item->id
        ]);

        // ユーザー1をログイン状態に設定する
        $this->actingAs($user1);

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
