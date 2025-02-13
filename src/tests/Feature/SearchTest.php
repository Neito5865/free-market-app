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
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
        ]);

        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        $matchingItem = \App\Models\Item::create([
            'name' => '検索対象商品A',
            'price' => 1000,
            'description' => 'これは検索対象の商品です。',
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'image' => 'item-img/test.jpg'
        ]);

        $response = $this->get(route('item.index', ['keyword' => '検索対象']));

        $response->assertStatus(200);

        $response->assertSee($matchingItem->name);
    }

    public function test_search_state_is_kept_in_mylist()
    {
        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
        ]);

        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        $matchingItem = \App\Models\Item::create([
            'name' => '検索対象商品A',
            'price' => 1000,
            'description' => 'これは検索対象の商品です。',
            'user_id' => $user1->id,
            'condition_id' => $condition->id,
            'image' => 'item-img/test.jpg'
        ]);

        $user2 = User::factory()->create([
            'name' => 'テストユーザー2',
            'email' => 'test2@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($user2);

        DB::table('favorites')->insert([
            'user_id' => $user2->id,
            'item_id' => $matchingItem->id,
        ]);

        $response = $this->get(route('item.index', ['keyword' => '検索対象']));
        $response->assertStatus(200);

        $response->assertSee($matchingItem->name);

        $response = $this->get(route('item.index', ['page' => 'mylist']));
        $response->assertStatus(200);

        $response->assertSee($matchingItem->name);
    }
}
