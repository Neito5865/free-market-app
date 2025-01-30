<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Condition;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_by_keyword()
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
        $matchingItem = \App\Models\Item::create([
            'name' => '検索対象商品A',
            'price' => 1000,
            'description' => 'これは検索対象の商品です。',
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'image' => 'item-img/test.jpg'
        ]);

        // 検索キーワードを含むGETリクエストを送信
        $response = $this->get(route('item.index', ['keyword' => '検索対象']));

        // ステータスコードが200であることを確認
        $response->assertStatus(200);

        // 検索結果にマッチした商品が表示されていることを確認
        $response->assertSee($matchingItem->name);
    }

    public function test_search_state_is_kept_in_mylist()
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
        $matchingItem = \App\Models\Item::create([
            'name' => '検索対象商品A',
            'price' => 1000,
            'description' => 'これは検索対象の商品です。',
            'user_id' => $user1->id,
            'condition_id' => $condition->id,
            'image' => 'item-img/test.jpg'
        ]);

        // ユーザー2（いいねする人）を作成
        $user2 = User::factory()->create([
            'name' => 'テストユーザー2',
            'email' => 'test2@test.com',
            'password' => bcrypt('password'),
        ]);

        // ユーザー2をログイン状態に設定
        $this->actingAs($user2);

        // favoritesテーブルのレコードを作成
        DB::table('favorites')->insert([
            'user_id' => $user2->id,
            'item_id' => $matchingItem->id,
        ]);

        // 検索キーワードを含むGETリクエストを送信
        $response = $this->get(route('item.index', ['keyword' => '検索対象']));
        $response->assertStatus(200);

        // 検索結果にマッチした商品が表示されていることを確認
        $response->assertSee($matchingItem->name);

        // マイリストページへアクセス
        $response = $this->get(route('item.index', ['page' => 'mylist']));
        $response->assertStatus(200);

        // マイリストにも検索結果が保持されていることを確認
        $response->assertSee($matchingItem->name);
    }
}
