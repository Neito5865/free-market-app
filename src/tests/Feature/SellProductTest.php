<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use App\Http\Middleware\VerifyCsrfToken;

class SellProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_can_be_created_successfully()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($user);

        $condition = Condition::create(['condition' => 'テストコンディション']);

        $category1 = Category::create(['category' => 'テストカテゴリー1']);
        $category2 = Category::create(['category' => 'テストカテゴリー2']);
        $category3 = Category::create(['category' => 'テストカテゴリー3']);

        $response = $this->get(route('item.create'));
        $response->assertStatus(200);

        $data = [
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'これはテスト商品です。',
            'categories' => [$category1->id, $category2->id, $category3->id],
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => UploadedFile::fake()->image('test-item.jpg'),
        ];
        $response = $this->post(route('item.store'), $data);

        $response->assertStatus(302);
        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'これはテスト商品です。',
            'brand' => 'テストブランド',
            'condition_id' => $condition->id,
        ]);

        $item = Item::where('name', 'テスト商品')->first();

        $this->assertDatabaseHas('item_category', [
            'item_id' => $item->id,
            'category_id' => $category1->id,
        ]);
        $this->assertDatabaseHas('item_category', [
            'item_id' => $item->id,
            'category_id' => $category2->id,
        ]);
        $this->assertDatabaseHas('item_category', [
            'item_id' => $item->id,
            'category_id' => $category3->id,
        ]);
    }
}
