<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\Condition;
use App\Models\Item;
use App\Models\User;
use App\Models\Comment;
use App\Models\Category;

class GetItemDetailTest extends TestCase
{
    public function test_all_information_of_item()
    {
        // テスト用の商品状態を作成
        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        // テスト用の商品を作成
        $item = Item::create([
            'name' => '検索対象商品A',
            'price' => 1000,
            'description' => 'これはテスト用の商品です。',
            'user_id' => 1,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test.jpg'
        ]);

        // テスト用のいいねレコードを作成
        DB::table('favorites')->insert([
            'user_id' => 2,
            'item_id' => $item->id
        ]);
        // コメントするユーザーを作成
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'image' => 'profile-img/test_user.jpg'
        ]);

        // テスト用のコメントを作成
        $comment = Comment::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'テストコメント'
        ]);

        // テスト用のカテゴリーを作成
        $category1 = Category::create([
            'category' => 'テストカテゴリー1',
        ]);
        $category2 = Category::create([
            'category' => 'テストカテゴリー2',
        ]);

        // テスト用のitem_categoryレコードを作成
        DB::table('item_category')->insert([
            [
                'item_id' => $item->id,
                'category_id' => $category1->id,
            ],
            [
                'item_id' => $item->id,
                'category_id' => $category2->id,
            ],
        ]);

        // 商品詳細ページへアクセス
        $response = $this->get(route('item.show', $item->id));
        $response->assertStatus(200);

        // 商品の情報が表示されているか確認
        $response->assertSee('<img src="' . asset('storage/' . $item->image) . '"', false); // 商品画像
        $response->assertSee($item->name); // 商品名
        $response->assertSee($item->brand); // ブランド名
        $response->assertSee($item->formatted_price); // 価格
        $response->assertSee($item->description); // 商品説明
        $response->assertSee($condition->condition); // 商品の状態
        $response->assertSee($comment->user->name); // コメントしたユーザー名

        // コメントしたユーザー画像
        $expectedStyle = "background-image: url('" . asset('storage/' . $user->image) . "');";
        $response->assertSee($expectedStyle, false);

        $response->assertSee($comment->comment); // コメント内容

        // いいね数が表示されているか確認
        $likeCount = $item->favoriteUsers()->count();
        $response->assertSee((string) $likeCount);

        // コメント数が表示されているか確認
        $commentCount = $item->comments()->count();
        $response->assertSee((string) $commentCount);

        $response->assertSee($category1->category); // カテゴリ
        $response->assertSee($category2->category); // カテゴリ
    }
}
