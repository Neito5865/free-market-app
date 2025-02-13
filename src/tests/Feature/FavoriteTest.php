<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use Illuminate\Support\Facades\DB;
use App\Http\Middleware\VerifyCsrfToken;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_favorite()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
        ]);

        $user2 = User::factory()->create([
            'name' => 'テストユーザー2',
            'email' => 'test2@test.com',
            'password' => bcrypt('password'),
        ]);

        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        $item = Item::create([
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'これはテスト用の商品です。',
            'user_id' => $user2->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test.jpg'
        ]);

        $this->actingAs($user1);

        $response = $this->get(route('item.show', $item->id));
        $response->assertStatus(200);

        $initialFavoriteCount = DB::table('favorites')
            ->where('item_id', $item->id)
            ->count();
        $this->assertEquals(0, $initialFavoriteCount);

        $response = $this->post(route('favorite', $item->id));

        $updatedFavoriteCount = DB::table('favorites')
            ->where('item_id', $item->id)
            ->count();
        $this->assertEquals(1, $updatedFavoriteCount);

        $response = $this->get(route('item.show', $item->id));
        $response->assertSee((string) $updatedFavoriteCount);
    }

    public function test_favorite_icon_changes_when_favorited()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
        ]);

        $user2 = User::factory()->create([
            'name' => 'テストユーザー2',
            'email' => 'test2@test.com',
            'password' => bcrypt('password'),
        ]);

        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        $item = Item::create([
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'これはテスト用の商品です。',
            'user_id' => $user2->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test.jpg'
        ]);

        $this->actingAs($user1);

        $response = $this->get(route('item.show', $item->id));
        $response->assertStatus(200);

        $response->assertSee('favorite-icon__form-btn');
        $response->assertDontSee('favorited');

        $response = $this->post(route('favorite', $item->id));
        $response = $this->get(route('item.show', $item->id));
        $response->assertSee('favorite-icon__form-btn favorited');
    }

    public function test_unfavorite()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
        ]);

        $user2 = User::factory()->create([
            'name' => 'テストユーザー2',
            'email' => 'test2@test.com',
            'password' => bcrypt('password'),
        ]);

        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        $item = Item::create([
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'これはテスト用の商品です。',
            'user_id' => $user2->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test.jpg'
        ]);

        DB::table('favorites')->insert([
            'user_id' => $user1->id,
            'item_id' => $item->id
        ]);

        $this->actingAs($user1);

        $response = $this->get(route('item.show', $item->id));
        $response->assertStatus(200);

        $initialFavoriteCount = DB::table('favorites')
            ->where('item_id', $item->id)
            ->count();
        $this->assertEquals(1, $initialFavoriteCount);

        $response = $this->delete(route('unfavorite', $item->id));

        $updatedFavoriteCount = DB::table('favorites')
            ->where('item_id', $item->id)
            ->count();
        $this->assertEquals(0, $updatedFavoriteCount);

        $response = $this->get(route('item.show', $item->id));
        $response->assertSee((string) $updatedFavoriteCount);
    }
}
