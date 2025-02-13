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
    use RefreshDatabase;

    public function test_all_information_of_item()
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
            'image' => 'profile-img/test_user2.jpg'
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

        DB::table('favorites')->insert([
            'user_id' => $user2->id,
            'item_id' => $item->id
        ]);

        $comment = Comment::create([
            'user_id' => $user2->id,
            'item_id' => $item->id,
            'comment' => 'テストコメント'
        ]);

        $category1 = Category::create([
            'category' => 'テストカテゴリー1',
        ]);
        $category2 = Category::create([
            'category' => 'テストカテゴリー2',
        ]);

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

        $response = $this->get(route('item.show', $item->id));
        $response->assertStatus(200);

        $response->assertSee('<img src="' . asset('storage/' . $item->image) . '"', false);
        $response->assertSee($item->name);
        $response->assertSee($item->brand);
        $response->assertSee($item->formatted_price);
        $response->assertSee($item->description);
        $response->assertSee($condition->condition);
        $response->assertSee($comment->user->name);

        $expectedStyle = "background-image: url('" . asset('storage/' . $user2->image) . "');";
        $response->assertSee($expectedStyle, false);

        $response->assertSee($comment->comment);

        $likeCount = $item->favoriteUsers()->count();
        $response->assertSee((string) $likeCount);

        $commentCount = $item->comments()->count();
        $response->assertSee((string) $commentCount);
        $response->assertSee($category1->category);
        $response->assertSee($category2->category);
    }
}
