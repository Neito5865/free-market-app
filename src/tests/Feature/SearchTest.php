<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SearchTest extends TestCase
{
    public function test_search_by_keyword()
    {
        // テスト用の商品を作成
        $matchingItem = \App\Models\Item::create([
            'name' => '検索対象商品A',
            'price' => 1000,
            'description' => 'これは検索対象の商品です。',
            'user_id' => 1,
            'condition_id' => 1,
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
        // テスト用の商品を作成
        $matchingItem = \App\Models\Item::create([
            'name' => '検索対象商品A',
            'price' => 1000,
            'description' => 'これは検索対象の商品です。',
            'user_id' => 1,
            'condition_id' => 1,
            'image' => 'item-img/test.jpg'
        ]);

        // user_id=2のユーザーを取得
        $user = User::find(2);

        // ユーザーをログイン状態に設定
        $this->actingAs($user);

        // favoritesテーブルのレコードを作成
        DB::table('favorites')->insert([
            'user_id' => $user->id,
            'item_id' => $matchingItem->id,
        ]);

        // 検索キーワードを含むGETリクエストを送信
        $response = $this->get(route('item.index', ['keyword' => '検索対象']));
        $response->assertStatus(200);

        // 検索結果にマッチした商品が表示されていることを確認
        $response->assertSee($matchingItem->name);

        // マイリストページへアクセス
        $response = $this->get('/?page=mylist');
        $response->assertStatus(200);

        // マイリストにも検索結果が保持されていることを確認
        $response->assertSee($matchingItem->name);
    }
}
